@extends('layouts.app')
@section('title', 'Admin Dashboard')

@section('content')

    <div class="page-header">
        <div>
            <h1 class="page-title">Admin Dashboard</h1>
            <p class="page-subtitle">System overview</p>
        </div>
        <a href="{{ route('admin.master-devices.create') }}" class="btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                 stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Add Device
        </a>
    </div>

    <div class="welcome-banner">
        <div class="welcome-top">
            <div class="welcome-bolt">
                <svg viewBox="0 0 24 24">
                    <path d="M13 2L4.5 13.5H11L10 22L19.5 10.5H13L13 2Z"/>
                </svg>
            </div>
            <div>
                <div class="welcome-heading">Welcome to <span>VoltWise</span> Admin!</div>
                <div class="welcome-user">Hi, {{ auth()->user()->name }}!</div>
            </div>
        </div>
        <p class="welcome-desc">
            Manage the master device library from this dashboard.
        </p>
    </div>

    <div class="stat-grid">
        <div class="card">
            <div class="card-body">
                <div class="stat-header">
                    <span class="stat-label">Master Devices</span>
                    <div class="stat-icon icon-blue">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                            <path d="M16 7V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v3"/>
                        </svg>
                    </div>
                </div>
                <div class="stat-value">{{ $totalDevices }}</div>
                <div class="stat-detail">Device templates in library</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="card-title">Recently Added Devices</div>
            <div class="card-subtitle" style="margin-bottom:1rem;">Latest master device templates</div>
            @if($recentDevices->count() > 0)
                <div style="display:flex; flex-direction:column; gap:0.5rem;">
                    @foreach($recentDevices as $device)
                        <div style="display:flex; align-items:center; justify-content:space-between;
                                    padding:0.5rem 0.75rem; background:var(--bg-tip);
                                    border-radius:8px; border:1px solid var(--border);">
                            <div>
                                <div style="font-size:0.8125rem; font-weight:600; color:var(--text-primary);">
                                    {{ $device->name }}
                                </div>
                                <div style="font-size:0.75rem; color:var(--text-muted);">
                                    {{ $device->category }} · {{ number_format($device->wattage, 0) }} W
                                </div>
                            </div>
                            <span class="badge badge-blue">{{ $device->created_at->diffForHumans() }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align:center; padding:2rem; color:var(--text-faint);">
                    No devices added yet. Click "Add Device" to create one.
                </div>
            @endif
        </div>
    </div>

    <div class="card" style="margin-top:1rem;">
        <div class="card-body">
            <div class="card-title">Quick Actions</div>
            <div class="card-subtitle" style="margin-bottom:1rem;">Common tasks</div>
            <div style="display:flex; gap:0.75rem;">
                <a href="{{ route('admin.master-devices.create') }}" class="btn-primary">Add Master Device</a>
                <a href="{{ route('admin.master-devices.index') }}" class="btn-secondary">View All Devices</a>
            </div>
        </div>
    </div>

@endsection
