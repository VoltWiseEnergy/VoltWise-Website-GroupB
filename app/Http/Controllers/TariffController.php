<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tariff;

class TariffController extends Controller
{
    public function index()
    {
        return response()->json(
            Tariff::orderBy('effective_date', 'desc')->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'rate_per_kwh' => 'required|numeric',
            'effective_date' => 'required|date'
        ]);

        $tariff = Tariff::create([
            'rate_per_kwh' => $request->rate_per_kwh,
            'effective_date' => $request->effective_date
        ]);

        return response()->json([
            'message' => 'Tariff created',
            'data' => $tariff
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'rate_per_kwh' => 'required|numeric'
        ]);

        $tariff = Tariff::create([
            'rate_per_kwh' => $request->rate_per_kwh,
            'effective_date' => now()
        ]);

        return response()->json([
            'message' => 'Tariff updated (new record)',
            'data' => $tariff
        ]);
    }

    public function history()
    {
        return response()->json(
            Tariff::orderBy('effective_date', 'desc')->get()
        );
    }
}