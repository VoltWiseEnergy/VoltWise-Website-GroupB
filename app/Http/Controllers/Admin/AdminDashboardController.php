<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterDevice;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        $totalDevices = MasterDevice::count();
        $recentDevices = MasterDevice::orderByDesc('created_at')->take(5)->get();

        return view('admin.dashboard', compact('totalDevices', 'recentDevices'));
    }
}
