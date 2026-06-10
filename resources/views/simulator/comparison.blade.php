@extends('layouts.app')

@section('title', 'Simulation Comparison')

@section('content')

<style>
    .comp-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1.5rem; }
    .comp-header h2 { font-size:1.25rem; font-weight:600; color:var(--text-primary); margin-bottom:0.25rem; }
    .comp-header p  { font-size:0.8rem; color:var(--text-muted); margin:0; }

    .btn-back {
        font-size:0.8rem; color:var(--text-muted); text-decoration:none;
        display:inline-flex; align-items:center; gap:0.35rem;
        border:1px solid var(--border); border-radius:7px; padding:0.4rem 0.85rem;
    }
    .btn-back:hover { color:var(--text-primary); }

    .comp-grid { display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1.25rem; }
    @media(max-width:560px){ .comp-grid { grid-template-columns:1fr; } }

    .comp-card {
        background:var(--bg-card); border:1px solid var(--border);
        border-radius:12px; overflow:hidden; box-shadow:var(--shadow-card);
    }
    .comp-card-header { padding:0.85rem 1.1rem; font-size:0.82rem; font-weight:600; }
    .header-current  { background:#EEF1FE; color:#3730A3; }
    .header-scenario { background:#D1FAE5; color:#065F46; }

    .comp-card-body { padding:1rem 1.1rem; }
    .metric-row { display:flex; justify-content:space-between; align-items:center; padding:0.5rem 0; border-bottom:1px solid var(--border); }
    .metric-row:last-child { border-bottom:none; }
    .metric-label { font-size:0.78rem; color:var(--text-muted); }
    .metric-value { font-size:0.9rem; font-weight:600; color:var(--text-primary); }
    .metric-value.blue { color:var(--blue-600); }

    .saving-card {
        background:var(--bg-card); border:1px solid var(--border);
        border-radius:12px; padding:1.25rem; margin-bottom:1.25rem;
        box-shadow:var(--shadow-card);
    }
    .saving-title { font-size:0.85rem; font-weight:600; color:var(--text-primary); margin-bottom:1rem; }

    .saving-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:0.75rem; }
    @media(max-width:480px){ .saving-grid { grid-template-columns:1fr; } }

    .saving-item { text-align:center; padding:0.75rem; background:var(--bg-base); border-radius:8px; }
    .saving-item .s-label { font-size:0.72rem; color:var(--text-muted); margin-bottom:0.35rem; }
    .saving-item .s-value { font-size:1.1rem; font-weight:600; }
    .s-pos { color:#059669; }
    .s-neg { color:#DC2626; }

    .progress-wrap { margin-bottom:1rem; }
    .progress-label { display:flex; justify-content:space-between; font-size:0.75rem; color:var(--text-muted); margin-bottom:0.4rem; }
    .progress-bar { height:8px; background:var(--border); border-radius:4px; overflow:hidden; }
    .progress-fill { height:100%; border-radius:4px; transition:width 0.6s ease; }
    .fill-current  { background:var(--blue-600); }
    .fill-scenario { background:#059669; }

    .device-info-card {
        background:var(--bg-base); border-radius:8px;
        padding:0.75rem 1rem; margin-bottom:1.25rem;
        font-size:0.78rem; color:var(--text-muted);
        display:flex; gap:1.5rem; flex-wrap:wrap;
    }
    .device-info-card span { color:var(--text-primary); font-weight:500; }
</style>

@php
    $pct        = $result['current']['kwh'] > 0
                    ? round(($result['scenario']['kwh'] / $result['current']['kwh']) * 100, 1)
                    : 0;
    $pctStyle   = 'width:' . min($pct, 100) . '%';
    $isPositive = $result['saving']['isPositive'];
    $savingSign = $isPositive ? '-' : '+';
@endphp

<div style="padding:1.5rem;">

    {{-- Header --}}
    <div class="comp-header">
        <div>
            <h2>{{ $scenario->name }}</h2>
            <p>Actual vs scenario usage comparison for {{ $scenario->device_name }}</p>
        </div>
        <a href="{{ route('simulator.index') }}" class="btn-back">← Back</a>
    </div>

    {{-- Device info --}}
    <div class="device-info-card">
        Device: <span>{{ $scenario->device_name }}</span>
        Wattage: <span>{{ $scenario->wattage }}W</span>
        Tariff: <span>Rp {{ number_format($scenario->tariff, 0, ',', '.') }}/kWh</span>
        Created: <span>{{ $scenario->created_at->format('d M Y') }}</span>
    </div>

    {{-- PBI #58 — Side by side --}}
    <div class="comp-grid">
        <div class="comp-card">
            <div class="comp-card-header header-current">📊 Actual Usage (7-day average)</div>
            <div class="comp-card-body">
                <div class="metric-row">
                    <span class="metric-label">Hours / day</span>
                    <span class="metric-value">{{ $result['current']['hours'] }} hrs</span>
                </div>
                <div class="metric-row">
                    <span class="metric-label">Energy consumption</span>
                    <span class="metric-value blue">{{ $result['current']['kwh'] }} kWh</span>
                </div>
                <div class="metric-row">
                    <span class="metric-label">Est. cost / day</span>
                    <span class="metric-value">Rp {{ number_format($result['current']['cost'], 0, ',', '.') }}</span>
                </div>
                <div class="metric-row">
                    <span class="metric-label">Est. cost / month</span>
                    <span class="metric-value">Rp {{ number_format($result['current']['cost'] * 30, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div class="comp-card">
            <div class="comp-card-header header-scenario">✨ Simulated Scenario</div>
            <div class="comp-card-body">
                <div class="metric-row">
                    <span class="metric-label">Hours / day</span>
                    <span class="metric-value">{{ $result['scenario']['hours'] }} hrs</span>
                </div>
                <div class="metric-row">
                    <span class="metric-label">Energy consumption</span>
                    <span class="metric-value blue">{{ $result['scenario']['kwh'] }} kWh</span>
                </div>
                <div class="metric-row">
                    <span class="metric-label">Est. cost / day</span>
                    <span class="metric-value">Rp {{ number_format($result['scenario']['cost'], 0, ',', '.') }}</span>
                </div>
                <div class="metric-row">
                    <span class="metric-label">Est. cost / month</span>
                    <span class="metric-value">Rp {{ number_format($result['scenario']['cost'] * 30, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Progress bar --}}
    <div class="saving-card">
        <div class="saving-title">Consumption Comparison</div>
        <div class="progress-wrap">
            <div class="progress-label">
                <span>Actual — {{ $result['current']['kwh'] }} kWh</span>
                <span>100%</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill fill-current" style="width:100%"></div>
            </div>
        </div>
        <div class="progress-wrap">
            <div class="progress-label">
                <span>Scenario — {{ $result['scenario']['kwh'] }} kWh</span>
                <span>{{ $pct }}%</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill fill-scenario" style="{{ $pctStyle }}"></div>
            </div>
        </div>
    </div>

    {{-- Saving summary --}}
    <div class="saving-card">
        <div class="saving-title">Savings Summary</div>
        <div class="saving-grid">
            <div class="saving-item">
                <div class="s-label">kWh saved / day</div>
                <div class="s-value {{ $isPositive ? 's-pos' : 's-neg' }}">
                    {{ $savingSign }}{{ abs($result['current']['kwh'] - $result['scenario']['kwh']) }} kWh
                </div>
            </div>
            <div class="saving-item">
                <div class="s-label">Cost saved / day</div>
                <div class="s-value {{ $isPositive ? 's-pos' : 's-neg' }}">
                    {{ $savingSign }}Rp {{ number_format(abs($result['saving']['amount']), 0, ',', '.') }}
                </div>
            </div>
            <div class="saving-item">
                <div class="s-label">Cost saved / month</div>
                <div class="s-value {{ $isPositive ? 's-pos' : 's-neg' }}">
                    {{ $savingSign }}Rp {{ number_format(abs($result['saving']['amount']) * 30, 0, ',', '.') }}
                </div>
            </div>
        </div>

        <div style="text-align:center; margin-top:1rem; padding:0.75rem; background:var(--bg-base); border-radius:8px;">
            @if($isPositive)
                <span style="font-size:1.5rem; font-weight:600; color:#059669;">
                    Save {{ $result['saving']['percent'] }}%
                </span>
                <div style="font-size:0.75rem; color:var(--text-muted); margin-top:0.25rem;">
                    With this scenario you could reduce your electricity bill by {{ $result['saving']['percent'] }}%
                </div>
            @else
                <span style="font-size:1.5rem; font-weight:600; color:#DC2626;">
                    Increase by {{ abs($result['saving']['percent']) }}%
                </span>
                <div style="font-size:0.75rem; color:var(--text-muted); margin-top:0.25rem;">
                    This scenario would increase your electricity bill by {{ abs($result['saving']['percent']) }}%
                </div>
            @endif
        </div>
    </div>

</div>

@endsection