<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\Tariff;
use App\Models\UsageLog;
use Illuminate\Support\Carbon;

class CompareController extends Controller
{
    public function index()
    {
        $devices = Device::where('user_id', auth()->id())
                         ->orderBy('name')
                         ->get();

        return view('compare.index', compact('devices'));
    }

    public function compare(Request $request)
    {
        $request->validate([
            'device_ids'   => 'required|array|min:2|max:4',
            'device_ids.*' => 'exists:devices,id',
        ]);

        $rate = Tariff::orderBy('effective_date', 'desc')->value('rate_per_kwh') ?? 1444.70;

        $devices = Device::where('user_id', auth()->id())
                         ->whereIn('id', $request->device_ids)
                         ->get();

        if ($devices->count() !== count($request->device_ids)) {
            abort(403);
        }

        $comparison = $devices->map(function ($device) use ($rate) {
            $avgHours = UsageLog::where('user_id', auth()->id())
                                ->where('device_id', $device->id)
                                ->where('usage_date', '>=', Carbon::today()->subDays(6))
                                ->avg('hours');

            $dailyHours   = $avgHours ?? 1;
            $daysPerMonth = $device->usage_days_per_month ?? 30;

            $monthly_kwh  = ($device->wattage / 1000) * $dailyHours * $daysPerMonth;
            $monthly_cost = $monthly_kwh * $rate;
            $yearly_kwh   = $monthly_kwh * 12;
            $yearly_cost  = $monthly_cost * 12;

            $labelScores  = ['A' => 95, 'B' => 80, 'C' => 65, 'D' => 45, 'E' => 25];
            $energyLabel  = $device->energy_label ?: 'C';
            $efficiency   = $labelScores[strtoupper($energyLabel)] ?? 65;

            return [
                'id'            => $device->id,
                'name'          => $device->name,
                'brand'         => $device->brand,
                'category'      => $device->category,
                'wattage'       => $device->wattage,
                'usage_hours'   => round($dailyHours, 1),
                'usage_days'    => $daysPerMonth,
                'energy_label'  => strtoupper($energyLabel),
                'monthly_kwh'   => round($monthly_kwh, 2),
                'monthly_cost'  => round($monthly_cost, 0),
                'yearly_kwh'    => round($yearly_kwh, 2),
                'yearly_cost'   => round($yearly_cost, 0),
                'efficiency'    => $efficiency,
            ];
        });

        $recommended = $comparison->sortByDesc('efficiency')->sortBy('monthly_cost')->first();

        $leastEfficient  = $comparison->sortBy('efficiency')->first();
        $potentialSavings = round($leastEfficient['monthly_cost'] - $recommended['monthly_cost'], 0);

        return view('compare.results', compact('comparison', 'recommended', 'potentialSavings', 'rate'));
    }
}