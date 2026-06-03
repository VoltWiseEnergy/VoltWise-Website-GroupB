<?php
namespace App\Services;

use App\Models\UsageLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ConsumptionAnalyzer
{
    protected int $userId;
    protected Collection $logs;

    public function __construct(int $userId, int $days = 30)
    {
        $this->userId = $userId;
        $this->logs   = UsageLog::where('user_id', $userId)
            ->where('usage_date', '>=', Carbon::today()->subDays($days - 1))
            ->get();
    }

    // ─────────────────────────────────────────
    // Entry point — return semua pattern
    // ─────────────────────────────────────────
    public function analyze(): array
    {
        $highUsage  = $this->detectHighUsageDevices();
        $alwaysOn   = $this->detectAlwaysOnDevices();
        $spikeDays  = $this->detectSpikeDays();

        return [
            'high_usage_devices'  => $highUsage,
            'always_on_devices'   => $alwaysOn,
            'spike_days'          => $spikeDays,
            'total_kwh'           => $this->totalKwh(),
            'daily_avg_kwh'       => $this->dailyAverageKwh(),
            'most_consuming'      => $this->mostConsumingDevice(),
            'recommendations'     => $this->generateRecommendations($highUsage, $alwaysOn, $spikeDays),
        ];
    }

    // ─────────────────────────────────────────
    // PBI #43 — Generate Recommendations
    // ─────────────────────────────────────────
    protected function generateRecommendations(
        Collection $highUsage,
        Collection $alwaysOn,
        Collection $spikeDays
    ): Collection {
        $recommendations = collect();

        // Rekomendasi untuk high usage devices
        foreach ($highUsage as $device) {
            $savingHours   = 2;
            $savingKwh     = round(($device['avg_kwh'] * $savingHours) / 24 * 30, 2);
            $savingRp      = number_format($savingKwh * 1444.7, 0, ',', '.');

            $recommendations->push([
                'type'     => 'high_usage',
                'priority' => 'high',
                'icon'     => 'zap',
                'title'    => "Reduce {$device['device_name']} usage",
                'message'  => "This device uses {$device['avg_kwh']} kWh/day on average — well above your other devices. Try reducing usage by {$savingHours} hours/day to potentially save ~Rp {$savingRp}/month.",
                'device'   => $device['device_name'],
            ]);
        }

        // Rekomendasi untuk always-on devices
        foreach ($alwaysOn as $device) {
            $recommendations->push([
                'type'     => 'always_on',
                'priority' => 'medium',
                'icon'     => 'clock',
                'title'    => "Check {$device['device_name']} schedule",
                'message'  => "{$device['device_name']} has been running {$device['avg_hours']} hours/day for {$device['days_active']} out of the last 7 days. Consider using a timer or unplugging when not in use.",
                'device'   => $device['device_name'],
            ]);
        }

        // Rekomendasi untuk spike days
        if ($spikeDays->isNotEmpty()) {
            $worstDay = $spikeDays->first();
            $date     = Carbon::parse($worstDay['date'])->format('d F Y');

            $recommendations->push([
                'type'     => 'spike',
                'priority' => 'medium',
                'icon'     => 'activity',
                'title'    => 'Unusual energy spike detected',
                'message'  => "On {$date}, your total usage was {$worstDay['total_kwh']} kWh — more than 2x your daily average. Review which devices were active that day to avoid repeat spikes.",
                'device'   => null,
            ]);
        }

        // Rekomendasi general kalau gak ada masalah
        if ($recommendations->isEmpty()) {
            $recommendations->push([
                'type'     => 'general',
                'priority' => 'low',
                'icon'     => 'check-circle',
                'title'    => 'Your usage looks healthy!',
                'message'  => 'No significant inefficiencies detected in the last 30 days. Keep monitoring your usage to maintain good energy habits.',
                'device'   => null,
            ]);
        }

        // Rekomendasi general tambahan
        $dailyAvg = $this->dailyAverageKwh();
        if ($dailyAvg > 10) {
            $recommendations->push([
                'type'     => 'general',
                'priority' => 'low',
                'icon'     => 'trending-down',
                'title'    => 'Overall consumption is high',
                'message'  => "Your daily average is {$dailyAvg} kWh. Indonesian households typically use 5-8 kWh/day. Consider an energy audit of all your devices.",
                'device'   => null,
            ]);
        }

        return $recommendations->sortBy(fn($r) => match($r['priority']) {
            'high'   => 1,
            'medium' => 2,
            'low'    => 3,
            default  => 4,
        })->values();
    }

    // Device dengan kWh rata-rata > 1.5x rata-rata semua device
    protected function detectHighUsageDevices(): Collection
    {
        $byDevice = $this->logs->groupBy('device_id')->map(function ($logs) {
            $avgKwh = $logs->avg(fn($l) => ($l->wattage * $l->hours) / 1000);
            return [
                'device_id'   => $logs->first()->device_id,
                'device_name' => $logs->first()->device_name,
                'avg_kwh'     => round($avgKwh, 3),
            ];
        })->values();

        if ($byDevice->isEmpty()) return collect();

        $globalAvg = $byDevice->avg('avg_kwh');

        return $byDevice->filter(fn($d) => $d['avg_kwh'] > $globalAvg * 1.5)
                        ->sortByDesc('avg_kwh')
                        ->values();
    }

    // Device yang aktif >= 6 hari dalam 7 hari terakhir dengan rata-rata > 8 jam
    protected function detectAlwaysOnDevices(): Collection
    {
        $last7 = $this->logs->filter(
            fn($l) => $l->usage_date->gte(Carbon::today()->subDays(6))
        );

        return $last7->groupBy('device_id')->filter(function ($logs) {
            return $logs->count() >= 6 && $logs->avg('hours') > 8;
        })->map(function ($logs) {
            return [
                'device_id'   => $logs->first()->device_id,
                'device_name' => $logs->first()->device_name,
                'days_active' => $logs->count(),
                'avg_hours'   => round($logs->avg('hours'), 1),
            ];
        })->values();
    }

    // Hari dengan total kWh > 2x rata-rata harian
    protected function detectSpikeDays(): Collection
    {
        $byDate = $this->logs->groupBy(fn($l) => $l->usage_date->toDateString())
            ->map(fn($logs) => [
                'date'      => $logs->first()->usage_date->toDateString(),
                'total_kwh' => round($logs->sum(fn($l) => ($l->wattage * $l->hours) / 1000), 3),
            ])->values();

        if ($byDate->isEmpty()) return collect();

        $avgDaily = $byDate->avg('total_kwh');

        return $byDate->filter(fn($d) => $d['total_kwh'] > $avgDaily * 2)
                      ->sortByDesc('total_kwh')
                      ->values();
    }

    protected function totalKwh(): float
    {
        return round($this->logs->sum(fn($l) => ($l->wattage * $l->hours) / 1000), 3);
    }

    protected function dailyAverageKwh(): float
    {
        $days = $this->logs->groupBy(fn($l) => $l->usage_date->toDateString())->count();
        return $days > 0 ? round($this->totalKwh() / $days, 3) : 0;
    }

    protected function mostConsumingDevice(): ?array
    {
        return $this->logs->groupBy('device_id')->map(function ($logs) {
            return [
                'device_name' => $logs->first()->device_name,
                'total_kwh'   => round($logs->sum(fn($l) => ($l->wattage * $l->hours) / 1000), 3),
            ];
        })->sortByDesc('total_kwh')->first();
    }
}