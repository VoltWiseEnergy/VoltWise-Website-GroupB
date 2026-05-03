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

        Device::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'wattage' => $request->wattage,
            'category' => $request->category
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

        $device->update([
            'name' => $request->name,
            'wattage' => $request->wattage,
            'category' => $request->category
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