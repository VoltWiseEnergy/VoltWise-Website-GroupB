<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\UsageLog;
use App\Services\PointService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(private PointService $pointService) {}

    /**
     * Display the user dashboard.
     */
    public function index()
    {
        $user   = Auth::user();
        $userId = $user->id;

        $devices = Device::where('user_id', $userId)->orderBy('name')->get();
        $totalDevices = $devices->count();

        // Fetch today's usage logs for the user
        $todayLogs = UsageLog::where('user_id', $userId)
            ->where('usage_date', Carbon::today()->toDateString())
            ->get()
            ->keyBy('device_id');

        // Map devices to add a daily_energy_kwh property dynamically
        $devices->map(function ($device) use ($todayLogs) {
            $log = $todayLogs->get($device->id);
            if ($log) {
                $device->daily_energy_kwh = $log->kwh;
            } else {
                // If no log exists for today, assume a default of 1 hour usage
                $device->daily_energy_kwh = round(($device->wattage / 1000) * 1, 3);
            }

            // Calculate tariff (cost) based on daily kWh × Rp 1444.7
            $device->tariff = $device->daily_energy_kwh * 1444.7;

            return $device;
        });

        $todayEnergyKwh = $devices->sum('daily_energy_kwh');
        $todayCost      = $devices->sum('tariff');
        $topConsumer    = $devices->sortByDesc('daily_energy_kwh')->first();
        $energyByCategory = $devices->groupBy('category')->map(fn ($group) => $group->sum('daily_energy_kwh'));

        // Monthly cost estimation: today's total cost × 30 days
        $monthlyCost = $todayCost * 30;

        // ── Points ──────────────────────────────────────────────────────────
        // Award daily points (idempotent — safe to call on every page load)
        $newlyAwarded = $this->pointService->awardDailyPoints($user);
        // Refresh user to get updated points total
        $user->refresh();
        $totalPoints  = $user->points ?? 0;
        $level        = $this->pointService->getLevel($totalPoints);
        $todayAwards  = $this->pointService->getTodayAwards($user);

        return view('dashboard', compact(
            'devices',
            'totalDevices',
            'todayEnergyKwh',
            'topConsumer',
            'energyByCategory',
            'todayCost',
            'monthlyCost',
            'totalPoints',
            'level',
            'todayAwards',
            'newlyAwarded'
        ));
    }

    /**
     * Return JSON for the 7-Day Energy Trend chart.
     * ?week=0  → current week (Mon–today)
     * ?week=1  → last week, … up to ?week=6
     */
    public function weeklyTrend(\Illuminate\Http\Request $request)
    {
        $weekOffset = max(0, min(6, (int) $request->query('week', 0)));
        $userId     = Auth::id();
        $colors     = ['#4A7CF6','#10b981','#8b5cf6','#f59e0b','#f87171','#06b6d4','#ec4899'];

        // Determine the Monday of the target week
        $monday = Carbon::now()->startOfWeek(Carbon::MONDAY)->subWeeks($weekOffset);
        $sunday = $monday->copy()->endOfWeek(Carbon::SUNDAY);

        // Build 7 date strings Mon → Sun
        $dates  = [];
        $labels = [];
        for ($d = 0; $d < 7; $d++) {
            $day      = $monday->copy()->addDays($d);
            $dates[]  = $day->toDateString();
            $labels[] = $day->format('D d/m');   // e.g. "Mon 02/06"
        }

        // Fetch all usage logs for those 7 days
        $logs = UsageLog::where('user_id', $userId)
            ->whereBetween('usage_date', [$dates[0], $dates[6]])
            ->get();

        // Fetch all devices for this user
        $devices = Device::where('user_id', $userId)->orderBy('name')->get();

        // Build datasets: one per device
        $datasets = [];
        foreach ($devices as $idx => $device) {
            $color = $colors[$idx % count($colors)];
            $data  = [];
            foreach ($dates as $date) {
                $log    = $logs->where('device_id', $device->id)->where('usage_date', $date)->first();
                $data[] = $log ? round($log->kwh, 3) : 0;
            }
            // Skip devices with zero usage across the whole week
            if (array_sum($data) == 0) continue;

            $datasets[] = [
                'label'           => $device->name,
                'data'            => $data,
                'borderColor'     => $color,
                'backgroundColor' => 'rgba(74,124,246,0.08)',
                'fill'            => $idx === 0,
                'tension'         => 0.4,
                'pointRadius'     => 4,
                'borderWidth'     => 2,
            ];
        }

        $weekLabel = $weekOffset === 0
            ? 'This week (' . $monday->format('d M') . ' – ' . $sunday->format('d M') . ')'
            : $monday->format('d M') . ' – ' . $sunday->format('d M Y');

        return response()->json([
            'labels'     => $labels,
            'datasets'   => $datasets,
            'weekLabel'  => $weekLabel,
            'weekOffset' => $weekOffset,
            'canPrev'    => $weekOffset < 6,
            'canNext'    => $weekOffset > 0,
        ]);
    }
}

