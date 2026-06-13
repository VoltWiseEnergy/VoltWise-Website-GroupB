<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\MasterDevice;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index()
    {
        $currentTime = now();
        $devices = Device::where('user_id', auth()->id())->get();
        $dueReminders = Device::where('user_id', auth()->id())
            ->where('reminder_enabled', true)
            ->whereTime('reminder_time', '<=', $currentTime->format('H:i'))
            ->get();
        $reminderDevices = $devices->where('reminder_enabled', true);

        return view('devices.index', compact('devices', 'dueReminders', 'reminderDevices'));
    }

    public function create()
    {
        $masterDevices = MasterDevice::orderBy('name')->get();
        return view('devices.create', compact('masterDevices'));
    }

    public function store(Request $request)
    {
        // Kalau pilih dari master device, auto-fill name/wattage/category
        if ($request->filled('master_device_id')) {
            $master = MasterDevice::findOrFail($request->master_device_id);
            $request->merge([
                'name'     => $request->name     ?: $master->name,
                'wattage'  => $request->wattage  ?: $master->wattage,
                'category' => $request->category ?: $master->category,
            ]);
        }

        $request->validate([
            'name'     => 'required|max:255|unique:devices,name',
            'wattage'  => 'required|numeric|min:1',
            'category' => 'required|max:255'
        ], [
            'name.unique' => 'Device name already exists.'
        ]);

        $dailyEnergyKwh = round(($request->wattage / 1000) * 1, 3);
        $tariff = $dailyEnergyKwh * 1444.7;

        Device::create([
            'user_id'          => auth()->id(),
            'name'             => $request->name,
            'wattage'          => $request->wattage,
            'category'         => $request->category,
            'daily_energy_kwh' => $dailyEnergyKwh,
            'tariff'           => $tariff,
            'reminder_enabled' => false,
            'reminder_time'    => null,
            'reminder_message' => null,
        ]);

        return redirect()->route('devices.index')
            ->with('success', 'Device added successfully.');
    }

    public function scheduleReminder(Request $request)
    {
        $request->validate([
            'device_id'        => 'required|exists:devices,id',
            'reminder_time'    => 'required|date_format:H:i',
            'reminder_message' => 'nullable|string|max:255',
        ]);

        $device = Device::where('id', $request->device_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $device->update([
            'reminder_enabled' => true,
            'reminder_time'    => $request->reminder_time,
            'reminder_message' => $request->reminder_message ?: "Turn off {$device->name}",
        ]);

        return redirect()->route('devices.index')
            ->with('success', 'Reminder scheduled successfully.');
    }

    public function editReminder(Device $device)
    {
        if ($device->user_id != auth()->id()) {
            abort(403);
        }

        if (! $device->reminder_enabled) {
            return redirect()->route('devices.index')->with('error', 'No active reminder found for this device.');
        }

        return view('devices.reminders.edit', compact('device'));
    }

    public function updateReminder(Request $request, Device $device)
    {
        if ($device->user_id != auth()->id()) {
            abort(403);
        }

        $request->validate([
            'reminder_time'    => 'required|date_format:H:i',
            'reminder_message' => 'nullable|string|max:255',
        ]);

        $device->update([
            'reminder_enabled' => true,
            'reminder_time'    => $request->reminder_time,
            'reminder_message' => $request->reminder_message ?: "Turn off {$device->name}",
        ]);

        return redirect()->route('devices.index')->with('success', 'Reminder updated successfully.');
    }

    public function destroyReminder(Device $device)
    {
        if ($device->user_id != auth()->id()) {
            abort(403);
        }

        $device->update([
            'reminder_enabled' => false,
            'reminder_time'    => null,
            'reminder_message' => null,
        ]);

        return redirect()->route('devices.index')->with('success', 'Reminder deleted successfully.');
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
            'name'     => 'required|max:255|unique:devices,name,' . $device->id,
            'wattage'  => 'required|integer|min:1',
            'category' => 'required|max:255'
        ], [
            'name.unique' => 'Device name already exists.'
        ]);

        $dailyEnergyKwh = round(($request->wattage / 1000) * 1, 3);
        $tariff = $dailyEnergyKwh * 1444.7;

        $device->update([
            'name'             => $request->name,
            'wattage'          => $request->wattage,
            'category'         => $request->category,
            'daily_energy_kwh' => $dailyEnergyKwh,
            'tariff'           => $tariff,
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