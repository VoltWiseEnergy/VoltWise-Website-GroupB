@extends('layouts.app')

@section('title', 'Daily Tracker')

@section('content')

<style>
    .tracker-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1.5rem; }
    .tracker-header h2 { font-size:1.25rem; font-weight:600; color:var(--text-primary); margin-bottom:0.25rem; }
    .tracker-header p  { font-size:0.8rem; color:var(--text-muted); margin:0; }

    .btn-primary-vw {
        background:var(--blue-600); color:#fff; border:none; border-radius:8px;
        padding:0.45rem 1.1rem; font-size:0.8rem; font-weight:500; cursor:pointer;
        text-decoration:none; display:inline-block;
    }
    .btn-primary-vw:hover { background:var(--blue-700); color:#fff; }

    /* Empty state */
    .empty-card {
        background:var(--bg-card); border:1px solid var(--border);
        border-radius:12px; box-shadow:var(--shadow-card);
        text-align:center; padding:3rem 1rem;
    }
    .empty-card .icon { font-size:2.5rem; margin-bottom:0.75rem; }
    .empty-card h6 { font-size:0.9rem; font-weight:600; color:var(--text-primary); margin-bottom:0.4rem; }
    .empty-card p  { font-size:0.8rem; color:var(--text-muted); margin-bottom:1rem; }

    /* Device card */
    .device-card {
        background:var(--bg-card); border:1px solid var(--border);
        border-radius:12px; box-shadow:var(--shadow-card);
        margin-bottom:1rem; overflow:hidden;
    }
    .device-card-header {
        display:flex; justify-content:space-between; align-items:center;
        padding:1rem 1.25rem 0.75rem;
    }
    .device-name { font-size:0.95rem; font-weight:600; color:var(--text-primary); margin-bottom:0.2rem; }
    .device-meta { font-size:0.78rem; color:var(--text-muted); }
    .device-badge {
        background:var(--blue-100); color:var(--blue-600);
        font-size:0.7rem; border-radius:20px; padding:3px 12px; font-weight:500;
    }

    .device-card-body {
        display:grid; grid-template-columns:1fr 1fr;
        gap:1px; background:var(--border);
    }
    @media(max-width:600px) { .device-card-body { grid-template-columns:1fr; } }

    /* Default & override panels */
    .panel {
        padding:1rem 1.25rem;
        background:var(--bg-card);
    }
    .panel-default { background:var(--bg-card); }
    .panel-override { background:var(--bg-tip); }

    .panel-label {
        font-size:0.75rem; font-weight:600; margin-bottom:0.65rem;
        display:flex; align-items:center; gap:0.4rem;
    }
    .panel-label.blue   { color:var(--blue-600); }
    .panel-label.orange { color:#B45309; }

    .panel-label .dot {
        width:6px; height:6px; border-radius:50%; flex-shrink:0;
    }
    .dot-blue   { background:var(--blue-600); }
    .dot-orange { background:#F59E42; }

    .form-row { display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap; }

    .input-vw {
        background:var(--bg-base); color:var(--text-primary);
        border:1px solid var(--border); border-radius:7px;
        padding:0.4rem 0.6rem; font-size:0.8rem; outline:none;
        transition:border-color 0.2s;
    }
    .input-vw:focus { border-color:var(--blue-600); }
    .input-hours { width:68px; text-align:center; }
    .input-date  { width:145px; }

    .input-label { font-size:0.78rem; color:var(--text-muted); }

    .btn-save {
        background:var(--blue-600); color:#fff; border:none;
        border-radius:7px; padding:0.4rem 0.9rem;
        font-size:0.78rem; font-weight:500; cursor:pointer; margin-left:auto;
    }
    .btn-save:hover { background:var(--blue-700); }

    .btn-override {
        background:#F59E42; color:#fff; border:none;
        border-radius:7px; padding:0.4rem 0.9rem;
        font-size:0.78rem; font-weight:500; cursor:pointer;
    }
    .btn-override:hover { background:#D97706; }

    .status-saved    { font-size:0.75rem; color:#059669; margin-top:0.5rem; }
    .status-override { font-size:0.75rem; color:#B45309; margin-top:0.5rem; }
</style>

<div style="padding:1.5rem;">

    {{-- Header --}}
    <div class="tracker-header">
        <div>
            <h2>Daily Tracker</h2>
            <p>Set and manage your daily device usage — {{ now()->format('l, d F Y') }}</p>
        </div>
        <a href="{{ route('usage.history') }}" class="btn-primary-vw">View History</a>
    </div>

    @if($devices->isEmpty())
        <div class="empty-card">
            <div class="icon">📱</div>
            <h6>No devices added yet</h6>
            <p>Add some devices first to start tracking your usage.</p>
            <a href="{{ route('devices.create') }}" class="btn-primary-vw">Add Device</a>
        </div>
    @else
        @foreach($devices as $device)
        <div class="device-card">

            {{-- Device header --}}
            <div class="device-card-header">
                <div>
                    <div class="device-name">{{ $device->name }}</div>
                    <div class="device-meta">{{ $device->wattage }}W &middot; {{ $device->category }}</div>
                </div>
                <span class="device-badge">{{ $device->category }}</span>
            </div>

            {{-- Two panels --}}
            <div class="device-card-body">

                {{-- PBI #16 — Default Usage --}}
                <div class="panel panel-default">
                    <div class="panel-label blue">
                        <span class="dot dot-blue"></span>
                        Default Usage / Day
                    </div>
                    <form action="{{ route('usage.setDefault') }}" method="POST">
                        @csrf
                        <input type="hidden" name="device_id" value="{{ $device->id }}">
                        <div class="form-row">
                            <input
                                type="number" name="hours"
                                min="0" max="24" step="0.5"
                                value="{{ $todayUsage[$device->id]->hours ?? '' }}"
                                placeholder="0"
                                class="input-vw input-hours"
                                required
                            >
                            <span class="input-label">hours / day</span>
                            <button type="submit" class="btn-save">Save</button>
                        </div>
                    </form>
                    @if(isset($todayUsage[$device->id]) && !$todayUsage[$device->id]->is_override)
                        <div class="status-saved">✓ Saved — {{ $todayUsage[$device->id]->hours }} hrs today</div>
                    @endif
                </div>

                {{-- PBI #17 — Override --}}
                <div class="panel panel-override">
                    <div class="panel-label orange">
                        <span class="dot dot-orange"></span>
                        Override for Specific Date
                    </div>
                    <form action="{{ route('usage.override') }}" method="POST">
                        @csrf
                        <input type="hidden" name="device_id" value="{{ $device->id }}">
                        <div class="form-row">
                            <input
                                type="date" name="usage_date"
                                value="{{ now()->toDateString() }}"
                                class="input-vw input-date"
                                required
                            >
                            <input
                                type="number" name="hours"
                                min="0" max="24" step="0.5"
                                placeholder="0"
                                class="input-vw input-hours"
                                required
                            >
                            <span class="input-label">hrs</span>
                            <button type="submit" class="btn-override">Override</button>
                        </div>
                    </form>
                    @if(isset($todayUsage[$device->id]) && $todayUsage[$device->id]->is_override)
                        <div class="status-override">⚠ Override active — {{ $todayUsage[$device->id]->hours }} hrs</div>
                    @endif
                </div>

            </div>
        </div>
        @endforeach
    @endif

</div>
@endsection