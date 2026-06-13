<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\UsageLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class AnalyticsController extends Controller
{
    /**
     * Display the Analytics page.
     * PBI-36: Top Consumer Analysis
     * PBI-37: Category Distribution
     * PBI-38: Peak Usage Heatmap
     */
    public function index()
    {
        $user   = Auth::user();
        $userId = $user->id;

        // ── Fetch today's usage logs ──────────────────────────────────────────
        $todayLogs = UsageLog::where('user_id', $userId)
            ->where('usage_date', Carbon::today()->toDateString())
            ->get()
            ->keyBy('device_id');

        // ── All user devices with today's dynamic kWh ─────────────────────────
        $devices = Device::where('user_id', $userId)->orderBy('name')->get();
        $devices->map(function ($device) use ($todayLogs) {
            $log = $todayLogs->get($device->id);
            $device->daily_energy_kwh = $log
                ? $log->kwh
                : round(($device->wattage / 1000) * 1, 3);
            $device->daily_cost = $device->daily_energy_kwh * 1444.7;
            return $device;
        });

        // ── PBI-36: Top Consumer Analysis ────────────────────────────────────
        // Top 10 devices sorted by daily kWh descending
        $topConsumers = $devices->sortByDesc('daily_energy_kwh')->take(10)->values();
        $maxKwh = $topConsumers->max('daily_energy_kwh') ?: 1;

        // ── PBI-37: Category Distribution ────────────────────────────────────
        $categoryData = $devices
            ->groupBy('category')
            ->map(fn($group) => round($group->sum('daily_energy_kwh'), 3))
            ->sortDesc();

        // ── PBI-38: Peak Usage Heatmap ────────────────────────────────────────
        // Last 7 days of usage logs to build a day × hour matrix
        // Since logs store daily totals (not hourly), we distribute wattage
        // across typical usage hours to approximate a heatmap.
        // hours column = daily usage hours logged per device
        $last7Days = UsageLog::where('user_id', $userId)
            ->where('usage_date', '>=', Carbon::today()->subDays(6)->toDateString())
            ->orderBy('usage_date')
            ->get();

        // Build a 7-day × 24-hour matrix
        // We model peak usage by distributing watt-hours into the most common
        // active hours for each usage record using a simple sine-curve weight.
        $days   = [];
        $matrix = [];   // $matrix[$dayIndex][$hour] = kWh

        for ($d = 6; $d >= 0; $d--) {
            $date       = Carbon::today()->subDays($d)->toDateString();
            $days[]     = Carbon::today()->subDays($d)->format('D, d M');
            $matrix[]   = array_fill(0, 24, 0.0);
        }

        foreach ($last7Days as $log) {
            $logDate  = Carbon::parse($log->usage_date)->toDateString();
            $dayIndex = Carbon::parse($log->usage_date)
                ->diffInDays(Carbon::today()->subDays(6));

            if ($dayIndex < 0 || $dayIndex > 6) continue;

            $kwh   = $log->kwh;
            $hours = (float) $log->hours;

            // Distribute across active hours proportional to a daytime bell curve
            // Peak around 18:00 (evening peak), secondary around 8:00 (morning)
            $activeHours   = [];
            $activeWeights = [];
            $totalWeight   = 0;

            for ($h = 0; $h < 24; $h++) {
                // Two peaks: 08:00 and 18:00 — typical household patterns
                $morningW  = exp(-0.5 * pow(($h - 8)  / 3, 2));
                $eveningW  = exp(-0.5 * pow(($h - 18) / 3, 2));
                $weight    = $morningW + $eveningW;
                $activeWeights[$h] = $weight;
                $totalWeight      += $weight;
            }

            for ($h = 0; $h < 24; $h++) {
                $share = $totalWeight > 0 ? $activeWeights[$h] / $totalWeight : 0;
                $matrix[$dayIndex][$h] += round($kwh * $share, 4);
            }
        }

        // Flatten max for normalisation in the view
        $heatmapMax = 0;
        foreach ($matrix as $row) {
            $rowMax = max($row);
            if ($rowMax > $heatmapMax) $heatmapMax = $rowMax;
        }

        return view('analytics', compact(
            'devices',
            'topConsumers',
            'maxKwh',
            'categoryData',
            'days',
            'matrix',
            'heatmapMax'
        ));
    }
}
