<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterDevice;
use Illuminate\Http\Request;

class MasterDeviceController extends Controller
{
    /**
     * PBI #2: View all master devices (list page)
     */
    public function index()
    {
        // Get all devices from database, ordered by name
        $masterDevices = MasterDevice::orderBy('name')->get();

        // Show the list page and pass the data
        return view('admin.master-devices.index', compact('masterDevices'));
    }

    /**
     * PBI #1: Show the "Add Device" form
     */
    public function create()
    {
        return view('admin.master-devices.create');
    }

    /**
     * PBI #1: Save the new device to database
     */
    public function store(Request $request)
    {
        // Validate the form input
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'category'    => 'required|string|max:255',
            'wattage'     => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
        ]);

        // Create the device in the database
        MasterDevice::create($validated);

        // Redirect back to list with success message
        return redirect()->route('admin.master-devices.index')
            ->with('success', 'Master device added successfully.');
    }

    /**
     * Show the edit form (pre-filled with existing data)
     */
    public function edit(MasterDevice $masterDevice)
    {
        return view('admin.master-devices.edit', compact('masterDevice'));
    }

    /**
     * Update an existing device
     */
    public function update(Request $request, MasterDevice $masterDevice)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'category'    => 'required|string|max:255',
            'wattage'     => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
        ]);

        $masterDevice->update($validated);

        return redirect()->route('admin.master-devices.index')
            ->with('success', 'Master device updated successfully.');
    }

    /**
     * Delete a device
     */
    public function destroy(MasterDevice $masterDevice)
    {
        $masterDevice->delete();

        return redirect()->route('admin.master-devices.index')
            ->with('success', 'Master device deleted successfully.');
    }
}
