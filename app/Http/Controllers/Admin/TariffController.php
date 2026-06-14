<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Tariff;
use App\Http\Controllers\Controller;

class TariffController extends Controller
{
    public function index()
    {
        $tariffs = Tariff::orderBy('effective_date', 'desc')->get();
        return view('admin.tariff.index', compact('tariffs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'rate_per_kwh' => 'required|numeric|min:0',
            'effective_date' => 'required|date'
        ]);

        Tariff::create([
            'rate_per_kwh' => $request->rate_per_kwh,
            'effective_date' => $request->effective_date
        ]);

        return redirect()->route('admin.tariff.index')
            ->with('success', 'Tariff record created successfully.');
    }
}
