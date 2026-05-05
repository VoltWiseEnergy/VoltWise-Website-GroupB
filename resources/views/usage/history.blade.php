@extends('layouts.app')

@section('title', 'Usage History')

@section('content')

<style>
    .usage-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1.5rem; }
    .usage-header h2 { font-size:1.25rem; font-weight:600; color:var(--text-primary); margin-bottom:0.25rem; }
    .usage-header p  { font-size:0.8rem; color:var(--text-muted); margin:0; }

    .btn-primary-vw {
        background:var(--blue-600); color:#fff; border:none; border-radius:8px;
        padding:0.45rem 1.1rem; font-size:0.8rem; font-weight:500; cursor:pointer;
        text-decoration:none; display:inline-block;
    }
    .btn-primary-vw:hover { background:var(--blue-700); color:#fff; }

    /* Filter card */
    .filter-card {
        background:var(--bg-card); border:1px solid var(--border);
        border-radius:12px; padding:1rem 1.25rem;
        box-shadow:var(--shadow-card); margin-bottom:1.25rem;
        display:flex; align-items:center; gap:1rem; flex-wrap:wrap;
    }
    .filter-group { display:flex; align-items:center; gap:0.5rem; }
    .filter-group label { font-size:0.78rem; color:var(--text-muted); white-space:nowrap; }
    .filter-group select {
        background:var(--bg-base); color:var(--text-primary);
        border:1px solid var(--border); border-radius:7px;
        padding:0.35rem 0.65rem; font-size:0.78rem; outline:none;
    }
    .btn-filter {
        background:var(--blue-600); color:#fff; border:none; border-radius:7px;
        padding:0.38rem 0.9rem; font-size:0.78rem; font-weight:500; cursor:pointer;
    }
    .btn-reset {
        background:transparent; color:var(--text-muted);
        border:1px solid var(--border); border-radius:7px;
        padding:0.38rem 0.9rem; font-size:0.78rem; cursor:pointer;
        text-decoration:none; display:inline-block;
    }
    .btn-reset:hover { color:var(--text-primary); }

    /* Summary grid */
    .summary-grid {
        display:grid; grid-template-columns:repeat(4, 1fr);
        gap:1rem; margin-bottom:1.25rem;
    }
    @media(max-width:640px) { .summary-grid { grid-template-columns:repeat(2,1fr); } }
    .summary-card {
        background:var(--bg-card); border:1px solid var(--border);
        border-radius:12px; padding:1rem; text-align:center;
        box-shadow:var(--shadow-card);
    }
    .summary-card .label { font-size:0.75rem; color:var(--text-muted); margin-bottom:0.35rem; }
    .summary-card .value { font-size:1.15rem; font-weight:600; color:var(--text-primary); }
    .summary-card .value.blue   { color:var(--blue-600); }
    .summary-card .value.orange { color:#B45309; }

    /* Table card */
    .table-card {
        background:var(--bg-card); border:1px solid var(--border);
        border-radius:12px; box-shadow:var(--shadow-card); overflow:hidden;
    }
    .usage-table { width:100%; border-collapse:collapse; }
    .usage-table thead tr { background:var(--bg-base); }
    .usage-table th {
        padding:0.75rem 1rem; font-size:0.75rem; font-weight:600;
        color:var(--text-muted); text-align:left; border-bottom:1px solid var(--border);
    }
    .usage-table td {
        padding:0.75rem 1rem; font-size:0.8rem; color:var(--text-primary);
        border-bottom:1px solid var(--border);
    }
    .usage-table tbody tr:last-child td { border-bottom:none; }
    .usage-table tbody tr:hover { background:var(--nav-hover-bg); }
    .td-muted  { color:var(--text-muted); }
    .td-blue   { color:var(--blue-600); font-weight:600; }
    .td-bold   { font-weight:600; }

    .badge-override {
        background:#FEF3C7; color:#B45309;
        font-size:0.68rem; border-radius:20px; padding:2px 10px; font-weight:500;
    }
    .badge-default {
        background:#D1FAE5; color:#065F46;
        font-size:0.68rem; border-radius:20px; padding:2px 10px; font-weight:500;
    }

    .empty-state { text-align:center; padding:3rem 1rem; }
    .empty-state .icon { font-size:2rem; margin-bottom:0.5rem; }
    .empty-state p { font-size:0.8rem; color:var(--text-muted); }
</style>

<div style="padding:1.5rem;">

    {{-- Header --}}
    <div class="usage-header">
        <div>
            <h2>Usage History</h2>
            <p>Your daily energy consumption records</p>
        </div>
        <a href="{{ route('usage.tracker') }}" class="btn-primary-vw">+ Set Usage</a>
    </div>

    {{-- Filter --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('usage.history') }}" style="display:flex; align-items:center; gap:1rem; flex-wrap:wrap;">
            <div class="filter-group">
                <label>Device</label>
                <select name="device_id">
                    <option value="">All Devices</option>
                    @foreach($devices as $device)
                        <option value="{{ $device->id }}" {{ request('device_id') == $device->id ? 'selected' : '' }}>
                            {{ $device->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label>Last</label>
                <select name="days">
                    <option value="7"  {{ request('days', 7) == 7  ? 'selected' : '' }}>7 days</option>
                    <option value="14" {{ request('days') == 14    ? 'selected' : '' }}>14 days</option>
                    <option value="30" {{ request('days') == 30    ? 'selected' : '' }}>30 days</option>
                </select>
            </div>
            <button type="submit" class="btn-filter">Filter</button>
            <a href="{{ route('usage.history') }}" class="btn-reset">Reset</a>
        </form>
    </div>

    {{-- Summary --}}
    <div class="summary-grid">
        <div class="summary-card">
            <div class="label">Total kWh</div>
            <div class="value blue">{{ number_format($logs->sum('kwh'), 2) }}</div>
        </div>
        <div class="summary-card">
            <div class="label">Total Records</div>
            <div class="value">{{ $logs->count() }}</div>
        </div>
        <div class="summary-card">
            <div class="label">Overrides</div>
            <div class="value orange">{{ $logs->where('is_override', true)->count() }}</div>
        </div>
        <div class="summary-card">
            <div class="label">Avg kWh/day</div>
            <div class="value">{{ $logs->count() ? number_format($logs->sum('kwh') / request('days', 7), 2) : '0.00' }}</div>
        </div>
    </div>

    {{-- Table --}}
    <div class="table-card">
        @if($logs->isEmpty())
            <div class="empty-state">
                <div class="icon">📋</div>
                <p>No usage records found.</p>
            </div>
        @else
            <table class="usage-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Device</th>
                        <th>Category</th>
                        <th>Hours</th>
                        <th>kWh</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr>
                        <td class="td-muted">{{ $log->usage_date->format('d M Y') }}</td>
                        <td class="td-bold">{{ $log->device_name }}</td>
                        <td class="td-muted">{{ $log->device->category ?? '-' }}</td>
                        <td>{{ $log->hours }} hrs</td>
                        <td class="td-blue">{{ number_format($log->kwh, 3) }}</td>
                        <td>
                            @if($log->is_override)
                                <span class="badge-override">override</span>
                            @else
                                <span class="badge-default">default</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

</div>
@endsection