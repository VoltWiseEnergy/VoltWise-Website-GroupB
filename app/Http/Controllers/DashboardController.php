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

            // Calculate tariff (cost) based on daily kWh × Rp 1444.7
            $device->tariff = $device->daily_energy_kwh * 1444.7;

            return $device;
        });

        $todayEnergyKwh = $devices->sum('daily_energy_kwh');
        $todayCost = $devices->sum('tariff'); // NEW: total cost across all devices
        $topConsumer = $devices->sortByDesc('daily_energy_kwh')->first();
        $energyByCategory = $devices->groupBy('category')->map(fn ($group) => $group->sum('daily_energy_kwh'));

        // Monthly cost estimation: today's total cost × 30 days
        $monthlyCost = $todayCost * 30;

        return view('dashboard', compact(
            'devices',
            'totalDevices',
            'todayEnergyKwh',
            'topConsumer',
            'energyByCategory',
            'todayCost',
            'monthlyCost'
        ));
    }
}
