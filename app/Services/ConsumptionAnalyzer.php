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
        return [
            'high_usage_devices'  => $this->detectHighUsageDevices(),
            'always_on_devices'   => $this->detectAlwaysOnDevices(),
            'spike_days'          => $this->detectSpikeDays(),
            'total_kwh'           => $this->totalKwh(),
            'daily_avg_kwh'       => $this->dailyAverageKwh(),
            'most_consuming'      => $this->mostConsumingDevice(),
        ];
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