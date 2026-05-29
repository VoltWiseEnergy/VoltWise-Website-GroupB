@extends('layouts.app')
@section('title', 'Smart Recommendations')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">Smart Recommendations</h1>
        <p class="page-subtitle">Based on your last 30 days of energy usage</p>
    </div>
    <div class="rec-count-badge">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        {{ $patterns['recommendations']->count() }} recommendation{{ $patterns['recommendations']->count() !== 1 ? 's' : '' }}
    </div>
</div>

{{-- Top Row: Summary + Energy Score --}}
<div class="rec-top-grid">
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
        <div class="rec-stat-card rec-stat-card-highlight">
            <div class="rec-stat-label">Issues Found</div>
            <div class="rec-stat-value" style="color: var(--icon-blue-fg);">
                {{ $patterns['high_usage_devices']->count() + $patterns['always_on_devices']->count() + $patterns['spike_days']->count() }}
                <span class="rec-stat-unit">patterns</span>
            </div>
        </div>
    </div>

    <div class="energy-score-card card">
        <div class="card-body" style="display:flex; flex-direction:column; align-items:center; justify-content:center; height:100%; gap:0.75rem;">
            <div class="energy-score-label">Energy Score</div>
            <div class="energy-score-circle score-{{ $score['color'] }}">
                <span class="energy-score-number">{{ $score['score'] }}</span>
                <span class="energy-score-total">/100</span>
            </div>
            <div class="energy-score-grade grade-{{ $score['color'] }}">
                {{ $score['grade'] }} — {{ $score['label'] }}
            </div>
            <div class="energy-score-bar-wrap">
                <div class="energy-score-bar">
                    <div class="energy-score-fill score-fill-{{ $score['color'] }}" style="width: {{ $score['score'] }}%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Recommendation Checklist --}}
<div class="card" style="margin-top:1rem;">
    <div class="card-body">
        <div class="rec-section-header">
            <div class="rec-section-icon icon-blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 11 12 14 22 4"/>
                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                </svg>
            </div>
            <div style="flex:1;">
                <div class="card-title">Personalized Recommendations</div>
                <div class="card-subtitle">Check off actions you've taken</div>
            </div>
            @php
                $total    = $patterns['recommendations']->count();
                $done     = $patterns['recommendations']->filter(fn($r) => $checks->get($r['type'] . '_' . ($r['device'] ?? 'general')))->count();
                $progress = $total > 0 ? round(($done / $total) * 100) : 0;
            @endphp
            <div class="checklist-progress-wrap">
                <div class="checklist-progress-label">{{ $done }}/{{ $total }} done</div>
                <div class="checklist-progress-bar">
                    <div class="checklist-progress-fill" style="width: {{ $progress }}%"></div>
                </div>
            </div>
        </div>

        <div class="rec-list">
            @foreach($patterns['recommendations'] as $rec)
            @php
                $key       = $rec['type'] . '_' . ($rec['device'] ?? 'general');
                $isChecked = $checks->get($key, false);
            @endphp
            <div class="rec-card rec-card-{{ $rec['priority'] }} {{ $isChecked ? 'rec-card-done' : '' }}" id="card-{{ md5($key) }}">
                <button class="rec-checkbox {{ $isChecked ? 'checked' : '' }}"
                        onclick="toggleCheck('{{ $key }}', '{{ md5($key) }}')"
                        title="{{ $isChecked ? 'Mark as undone' : 'Mark as done' }}">
                    @if($isChecked)
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                    @endif
                </button>
                <div class="rec-card-icon rec-icon-{{ $rec['priority'] }}">
                    @if($rec['icon'] === 'zap')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                    @elseif($rec['icon'] === 'clock')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    @elseif($rec['icon'] === 'activity')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    @elseif($rec['icon'] === 'trending-down')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 18 13.5 8.5 8.5 13.5 1 6"/><polyline points="17 18 23 18 23 12"/></svg>
                    @else
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    @endif
                </div>
                <div class="rec-card-body">
                    <div class="rec-card-title {{ $isChecked ? 'strikethrough' : '' }}">{{ $rec['title'] }}</div>
                    <div class="rec-card-message">{{ $rec['message'] }}</div>
                    <div class="rec-card-tip">
                        @if($rec['type'] === 'high_usage') 💡 Try setting a daily usage limit or use a smart plug timer.
                        @elseif($rec['type'] === 'always_on') 💡 A smart plug with scheduling can automate this for you.
                        @elseif($rec['type'] === 'spike') 💡 Check your Daily Tracker for that date to identify the cause.
                        @else 💡 Small changes across all devices add up to big savings over time.
                        @endif
                    </div>
                </div>
                <div class="rec-priority-badge badge-{{ $rec['priority'] }}">
                    @if($rec['priority'] === 'high')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:10px;height:10px;"><polyline points="18 15 12 9 6 15"/></svg>
                    @elseif($rec['priority'] === 'medium')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:10px;height:10px;"><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    @else
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:10px;height:10px;"><polyline points="18 9 12 15 6 9"/></svg>
                    @endif
                    {{ ucfirst($rec['priority']) }}
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- What-if Simulator --}}
<div class="card" style="margin-top:1rem;">
    <div class="card-body">
        <div class="rec-section-header">
            <div class="rec-section-icon icon-purple">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                </svg>
            </div>
            <div>
                <div class="card-title">What-if Simulator</div>
                <div class="card-subtitle">See how much you'd save by reducing device usage</div>
            </div>
        </div>

        <div class="sim-form">
            <div class="sim-form-group">
                <label class="profile-label">Select Device</label>
                <select id="sim-device" class="sim-select" onchange="updateSimulator()">
                    <option value="">-- Choose a device --</option>
                    @foreach($devices as $device)
                    <option value="{{ $device['wattage'] }}"
                            data-tariff="{{ $device['tariff'] }}"
                            data-hours="{{ $device['avg_hours'] }}"
                            data-name="{{ $device['name'] }}">
                        {{ $device['name'] }} ({{ $device['wattage'] }}W)
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="sim-form-group">
                <label class="profile-label">Current daily usage: <span id="sim-current-hours">—</span> hrs</label>
                <label class="profile-label" style="margin-top:0.75rem;">Reduce by <span id="sim-reduce-val">0</span> hours/day</label>
                <input type="range" id="sim-slider" min="0" max="24" value="0" step="0.5"
                       oninput="updateSimulator()" disabled style="width:100%; margin-top:0.375rem;">
            </div>
        </div>

        <div class="sim-results" id="sim-results" style="display:none;">
            <div class="sim-result-grid">
                <div class="sim-result-card">
                    <div class="sim-result-label">Current Monthly Cost</div>
                    <div class="sim-result-value" id="sim-current-cost">—</div>
                </div>
                <div class="sim-result-card sim-result-arrow">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"/>
                        <polyline points="12 5 19 12 12 19"/>
                    </svg>
                </div>
                <div class="sim-result-card sim-result-card-new">
                    <div class="sim-result-label">Projected Monthly Cost</div>
                    <div class="sim-result-value" id="sim-new-cost">—</div>
                </div>
            </div>
            <div class="sim-saving-banner" id="sim-saving-banner">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="1" x2="12" y2="23"/>
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
                <span>You could save <strong id="sim-saving">Rp 0</strong>/month by reducing <span id="sim-device-name">this device</span> by <span id="sim-reduce-label">0</span> hours/day</span>
            </div>
            <div class="sim-kwh-row">
                <span class="sim-kwh-label">kWh saved/month:</span>
                <span class="sim-kwh-value" id="sim-kwh-saved">0 kWh</span>
            </div>
        </div>

        <div class="sim-empty" id="sim-empty">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            Select a device above to start simulating savings
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
            <div class="rec-empty">✅ No high usage devices detected. Great job!</div>
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
            <div class="rec-empty">✅ No always-on devices detected.</div>
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
            <div class="rec-empty">✅ No spike days detected.</div>
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
    .rec-count-badge {
        display: inline-flex; align-items: center; gap: 0.375rem;
        padding: 0.4rem 0.875rem; border-radius: 999px;
        background: var(--icon-blue-bg); color: var(--icon-blue-fg);
        font-size: 0.8125rem; font-weight: 600; border: 1px solid var(--blue-200);
    }
    .rec-count-badge svg { width: 14px; height: 14px; }

    .rec-top-grid { display: grid; grid-template-columns: 1fr 220px; gap: 1rem; align-items: start; }
    .rec-summary-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; }
    .rec-stat-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 10px; padding: 1.25rem; }
    .rec-stat-card-highlight { border-color: var(--blue-200); background: var(--icon-blue-bg); }
    .rec-stat-label { font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.5rem; }
    .rec-stat-value { font-size: 1.5rem; font-weight: 700; color: var(--text-primary); }
    .rec-stat-unit { font-size: 0.8rem; font-weight: 400; color: var(--text-muted); }

    .energy-score-card { height: 100%; }
    .energy-score-label { font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; }
    .energy-score-circle { width: 90px; height: 90px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-direction: column; border: 4px solid; transition: all 0.3s; }
    .energy-score-number { font-size: 1.75rem; font-weight: 700; line-height: 1; }
    .energy-score-total  { font-size: 0.65rem; color: var(--text-muted); }
    .score-green  { border-color: #10b981; color: #10b981; }
    .score-blue   { border-color: var(--blue-600); color: var(--blue-600); }
    .score-orange { border-color: #f59e0b; color: #f59e0b; }
    .score-red    { border-color: #ef4444; color: #ef4444; }
    .energy-score-grade { font-size: 0.8125rem; font-weight: 600; }
    .grade-green  { color: #10b981; }
    .grade-blue   { color: var(--blue-600); }
    .grade-orange { color: #f59e0b; }
    .grade-red    { color: #ef4444; }
    .energy-score-bar-wrap { width: 100%; }
    .energy-score-bar { width: 100%; height: 6px; background: var(--border); border-radius: 99px; overflow: hidden; }
    .energy-score-fill { height: 100%; border-radius: 99px; transition: width 0.8s cubic-bezier(.4,0,.2,1); }
    .score-fill-green  { background: #10b981; }
    .score-fill-blue   { background: var(--blue-600); }
    .score-fill-orange { background: #f59e0b; }
    .score-fill-red    { background: #ef4444; }

    .checklist-progress-wrap { display: flex; flex-direction: column; align-items: flex-end; gap: 4px; min-width: 120px; }
    .checklist-progress-label { font-size: 0.75rem; color: var(--text-muted); font-weight: 500; }
    .checklist-progress-bar { width: 120px; height: 6px; background: var(--border); border-radius: 99px; overflow: hidden; }
    .checklist-progress-fill { height: 100%; background: var(--blue-600); border-radius: 99px; transition: width 0.4s ease; }

    .rec-section-header { display: flex; align-items: center; gap: 0.875rem; margin-bottom: 1rem; }
    .rec-section-icon { width: 38px; height: 38px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .rec-section-icon svg { width: 18px; height: 18px; }

    .rec-list { display: flex; flex-direction: column; gap: 0.5rem; }

    .rec-card { display: flex; align-items: flex-start; gap: 1rem; padding: 1rem 1.25rem; border-radius: 10px; background: var(--bg-base); border: 1px solid var(--border); border-left: 4px solid transparent; transition: opacity 0.3s, background 0.3s; }
    .rec-card-high   { border-left-color: #ef4444; }
    .rec-card-medium { border-left-color: #f59e0b; }
    .rec-card-low    { border-left-color: #10b981; }
    .rec-card-done   { opacity: 0.55; }

    .rec-checkbox { width: 22px; height: 22px; border-radius: 6px; flex-shrink: 0; border: 2px solid var(--border); background: var(--bg-card); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; margin-top: 1px; }
    .rec-checkbox:hover { border-color: var(--blue-600); }
    .rec-checkbox.checked { background: var(--blue-600); border-color: var(--blue-600); }
    .rec-checkbox svg { width: 12px; height: 12px; stroke: white; }

    .rec-card-icon { width: 36px; height: 36px; border-radius: 8px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; }
    .rec-card-icon svg { width: 16px; height: 16px; }
    .rec-icon-high   { background: var(--red-100);    color: var(--red-700); }
    .rec-icon-medium { background: var(--orange-100); color: var(--orange-700); }
    .rec-icon-low    { background: var(--icon-green-bg); color: var(--icon-green-fg); }

    .rec-card-body { flex: 1; }
    .rec-card-title { font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.25rem; transition: all 0.2s; }
    .rec-card-title.strikethrough { text-decoration: line-through; color: var(--text-faint); }
    .rec-card-message { font-size: 0.8125rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 0.5rem; }
    .rec-card-tip { font-size: 0.75rem; color: var(--text-faint); font-style: italic; }

    .rec-priority-badge { display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.7rem; font-weight: 600; padding: 0.2rem 0.6rem; border-radius: 999px; white-space: nowrap; align-self: flex-start; }
    .badge-high   { background: var(--red-100);    color: var(--red-700); }
    .badge-medium { background: var(--orange-100); color: var(--orange-700); }
    .badge-low    { background: var(--icon-green-bg); color: var(--icon-green-fg); }

    .rec-item { display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 1rem; background: var(--bg-base); border: 1px solid var(--border); border-radius: 8px; }
    .rec-item-name { font-size: 0.875rem; font-weight: 600; color: var(--text-primary); }
    .rec-item-meta { display: flex; gap: 0.5rem; }

    .rec-badge { font-size: 0.7rem; font-weight: 600; padding: 0.2rem 0.6rem; border-radius: 999px; }
    .badge-red    { background: var(--red-100);    color: var(--red-700); }
    .badge-orange { background: var(--orange-100); color: var(--orange-700); }
    .badge-purple { background: var(--purple-100); color: var(--purple-700); }

    .rec-empty { font-size: 0.875rem; color: var(--text-muted); padding: 0.75rem 0; }

    /* Simulator */
    .sim-form { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem; }
    .sim-form-group { display: flex; flex-direction: column; gap: 0.375rem; }
    .sim-select { padding: 0.5625rem 0.875rem; border: 1px solid var(--border); border-radius: 8px; background: var(--bg-base); color: var(--text-primary); font-size: 0.875rem; font-family: 'Inter', sans-serif; outline: none; width: 100%; transition: border-color 0.15s, box-shadow 0.15s; }
    .sim-select:focus { border-color: var(--blue-600); box-shadow: 0 0 0 3px rgba(74,124,246,0.12); }

    .sim-results { margin-top: 1rem; }
    .sim-result-grid { display: grid; grid-template-columns: 1fr auto 1fr; gap: 1rem; align-items: center; margin-bottom: 1rem; }
    .sim-result-card { background: var(--bg-base); border: 1px solid var(--border); border-radius: 10px; padding: 1rem 1.25rem; text-align: center; }
    .sim-result-card-new { border-color: #10b981; background: rgba(16,185,129,0.05); }
    .sim-result-arrow { background: none; border: none; display: flex; align-items: center; justify-content: center; color: var(--text-muted); }
    .sim-result-arrow svg { width: 20px; height: 20px; }
    .sim-result-label { font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.375rem; }
    .sim-result-value { font-size: 1.25rem; font-weight: 700; color: var(--text-primary); }

    .sim-saving-banner { display: flex; align-items: center; gap: 0.75rem; padding: 0.875rem 1rem; border-radius: 10px; background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.3); color: #065f46; font-size: 0.875rem; margin-bottom: 0.75rem; }
    .sim-saving-banner svg { width: 18px; height: 18px; flex-shrink: 0; stroke: #10b981; }
    [data-theme="dark"] .sim-saving-banner { color: #6ee7b7; }

    .sim-kwh-row { display: flex; align-items: center; justify-content: space-between; font-size: 0.8125rem; color: var(--text-muted); }
    .sim-kwh-value { font-weight: 600; color: #10b981; }

    .sim-empty { display: flex; align-items: center; gap: 0.5rem; padding: 1.5rem; border-radius: 10px; background: var(--bg-base); border: 1px dashed var(--border); color: var(--text-faint); font-size: 0.875rem; justify-content: center; }
    .sim-empty svg { width: 16px; height: 16px; }

    @media (max-width: 900px) { .rec-top-grid { grid-template-columns: 1fr; } .rec-summary-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 600px) { .rec-summary-grid { grid-template-columns: 1fr; } .sim-form { grid-template-columns: 1fr; } .sim-result-grid { grid-template-columns: 1fr; } }
</style>
@endsection

@section('scripts')
<script>
function toggleCheck(key, hash) {
    fetch('{{ route("recommendations.toggle") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ key: key })
    })
    .then(res => res.json())
    .then(() => {
        const card     = document.getElementById('card-' + hash);
        const checkbox = card.querySelector('.rec-checkbox');
        const title    = card.querySelector('.rec-card-title');
        const isNowChecked = !checkbox.classList.contains('checked');
        checkbox.classList.toggle('checked');
        checkbox.innerHTML = isNowChecked
            ? `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>`
            : '';
        card.classList.toggle('rec-card-done', isNowChecked);
        title.classList.toggle('strikethrough', isNowChecked);
        updateProgress();
    });
}

function updateProgress() {
    const total = document.querySelectorAll('.rec-card').length;
    const done  = document.querySelectorAll('.rec-checkbox.checked').length;
    const pct   = total > 0 ? Math.round((done / total) * 100) : 0;
    document.querySelector('.checklist-progress-fill').style.width = pct + '%';
    document.querySelector('.checklist-progress-label').textContent = done + '/' + total + ' done';
}

function updateSimulator() {
    const select  = document.getElementById('sim-device');
    const slider  = document.getElementById('sim-slider');
    const results = document.getElementById('sim-results');
    const empty   = document.getElementById('sim-empty');

    if (!select.value) {
        results.style.display = 'none';
        empty.style.display   = 'flex';
        slider.disabled = true;
        return;
    }

    const opt     = select.options[select.selectedIndex];
    const wattage = parseFloat(select.value);
    const tariff  = parseFloat(opt.dataset.tariff);
    const curHrs  = parseFloat(opt.dataset.hours) || 8;
    const name    = opt.dataset.name;

    slider.max      = Math.min(curHrs, 24);
    slider.disabled = false;

    const reduceHrs   = parseFloat(slider.value);
    const newHrs      = Math.max(0, curHrs - reduceHrs);
    const curKwhMonth = (wattage * curHrs / 1000) * 30;
    const newKwhMonth = (wattage * newHrs / 1000) * 30;
    const savedKwh    = curKwhMonth - newKwhMonth;
    const curCost     = curKwhMonth * tariff;
    const newCost     = newKwhMonth * tariff;
    const savedCost   = curCost - newCost;

    document.getElementById('sim-current-hours').textContent = curHrs;
    document.getElementById('sim-reduce-val').textContent    = reduceHrs;
    document.getElementById('sim-current-cost').textContent  = 'Rp ' + Math.round(curCost).toLocaleString('id-ID');
    document.getElementById('sim-new-cost').textContent      = 'Rp ' + Math.round(newCost).toLocaleString('id-ID');
    document.getElementById('sim-saving').textContent        = 'Rp ' + Math.round(savedCost).toLocaleString('id-ID');
    document.getElementById('sim-device-name').textContent   = name;
    document.getElementById('sim-reduce-label').textContent  = reduceHrs;
    document.getElementById('sim-kwh-saved').textContent     = Math.round(savedKwh * 100) / 100 + ' kWh';

    results.style.display = 'block';
    empty.style.display   = 'none';
}
</script>
@endsection