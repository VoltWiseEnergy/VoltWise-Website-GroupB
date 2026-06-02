<?php
namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\RecommendationCheck;
use App\Services\ConsumptionAnalyzer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecommendationController extends Controller
{
    public function index()
    {
        $userId   = Auth::id();
        $analyzer = new ConsumptionAnalyzer($userId, 30);
        $patterns = $analyzer->analyze();

        $checks = RecommendationCheck::where('user_id', $userId)
            ->pluck('is_checked', 'recommendation_key');

        $score = $this->calculateEnergyScore($patterns);

        // Ambil devices untuk simulator
        $devices = Device::where('user_id', $userId)
            ->get(['id', 'name', 'wattage', 'tariff'])
            ->map(function ($d) use ($patterns) {
                $log = $patterns['high_usage_devices']
                    ->firstWhere('device_id', $d->id);
                return [
                    'id'        => $d->id,
                    'name'      => $d->name,
                    'wattage'   => $d->wattage,
                    'tariff'    => $d->tariff ?? 1444.70,
                    'avg_hours' => $log ? round($log['avg_kwh'] * 1000 / $d->wattage, 1) : 0,
                ];
            });

        return view('recommendations.index', compact('patterns', 'checks', 'score', 'devices'));
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'key' => 'required|string|max:255',
        ]);

        $check = RecommendationCheck::where('user_id', Auth::id())
            ->where('recommendation_key', $request->key)
            ->first();

        if ($check) {
            $check->update(['is_checked' => !$check->is_checked]);
        } else {
            RecommendationCheck::create([
                'user_id'            => Auth::id(),
                'recommendation_key' => $request->key,
                'is_checked'         => true,
            ]);
        }

        return response()->json(['success' => true]);
    }

    protected function calculateEnergyScore(array $patterns): array
    {
        $score = 100;
        $score -= $patterns['high_usage_devices']->count() * 20;
        $score -= $patterns['always_on_devices']->count() * 15;
        $score -= $patterns['spike_days']->count() * 10;
        if ($patterns['daily_avg_kwh'] > 10) $score -= 10;
        if ($patterns['daily_avg_kwh'] > 20) $score -= 10;
        $score = max(0, min(100, $score));

        $grade = match(true) {
            $score >= 90 => 'A',
            $score >= 75 => 'B',
            $score >= 60 => 'C',
            $score >= 45 => 'D',
            default      => 'F',
        };

        $label = match($grade) {
            'A' => 'Excellent',
            'B' => 'Good',
            'C' => 'Fair',
            'D' => 'Poor',
            'F' => 'Critical',
        };

        $color = match($grade) {
            'A' => 'green',
            'B' => 'blue',
            'C' => 'orange',
            'D', 'F' => 'red',
        };

        return compact('score', 'grade', 'label', 'color');
    }
}