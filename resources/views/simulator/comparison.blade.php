@extends('layouts.app')

@section('title', 'Simulation Comparison')

@section('content')

<style>
.comp-header { display:flex; justify-content:space-between; margin-bottom:1.5rem; }
.comp-header h2 { font-size:1.25rem; font-weight:600; }
.comp-header p { font-size:0.8rem; color:gray; }

.btn-back {
    font-size:0.8rem;
    border:1px solid #ccc;
    border-radius:6px;
    padding:0.4rem 0.8rem;
    text-decoration:none;
}

.comp-grid {
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:1rem;
}

.comp-card {
    border:1px solid #ddd;
    border-radius:10px;
}

.comp-card-header {
    padding:0.8rem;
    font-weight:600;
}

.header-current { background:#eef1fe; }
.header-scenario { background:#d1fae5; }

.comp-card-body { padding:1rem; }

.metric-row {
    display:flex;
    justify-content:space-between;
    padding:0.4rem 0;
    border-bottom:1px solid #eee;
}

.saving-card {
    margin-top:1.5rem;
    padding:1rem;
    border:1px solid #ddd;
    border-radius:10px;
}

.progress-wrap { margin-bottom:1rem; }

.progress-label {
    display:flex;
    justify-content:space-between;
    font-size:0.75rem;
}

.progress-bar {
    height:8px;
    background:#ddd;
    border-radius:4px;
    overflow:hidden;
}

.progress-fill {
    height:100%;
    transition:width 0.6s ease;
}

.fill-current { background:blue; }
.fill-scenario { background:green; }
</style>

<div style="padding:1.5rem;">

{{-- HEADER --}}
<div class="comp-header">
    <div>
        <h2>{{ $scenario->name }}</h2>
        <p>Perbandingan pemakaian aktual vs skenario</p>
    </div>
    <a href="{{ route('simulator.index') }}" class="btn-back">← Kembali</a>
</div>

{{-- DATA --}}
<div class="comp-grid">

    <div class="comp-card">
        <div class="comp-card-header header-current">Aktual</div>
        <div class="comp-card-body">
            <div class="metric-row">
                <span>Jam</span>
                <span>{{ $result['current']['hours'] }}</span>
            </div>
            <div class="metric-row">
                <span>KWH</span>
                <span>{{ $result['current']['kwh'] }}</span>
            </div>
        </div>
    </div>

    <div class="comp-card">
        <div class="comp-card-header header-scenario">Skenario</div>
        <div class="comp-card-body">
            <div class="metric-row">
                <span>Jam</span>
                <span>{{ $result['scenario']['hours'] }}</span>
            </div>
            <div class="metric-row">
                <span>KWH</span>
                <span>{{ $result['scenario']['kwh'] }}</span>
            </div>
        </div>
    </div>

</div>

{{-- PROGRESS --}}
<div class="saving-card">

    {{-- CURRENT --}}
    <div class="progress-wrap">
        <div class="progress-label">
            <span>Aktual</span>
            <span>100%</span>
        </div>
        <div class="progress-bar">
            <div class="progress-fill fill-current" style="width:100%"></div>
        </div>
    </div>

    {{-- SCENARIO --}}
    @php
        $pct = $result['current']['kwh'] > 0
            ? round(($result['scenario']['kwh'] / $result['current']['kwh']) * 100, 1)
            : 0;
    @endphp

    <div class="progress-wrap">
        <div class="progress-label">
            <span>Skenario</span>
            <span>{{ $pct }}%</span>
        </div>
        <div class="progress-bar">
            <div class="progress-fill fill-scenario"
                 style="width: {{ min($pct, 100) }}%;">
            </div>
        </div>
    </div>

</div>

</div>

@endsection