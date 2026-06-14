@extends('layouts.app')

@section('title', 'Comparison Results')

@section('styles')
<style>
.compare-back {
    display:inline-flex; align-items:center; gap:6px; text-decoration:none;
    color:var(--text-muted); font-size:0.875rem; margin-bottom:1rem;
    padding:6px 12px; border:1px solid var(--border); border-radius:8px;
    transition:all 0.15s;
}
.compare-back:hover { background:var(--nav-hover-bg); }

.compare-grid {
    display:grid; gap:1rem; margin-bottom:1.5rem;
    grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));
}
.compare-device-card {
    background:var(--bg-card); border:1px solid var(--border);
    border-radius:12px; padding:1.25rem; position:relative;
    transition:border-color 0.2s, box-shadow 0.2s;
}
.compare-device-card.recommended {
    border-color:var(--blue-600); box-shadow:0 0 0 2px rgba(74,124,246,0.2);
}
.recommended-badge {
    position:absolute; top:-10px; right:12px;
    background:var(--blue-600); color:#fff; padding:2px 10px;
    border-radius:12px; font-size:0.68rem; font-weight:700;
    letter-spacing:0.03em;
}
.compare-device-name { font-size:1rem; font-weight:700; color:var(--text-primary); margin-bottom:0.125rem; }
.compare-device-brand { font-size:0.75rem; color:var(--text-muted); margin-bottom:0.75rem; }
.compare-row { display:flex; justify-content:space-between; align-items:center; padding:0.4rem 0; border-bottom:1px solid var(--border); }
.compare-row:last-child { border-bottom:none; }
.compare-label { font-size:0.75rem; color:var(--text-muted); }
.compare-value { font-size:0.8125rem; font-weight:600; color:var(--text-primary); }
.compare-value.highlight { color:var(--blue-600); }

.efficiency-bar-wrap { width:100%; height:8px; background:var(--budget-track-bg); border-radius:99px; overflow:hidden; margin-top:0.375rem; }
.efficiency-bar { height:100%; border-radius:99px; transition:width 0.6s ease; }
.efficiency-a { background:#10b981; }
.efficiency-b { background:#22c55e; }
.efficiency-c { background:#f59e0b; }
.efficiency-d { background:#f97316; }
.efficiency-e { background:#ef4444; }

.savings-card {
    background:linear-gradient(135deg, var(--icon-green-bg), var(--icon-blue-bg));
    border:1px solid var(--border); border-radius:12px; padding:1.25rem 1.5rem;
    display:flex; align-items:center; gap:1rem;
}
.savings-icon { width:44px; height:44px; background:var(--icon-green-bg); color:var(--icon-green-fg); border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.savings-icon svg { width:22px; height:22px; }
.savings-title { font-size:0.8125rem; color:var(--text-muted); }
.savings-amount { font-size:1.375rem; font-weight:700; color:var(--text-primary); }
.savings-detail { font-size:0.75rem; color:var(--text-muted); margin-top:0.125rem; }

.compare-summary-table { width:100%; border-collapse:collapse; margin-top:1rem; }
.compare-summary-table th { text-align:left; padding:0.5rem 0.75rem; font-size:0.72rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; border-bottom:1px solid var(--border); }
.compare-summary-table td { padding:0.6rem 0.75rem; font-size:0.8125rem; color:var(--text-secondary); border-bottom:1px solid var(--border); }
.compare-summary-table tr:hover td { background:rgba(74,124,246,0.04); }
.best-value { color:var(--icon-green-fg); font-weight:700; }
</style>
@endsection

@section('content')
<a href="{{ route('compare.index') }}" class="compare-back">← Back to Selection</a>

<div class="page-header">
    <div>
        <h1 class="page-title">📊 Comparison Results</h1>
        <p class="page-subtitle">Side-by-side energy consumption, cost, and efficiency analysis</p>
    </div>
</div>

{{-- Savings highlight --}}
@if($potentialSavings > 0)
<div class="savings-card" style="margin-bottom:1.5rem;">
    <div class="savings-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/>
        </svg>
    </div>
    <div>
        <div class="savings-title">Potential Monthly Savings</div>
        <div class="savings-amount">Rp {{ number_format($potentialSavings, 0, ',', '.') }}</div>
        <div class="savings-detail">By switching to the most efficient device: <strong>{{ $recommended['name'] }}</strong></div>
    </div>
</div>
@endif

{{-- Device cards --}}
<div class="compare-grid">
    @foreach($comparison as $device)
    <div class="compare-device-card {{ $device['id'] === $recommended['id'] ? 'recommended' : '' }}">
        @if($device['id'] === $recommended['id'])
            <span class="recommended-badge">⭐ RECOMMENDED</span>
        @endif
        <div class="compare-device-name">{{ $device['name'] }}</div>
        <div class="compare-device-brand">{{ $device['brand'] ?? '—' }} · {{ $device['category'] }}</div>

        <div class="compare-row">
            <span class="compare-label">Wattage</span>
            <span class="compare-value">{{ number_format($device['wattage']) }}W</span>
        </div>
        <div class="compare-row">
            <span class="compare-label">Usage</span>
            <span class="compare-value">{{ $device['usage_hours'] }}h/day · {{ $device['usage_days'] }}d/mo</span>
        </div>
        <div class="compare-row">
            <span class="compare-label">Energy Label</span>
            <span class="compare-value highlight">{{ $device['energy_label'] }}</span>
        </div>
        <div class="compare-row">
            <span class="compare-label">Monthly kWh</span>
            <span class="compare-value">{{ number_format($device['monthly_kwh'], 2) }} kWh</span>
        </div>
        <div class="compare-row">
            <span class="compare-label">Monthly Cost</span>
            <span class="compare-value">Rp {{ number_format($device['monthly_cost'], 0, ',', '.') }}</span>
        </div>
        <div class="compare-row">
            <span class="compare-label">Yearly Cost</span>
            <span class="compare-value">Rp {{ number_format($device['yearly_cost'], 0, ',', '.') }}</span>
        </div>

        <div style="margin-top:0.75rem;">
            <div style="display:flex; justify-content:space-between; font-size:0.72rem; color:var(--text-muted); margin-bottom:0.25rem;">
                <span>Efficiency</span>
                <span style="font-weight:600; color:var(--text-primary);">{{ $device['efficiency'] }}%</span>
            </div>
            <div class="efficiency-bar-wrap">
                @php
                    $barClass = match(true) {
                        $device['efficiency'] >= 90 => 'efficiency-a',
                        $device['efficiency'] >= 75 => 'efficiency-b',
                        $device['efficiency'] >= 55 => 'efficiency-c',
                        $device['efficiency'] >= 40 => 'efficiency-d',
                        default => 'efficiency-e',
                    };
                @endphp
                <div class="efficiency-bar {{ $barClass }}" style="width:{{ $device['efficiency'] }}%;"></div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Summary table --}}
<div class="card">
    <div class="card-body">
        <h3 class="card-title" style="margin-bottom:0.75rem;">Comparison Summary</h3>

        @php
            $minCost = $comparison->min('monthly_cost');
            $minKwh  = $comparison->min('monthly_kwh');
            $maxEff  = $comparison->max('efficiency');
        @endphp

        <table class="compare-summary-table">
            <thead>
                <tr>
                    <th>Device</th>
                    <th>Wattage</th>
                    <th>Monthly kWh</th>
                    <th>Monthly Cost</th>
                    <th>Yearly Cost</th>
                    <th>Efficiency</th>
                </tr>
            </thead>
            <tbody>
                @foreach($comparison as $d)
                <tr>
                    <td style="font-weight:600;">{{ $d['name'] }}</td>
                    <td>{{ number_format($d['wattage']) }}W</td>
                    <td class="{{ $d['monthly_kwh'] == $minKwh ? 'best-value' : '' }}">
                        {{ number_format($d['monthly_kwh'], 2) }}
                    </td>
                    <td class="{{ $d['monthly_cost'] == $minCost ? 'best-value' : '' }}">
                        Rp {{ number_format($d['monthly_cost'], 0, ',', '.') }}
                    </td>
                    <td>Rp {{ number_format($d['yearly_cost'], 0, ',', '.') }}</td>
                    <td class="{{ $d['efficiency'] == $maxEff ? 'best-value' : '' }}">
                        {{ $d['efficiency'] }}%
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <p style="font-size:0.75rem; color:var(--text-faint); margin-top:0.75rem;">
            Tariff rate used: Rp {{ number_format($rate, 2, ',', '.') }}/kWh · Green values indicate the best in category
        </p>
    </div>
</div>
@endsection
