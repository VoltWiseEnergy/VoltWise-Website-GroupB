<?php
 
namespace App\Http\Controllers;
 
use App\Models\Device;
use App\Models\UsageLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
 
class UsageController extends Controller
{
    // ─────────────────────────────────────────
    // Tampilkan halaman Daily Tracker
    // GET /usage/tracker
    // ─────────────────────────────────────────
    public function tracker()
    {
        $userId = Auth::id();
        $today  = Carbon::today()->toDateString();
 
        $devices = Device::where('user_id', $userId)->get();
 
        $todayUsage = UsageLog::where('user_id', $userId)
            ->where('usage_date', $today)
            ->get()
            ->keyBy('device_id');
 
        return view('usage.tracker', compact('devices', 'todayUsage'));
    }
 
    // ─────────────────────────────────────────
    // PBI #16 — Set default usage hours
    // POST /usage/default
    // body: { device_id, hours }
    // ─────────────────────────────────────────
    public function setDefault(Request $request)
    {
        $request->validate([
            'device_id' => 'required|integer|exists:devices,id',
            'hours'     => 'required|numeric|min:0|max:24',
        ]);
 
        // Ambil dari Device model William langsung
        $device = Device::where('id', $request->device_id)
                        ->where('user_id', Auth::id())
                        ->firstOrFail();
 
        UsageLog::updateOrCreate(
            [
                'user_id'    => Auth::id(),
                'device_id'  => $device->id,
                'usage_date' => Carbon::today()->toDateString(),
            ],
            [
                'device_name' => $device->name,
                'wattage'     => $device->wattage,
                'hours'       => $request->hours,
                'is_override' => false,
            ]
        );
 
        return redirect()->route('usage.tracker')
            ->with('success', "Default usage for {$device->name} saved!");
    }
 
    // ─────────────────────────────────────────
    // PBI #17 — Override usage hari tertentu
    // POST /usage/override
    // body: { device_id, hours, usage_date }
    // ─────────────────────────────────────────
    public function override(Request $request)
    {
        $request->validate([
            'device_id'  => 'required|integer|exists:devices,id',
            'hours'      => 'required|numeric|min:0|max:24',
            'usage_date' => 'required|date',
        ]);
 
        $device = Device::where('id', $request->device_id)
                        ->where('user_id', Auth::id())
                        ->firstOrFail();
 
        UsageLog::updateOrCreate(
            [
                'user_id'    => Auth::id(),
                'device_id'  => $device->id,
                'usage_date' => $request->usage_date,
            ],
            [
                'device_name' => $device->name,
                'wattage'     => $device->wattage,
                'hours'       => $request->hours,
                'is_override' => true,
            ]
        );
 
        return redirect()->route('usage.tracker')
            ->with('success', "Override for {$device->name} on {$request->usage_date} saved!");
    }
 
    // ─────────────────────────────────────────
    // PBI #18 — Tampilkan halaman usage history
    // GET /usage/history
    // ─────────────────────────────────────────
    public function history(Request $request)
    {
        $request->validate([
            'device_id' => 'nullable|integer|exists:devices,id',
            'days'      => 'nullable|integer|min:1|max:90',
        ]);
 
        $userId  = Auth::id();
        $days    = $request->input('days', 7);
        $devices = Device::where('user_id', $userId)->get();
 
        $query = UsageLog::where('user_id', $userId)
            ->where('usage_date', '>=', Carbon::today()->subDays($days - 1))
            ->orderBy('usage_date', 'desc');
 
        if ($request->filled('device_id')) {
            $query->where('device_id', $request->device_id);
        }
 
        $logs = $query->get();
 
        return view('usage.history', compact('logs', 'devices'));
    }
 
    // ─────────────────────────────────────────
    // Bonus — Usage hari ini (JSON, untuk dashboard)
    // GET /usage/today
    // ─────────────────────────────────────────
    public function today()
    {
        $logs = UsageLog::where('user_id', Auth::id())
            ->where('usage_date', Carbon::today()->toDateString())
            ->get()
            ->map(fn($log) => [
                'device_id'   => $log->device_id,
                'device_name' => $log->device_name,
                'wattage'     => $log->wattage,
                'hours'       => $log->hours,
                'kwh'         => $log->kwh,
                'is_override' => $log->is_override,
            ]);
 
        return response()->json(['data' => $logs]);
    }
}