<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index()
    {
        $devices = Device::where('user_id', auth()->id())->get();
        return view('devices.index', compact('devices'));
    }

    public function create()
    {
        return view('devices.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'wattage' => 'required|integer|min:1',
            'category' => 'required|max:255'
        ]);

        // Calculate daily energy (default 1 hour usage) and tariff
        $dailyEnergyKwh = round(($request->wattage / 1000) * 1, 3);
        $tariff = $dailyEnergyKwh * 1444.7;

        Device::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'wattage' => $request->wattage,
            'category' => $request->category,
            'daily_energy_kwh' => $dailyEnergyKwh,
            'tariff' => $tariff
        ]);

        return redirect()->route('devices.index')
            ->with('success', 'Device added successfully.');
    }

    public function show(Device $device)
    {
        //
    }

    public function edit(Device $device)
    {
        if ($device->user_id != auth()->id()) {
            abort(403);
        }

        return view('devices.edit', compact('device'));
    }

    public function update(Request $request, Device $device)
    {
        if ($device->user_id != auth()->id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|max:255',
            'wattage' => 'required|integer|min:1',
            'category' => 'required|max:255'
        ]);

        // Recalculate daily energy and tariff on update
        $dailyEnergyKwh = round(($request->wattage / 1000) * 1, 3);
        $tariff = $dailyEnergyKwh * 1444.7;

        $device->update([
            'name' => $request->name,
            'wattage' => $request->wattage,
            'category' => $request->category,
            'daily_energy_kwh' => $dailyEnergyKwh,
            'tariff' => $tariff
        ]);

        return redirect()->route('devices.index')
            ->with('success', 'Device updated successfully.');
    }

    public function destroy(Device $device)
    {
        if ($device->user_id != auth()->id()) {
            abort(403);
        }

        $device->delete();

        return redirect()->route('devices.index')
            ->with('success', 'Device deleted successfully.');
    }
}
