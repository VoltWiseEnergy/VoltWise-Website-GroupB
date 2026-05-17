@extends('layouts.app')
@section('title', 'Smart Recommendations')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">Smart Recommendations</h1>
        <p class="page-subtitle">Based on your last 30 days of energy usage</p>
    </div>
</div>

{{-- Summary Cards --}}
<div class="rec-summary-grid">
    <div class="rec-stat-card">
        <div class="rec-stat-label">Total Usage (30d)</div>
        <div class="rec-stat-value">{{ $patterns['total_kwh'] }} <span class="rec-stat-unit">kWh</span></div>
    </div>
    <div class="rec-stat-card">
        <div class="rec-stat-label">Daily Average</div>
        <div class="rec-stat-value">{{ $patterns['daily_avg_kwh'] }} <span class="rec-stat-unit">kWh/day</span></div>
    </div>
    <div class="rec-stat-card">
        <div class="rec-stat-label">Top Consumer</div>
        <div class="rec-stat-value" style="font-size:1.1rem;">
            {{ $patterns['most_consuming']['device_name'] ?? 'No data' }}
            @if($patterns['most_consuming'])
                <span class="rec-stat-unit">{{ $patterns['most_consuming']['total_kwh'] }} kWh</span>
            @endif
        </div>
    </div>
</div>

{{-- High Usage Devices --}}
<div class="card" style="margin-top:1rem;">
    <div class="card-body">
        <div class="rec-section-header">
            <div class="rec-section-icon icon-red">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
                </svg>
            </div>
            <div>
                <div class="card-title">High Usage Devices</div>
                <div class="card-subtitle">Devices consuming 1.5x above average</div>
            </div>
        </div>

        @if($patterns['high_usage_devices']->isEmpty())
            <div class="rec-empty">No high usage devices detected. Great job!</div>
        @else
            <div class="rec-list">
                @foreach($patterns['high_usage_devices'] as $device)
                <div class="rec-item">
                    <div class="rec-item-name">{{ $device['device_name'] }}</div>
                    <div class="rec-item-meta">
                        <span class="rec-badge badge-red">{{ $device['avg_kwh'] }} kWh/day avg</span>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- Always On Devices --}}
<div class="card" style="margin-top:1rem;">
    <div class="card-body">
        <div class="rec-section-header">
            <div class="rec-section-icon icon-orange">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
            <div>
                <div class="card-title">Always-On Devices</div>
                <div class="card-subtitle">Active 6+ days/week with average usage above 8 hours</div>
            </div>
        </div>

        @if($patterns['always_on_devices']->isEmpty())
            <div class="rec-empty">No always-on devices detected.</div>
        @else
            <div class="rec-list">
                @foreach($patterns['always_on_devices'] as $device)
                <div class="rec-item">
                    <div class="rec-item-name">{{ $device['device_name'] }}</div>
                    <div class="rec-item-meta">
                        <span class="rec-badge badge-orange">{{ $device['days_active'] }} days active</span>
                        <span class="rec-badge badge-orange">{{ $device['avg_hours'] }}h avg/day</span>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- Spike Days --}}
<div class="card" style="margin-top:1rem;">
    <div class="card-body">
        <div class="rec-section-header">
            <div class="rec-section-icon icon-purple">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                </svg>
            </div>
            <div>
                <div class="card-title">Spike Days</div>
                <div class="card-subtitle">Days with consumption 2x above your daily average</div>
            </div>
        </div>

        @if($patterns['spike_days']->isEmpty())
            <div class="rec-empty">No spike days detected.</div>
        @else
            <div class="rec-list">
                @foreach($patterns['spike_days'] as $spike)
                <div class="rec-item">
                    <div class="rec-item-name">{{ \Carbon\Carbon::parse($spike['date'])->format('d F Y') }}</div>
                    <div class="rec-item-meta">
                        <span class="rec-badge badge-purple">{{ $spike['total_kwh'] }} kWh</span>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

@endsection

@section('styles')
<style>
    .rec-summary-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }
    .rec-stat-card {
        background: var(--bg-card); border: 1px solid var(--border);
        border-radius: 10px; padding: 1.25rem;
    }
    .rec-stat-label { font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.5rem; }
    .rec-stat-value { font-size: 1.5rem; font-weight: 700; color: var(--text-primary); }
    .rec-stat-unit { font-size: 0.8rem; font-weight: 400; color: var(--text-muted); }

    .rec-section-header { display: flex; align-items: center; gap: 0.875rem; margin-bottom: 1rem; }
    .rec-section-icon { width: 38px; height: 38px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .rec-section-icon svg { width: 18px; height: 18px; }

    .rec-list { display: flex; flex-direction: column; gap: 0.5rem; }
    .rec-item {
        display: flex; align-items: center; justify-content: space-between;
        padding: 0.75rem 1rem; background: var(--bg-base);
        border: 1px solid var(--border); border-radius: 8px;
    }
    .rec-item-name { font-size: 0.875rem; font-weight: 600; color: var(--text-primary); }
    .rec-item-meta { display: flex; gap: 0.5rem; }

    .rec-badge {
        font-size: 0.7rem; font-weight: 600; padding: 0.2rem 0.6rem;
        border-radius: 999px;
    }
    .badge-red    { background: var(--red-100);    color: var(--red-700); }
    .badge-orange { background: var(--orange-100); color: var(--orange-700); }
    .badge-purple { background: var(--purple-100); color: var(--purple-700); }

    .rec-empty { font-size: 0.875rem; color: var(--text-muted); padding: 0.75rem 0; }

    @media (max-width: 600px) { .rec-summary-grid { grid-template-columns: 1fr; } }
</style>
@endsection