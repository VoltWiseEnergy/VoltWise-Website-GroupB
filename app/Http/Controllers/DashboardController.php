<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\UsageLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the user dashboard.
     */
    public function index()
    {
        $userId = Auth::id();
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
            return $device;
        });

        $todayEnergyKwh = $devices->sum('daily_energy_kwh');
        $topConsumer = $devices->sortByDesc('daily_energy_kwh')->first();
        $energyByCategory = $devices->groupBy('category')->map(fn ($group) => $group->sum('daily_energy_kwh'));

        // Rough estimation of monthly cost: Today's cost * 30 days. Cost per kWh = 1444 Rp.
        $monthlyCost = $todayEnergyKwh * 30 * 1444;

        return view('dashboard', compact(
            'devices',
            'totalDevices',
            'todayEnergyKwh',
            'topConsumer',
            'energyByCategory',
            'monthlyCost'
        ));
    }
}
