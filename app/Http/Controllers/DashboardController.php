<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MasterDevice;

class DashboardController extends Controller
{
    /**
     * Display the user dashboard.
     */
    public function index()
    {
        $devices = MasterDevice::orderBy('name')->get();
        $totalDevices = $devices->count();
        $todayEnergyKwh = $devices->sum(fn ($device) => $device->daily_energy_kwh);
        $topConsumer = $devices->sortByDesc('daily_energy_kwh')->first();
        $energyByCategory = $devices->groupBy('category')->map(fn ($group) => $group->sum('daily_energy_kwh'));

        return view('dashboard', compact(
            'devices',
            'totalDevices',
            'todayEnergyKwh',
            'topConsumer',
            'energyByCategory'
        ));
    }
}
