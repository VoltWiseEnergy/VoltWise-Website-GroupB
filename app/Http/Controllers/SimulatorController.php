<?php
 
namespace App\Http\Controllers;
 
use App\Models\Device;
use App\Models\SimulatorScenario;
use App\Models\UsageLog;
use App\Services\SimulatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
 
class SimulatorController extends Controller
{
    public function __construct(private SimulatorService $simulator) {}
 
    // ─────────────────────────────────────────────────────────
    // PBI #56 — Tampilkan form input skenario
    // GET /simulator
    // ─────────────────────────────────────────────────────────
    public function index()
    {
        $userId  = Auth::id();
        $devices = Device::where('user_id', $userId)->get();
 
        // Ambil rata-rata jam aktual 7 hari dari usage_logs (Sprint 1)
        $avgUsage = UsageLog::where('user_id', $userId)
            ->where('usage_date', '>=', Carbon::today()->subDays(6))
            ->get()
            ->groupBy('device_id')
            ->map(fn($logs) => round($logs->avg('hours'), 2));
 
        // Ambil skenario tersimpan user
        $scenarios = SimulatorScenario::where('user_id', $userId)
            ->latest()
            ->get();
 
        return view('simulator.index', compact('devices', 'avgUsage', 'scenarios'));
    }
 
    // ─────────────────────────────────────────────────────────
    // PBI #56 + #57 — Simpan skenario dan hitung estimasi
    // POST /simulator
    // ─────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:100',
            'device_id'      => 'required|integer|exists:devices,id',
            'scenario_hours' => 'required|numeric|min:0|max:24',
        ]);
 
        $userId = Auth::id();
        $device = Device::where('id', $request->device_id)
                        ->where('user_id', $userId)
                        ->firstOrFail();
 
        // Ambil rata-rata jam aktual dari usage_logs
        $currentHours = UsageLog::where('user_id', $userId)
            ->where('device_id', $device->id)
            ->where('usage_date', '>=', Carbon::today()->subDays(6))
            ->avg('hours') ?? 0;
 
        // Mock tariff — ganti ke Tariff::latest()->value('rate') pas Narindra merge
        $tariff = 1444;
 
        SimulatorScenario::create([
            'user_id'        => $userId,
            'name'           => $request->name,
            'device_id'      => $device->id,
            'device_name'    => $device->name,
            'wattage'        => $device->wattage,
            'current_hours'  => round($currentHours, 2),
            'scenario_hours' => $request->scenario_hours,
            'tariff'         => $tariff,
        ]);
 
        return redirect()->route('simulator.index')
            ->with('success', "Scenario '{$request->name}' Saved!");
    }
 
    // ─────────────────────────────────────────────────────────
    // PBI #58 — Tampilkan halaman comparison
    // GET /simulator/{scenario}
    // ─────────────────────────────────────────────────────────
    public function show(SimulatorScenario $scenario)
    {
        // Pastikan hanya punya sendiri yang bisa diakses
        if ($scenario->user_id !== Auth::id()) {
            abort(403);
        }
 
        $result = $this->simulator->compute(
            $scenario->wattage,
            $scenario->current_hours,
            $scenario->scenario_hours,
            $scenario->tariff,
        );
 
        return view('simulator.comparison', compact('scenario', 'result'));
    }
 
    // ─────────────────────────────────────────────────────────
    // Hapus skenario
    // DELETE /simulator/{scenario}
    // ─────────────────────────────────────────────────────────
    public function destroy(SimulatorScenario $scenario)
    {
        if ($scenario->user_id !== Auth::id()) {
            abort(403);
        }
 
        $scenario->delete();
 
        return redirect()->route('simulator.index')
            ->with('success', 'Skenario berhasil dihapus.');
    }
}