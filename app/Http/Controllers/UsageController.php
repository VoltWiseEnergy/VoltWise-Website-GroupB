<?php
 
namespace App\Http\Controllers;
 
use App\Models\UsageLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
 
class UsageController extends Controller
{
    // PBI #16 — Set default usage hours
    // POST /usage/default
    // body: { device_id, device_name, wattage, hours }
    public function setDefault(Request $request)
    {
        $request->validate([
            'device_id'   => 'required|string',
            'device_name' => 'required|string',
            'wattage'     => 'required|integer|min:1',
            'hours'       => 'required|numeric|min:0|max:24',
        ]);
 
        UsageLog::updateOrCreate(
            [
                'user_id'    => Auth::id(),
                'device_id'  => $request->device_id,
                'usage_date' => Carbon::today()->toDateString(),
            ],
            [
                'device_name' => $request->device_name,
                'wattage'     => $request->wattage,
                'hours'       => $request->hours,
                'is_override' => false,
            ]
        );
 
        return response()->json(['message' => 'Default usage saved.']);
    }
 
    // PBI #17 — Override usage hari tertentu
    // POST /usage/override
    // body: { device_id, device_name, wattage, hours, usage_date }
    public function override(Request $request)
    {
        $request->validate([
            'device_id'   => 'required|string',
            'device_name' => 'required|string',
            'wattage'     => 'required|integer|min:1',
            'hours'       => 'required|numeric|min:0|max:24',
            'usage_date'  => 'required|date',
        ]);
 
        UsageLog::updateOrCreate(
            [
                'user_id'    => Auth::id(),
                'device_id'  => $request->device_id,
                'usage_date' => $request->usage_date,
            ],
            [
                'device_name' => $request->device_name,
                'wattage'     => $request->wattage,
                'hours'       => $request->hours,
                'is_override' => true,
            ]
        );
 
        return response()->json(['message' => 'Override usage saved.']);
    }
 
    // PBI #18 — View usage history
    // GET /usage/history?device_id=xxx&days=7
    public function history(Request $request)
    {
        $request->validate([
            'device_id' => 'nullable|string',
            'days'      => 'nullable|integer|min:1|max:90',
        ]);
 
        $days  = $request->input('days', 7);
 
        $query = UsageLog::where('user_id', Auth::id())
            ->where('usage_date', '>=', Carbon::today()->subDays($days - 1))
            ->orderBy('usage_date', 'desc');
 
        if ($request->filled('device_id')) {
            $query->where('device_id', $request->device_id);
        }
 
        $logs = $query->get()->map(fn($log) => [
            'date'        => $log->usage_date->format('d M Y'),
            'device_id'   => $log->device_id,
            'device_name' => $log->device_name,
            'wattage'     => $log->wattage,
            'hours'       => $log->hours,
            'kwh'         => $log->kwh,
            'is_override' => $log->is_override,
        ]);
 
        return response()->json(['data' => $logs]);
    }
 
    // Bonus — Usage hari ini semua device
    // GET /usage/today
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