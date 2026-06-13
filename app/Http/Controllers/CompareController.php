<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\Tariff;

class CompareController extends Controller
{
    /**
     * PBI #33 – Show the comparison page with device selector
     */
    public function index()
    {
        $devices = Device::where('user_id', auth()->id())
                         ->orderBy('name')
                         ->get();

        return view('compare.index', compact('devices'));
    }

    /**
     * PBI #34 & #35 – Display comparison results + highlight recommendation
     */
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

        // Abort if any device doesn't belong to user
        if ($devices->count() !== count($request->device_ids)) {
            abort(403);
        }

        // Build comparison data array (PBI #34)
        $comparison = $devices->map(function ($device) use ($rate) {
            $monthly_kwh  = ($device->wattage / 1000) * $device->usage_hours_per_day * $device->usage_days_per_month;
            $monthly_cost = $monthly_kwh * $rate;
            $yearly_kwh   = $monthly_kwh * 12;
            $yearly_cost  = $monthly_cost * 12;

            $labelScores  = ['A' => 95, 'B' => 80, 'C' => 65, 'D' => 45, 'E' => 25];
            $efficiency   = $labelScores[strtoupper($device->energy_label ?? '')] ?? 50;

            return [
                'id'            => $device->id,
                'name'          => $device->name,
                'brand'         => $device->brand,
                'category'      => $device->category,
                'wattage'       => $device->wattage,
                'usage_hours'   => $device->usage_hours_per_day,
                'usage_days'    => $device->usage_days_per_month,
                'energy_label'  => strtoupper($device->energy_label ?? '?'),
                'monthly_kwh'   => round($monthly_kwh, 2),
                'monthly_cost'  => round($monthly_cost, 0),
                'yearly_kwh'    => round($yearly_kwh, 2),
                'yearly_cost'   => round($yearly_cost, 0),
                'efficiency'    => $efficiency,
            ];
        });

        // PBI #35 – Find the most efficient device (highest efficiency score, lowest cost as tiebreaker)
        $recommended = $comparison->sortByDesc('efficiency')->sortBy('monthly_cost')->first();

        // Savings vs worst device
        $leastEfficient  = $comparison->sortBy('efficiency')->first();
        $potentialSavings = round($leastEfficient['monthly_cost'] - $recommended['monthly_cost'], 0);

        return view('compare.results', compact('comparison', 'recommended', 'potentialSavings', 'rate'));
    }
}
