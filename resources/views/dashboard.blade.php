    @extends('layouts.app')

    @section('title', 'Dashboard')
    @section('meta-desc', 'VoltWise Energy Dashboard - Monitor your electricity consumption')

    @php
        /* ---- Budget calculations ---- */
        $budget      = auth()->user()->monthly_budget;         // null = not set
        $monthlyCost = $monthlyCost ?? 0;
        $pct         = ($budget && $budget > 0)
                        ? min(round(($monthlyCost / $budget) * 100, 1), 100)
                        : 0;
        $fillClass   = $pct >= 90 ? 'danger' : ($pct >= 70 ? 'warn' : '');
        $budgetFmt   = $budget ? 'Rp ' . number_format($budget, 0, ',', '.') : null;
        $usedFmt     = 'Rp ' . number_format($monthlyCost, 0, ',', '.');
    @endphp

    @php
        /* ---- Budget calculations ---- */
        $budget      = auth()->user()->monthly_budget;         // null = not set
        $monthlyCost = auth()->user()->devices()->sum('monthly_cost') ?? 0;                                    
        $pct         = ($budget && $budget > 0)
                    ? min(round(($monthlyCost / $budget) * 100, 1), 100)
                    : 0;
        $fillClass = ($pct <= 0)
            ? 'danger'
            : ($pct >= 90
            ? 'danger'
            : ($pct >= 75 ? 'warn' : 'safe'));
        $budgetFmt   = $budget ? 'Rp ' . number_format($budget, 0, ',', '.') : null;
        $usedFmt     = 'Rp ' . number_format($monthlyCost, 0, ',', '.');
    @endphp

    @section('styles')
    <style>
    /* ── Chart legend pills ── */
    .chart-legend { display:flex; flex-wrap:wrap; gap:0.5rem; align-items:center; }
    .legend-item  { display:flex; align-items:center; gap:0.3rem; font-size:0.72rem; color:var(--text-muted); }
    .legend-dot   { width:8px; height:8px; border-radius:50%; flex-shrink:0; }

    /* ── Consumers table ── */
    .consumers-table { width:100%; border-collapse:collapse; }
    .consumers-table thead th {
        font-size:0.72rem; font-weight:600; color:var(--text-muted);
        text-transform:uppercase; letter-spacing:0.06em;
        padding:0.4rem 0.75rem; border-bottom:1px solid var(--border); text-align:left;
    }
    .consumers-table tbody td {
        padding:0.6rem 0.75rem; font-size:0.8rem; color:var(--text-secondary);
        border-bottom:1px solid var(--border); vertical-align:middle;
    }
    .consumers-table tbody tr:last-child td { border-bottom:none; }
    .consumers-table tbody tr:hover td { background:rgba(74,124,246,0.04); }

    /* ── Device avatar ── */
    .dev-avatar {
        width:30px; height:30px; border-radius:50%;
        display:inline-flex; align-items:center; justify-content:center;
        font-size:0.65rem; font-weight:700; flex-shrink:0;
    }

    /* ── kWh progress bar ── */
    .kwh-bar-wrap { display:flex; align-items:center; gap:0.5rem; }
    .kwh-bar-bg   { flex:1; height:6px; background:var(--border); border-radius:3px; overflow:hidden; }
    .kwh-bar-fill { height:100%; border-radius:3px; }
    .kwh-bar-val  { font-size:0.75rem; font-weight:600; min-width:52px; color:var(--text-primary); }

    /* ── Status badges ── */
    .status-badge { display:inline-flex; align-items:center; padding:0.2rem 0.55rem; border-radius:20px; font-size:0.68rem; font-weight:600; }
    .status-active  { background:#d1fae5; color:#059669; }
    .status-standby { background:#fef9c3; color:#ca8a04; }
    .status-off     { background:#fee2e2; color:#dc2626; }
    [data-theme="dark"] .status-active  { background:rgba(5,150,105,0.2);  color:#6ee7b7; }
    [data-theme="dark"] .status-standby { background:rgba(202,138,4,0.2);  color:#fde047; }
    [data-theme="dark"] .status-off     { background:rgba(220,38,38,0.2);  color:#fca5a5; }
    [data-theme="dark"] .dev-avatar.av-blue   { background:rgba(74,124,246,0.2)!important; color:#93b4fb!important; }
    [data-theme="dark"] .dev-avatar.av-green  { background:rgba(16,185,129,0.2)!important; color:#6ee7b7!important; }
    [data-theme="dark"] .dev-avatar.av-purple { background:rgba(139,92,246,0.2)!important; color:#c4b5fd!important; }
    [data-theme="dark"] .dev-avatar.av-yellow { background:rgba(202,138,4,0.2)!important;  color:#fde047!important; }
    [data-theme="dark"] .dev-avatar.av-red    { background:rgba(220,38,38,0.2)!important;  color:#fca5a5!important; }

    /* ── Welcome banner close button ── */
    .welcome-banner { position:relative; }
    .welcome-close {
        position:absolute; top:0.65rem; right:0.75rem;
        width:26px; height:26px; border-radius:50%;
        border:none; background:transparent;
        cursor:pointer; font-size:1.1rem; line-height:1;
        color:var(--text-muted); display:flex; align-items:center; justify-content:center;
        transition:background 0.15s, color 0.15s;
    }
    .welcome-close:hover { background:var(--icon-btn-hover); color:var(--text-primary); }
    </style>
    @endsection

    @section('content')

        {{-- Page Header --}}
        <div class="page-header">
            <div>
                <h1 class="page-title">Energy Dashboard</h1>
                <p class="page-subtitle">Monitor your electricity consumption and savings</p>
            </div>
            <a href="{{ route('devices.create') }}" class="btn-primary" style="text-decoration:none;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Add Device
            </a>
        </div>

        {{-- Welcome Banner --}}
        <div class="welcome-banner" id="welcomeBanner">
            <div style="display:flex;align-items:center;justify-content:space-between;">
                <div class="welcome-top" style="margin-bottom:0;">
                    <div class="welcome-bolt">
                        <svg viewBox="0 0 24 24"><path d="M13 2L4.5 13.5H11L10 22L19.5 10.5H13L13 2Z"/></svg>
                    </div>
                    <div>
                        <div class="welcome-heading">Welcome to <span>VoltWise!</span></div>
                        <div class="welcome-user">Hi, {{ auth()->user()->name }}!</div>
                    </div>
                </div>
                <button class="welcome-close" id="welcomeToggle" title="Toggle banner" aria-label="Toggle banner" aria-expanded="true">
                    <svg id="bannerChevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;transition:transform 0.3s;">
                        <polyline points="18 15 12 9 6 15"/>
                    </svg>
                </button>
            </div>
            <div class="welcome-banner-body" id="welcomeBody">
                <p class="welcome-desc" style="margin-top:0.6rem;">
                    Get started by adding your electronic devices to begin monitoring your energy consumption and discover opportunities to save money.
                </p>
            <div class="welcome-features">
                <div class="welcome-feature">
                    <div class="wf-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="3"/>
                            <path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/>
                        </svg>
                    </div>
                    <div>
                        <div class="wf-title">Track Usage</div>
                        <div class="wf-desc">Monitor device consumption in real-time</div>
                    </div>
                </div>
                <div class="welcome-feature">
                    <div class="wf-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="1" x2="12" y2="23"/>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                        </svg>
                    </div>
                    <div>
                        <div class="wf-title">Save Money</div>
                        <div class="wf-desc">Get personalized energy-saving tips</div>
                    </div>
                </div>
                <div class="welcome-feature">
                    <div class="wf-icon green">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="wf-title">SDG 7 Support</div>
                        <div class="wf-desc">Contribute to sustainable energy goals</div>
                    </div>
                </div>
            </div>
            </div>
        </div>

        {{-- Stat Cards --}}
        <div class="stat-grid">
            <div class="card">
                <div class="card-body">
                    <div class="stat-header">
                        <span class="stat-label">Total Devices</span>
                        <div class="stat-icon icon-blue">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                            </svg>
                        </div>
                    </div>
                    <div class="stat-value">{{ $totalDevices }}</div>
                    <div class="stat-detail">Active devices monitored</div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="stat-header">
                        <span class="stat-label">Today's Energy</span>
                        <div class="stat-icon icon-green">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
                                <polyline points="17 6 23 6 23 12"/>
                            </svg>
                        </div>
                    </div>
                    <div class="stat-value">{{ number_format($todayEnergyKwh, 2) }} <small>kWh</small></div>
                    <div class="stat-detail">Energy consumed today</div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="stat-header">
                        <span class="stat-label">Today's Cost</span>
                        <div class="stat-icon icon-orange">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="1" x2="12" y2="23"/>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                            </svg>
                        </div>
                    </div>
                    <div class="stat-value">Rp.{{ number_format($todayCost, 0, ',', '.') }}</div>
                    <div class="stat-detail">Rp.{{ number_format($monthlyCost, 0, ',', '.') }}/month</div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="stat-header">
                        <span class="stat-label">Top Consumer</span>
                        <div class="stat-icon icon-purple">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                                <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                            </svg>
                        </div>
                    </div>
                    <div class="stat-value na">{{ $topConsumer ? $topConsumer->name : 'N/A' }}</div>
                    <div class="stat-detail">
                        @if($topConsumer)
                            {{ number_format($topConsumer->daily_energy_kwh, 2) }} kWh/day
                        @else
                            Highest energy usage
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Budget Tracker Card --}}
        <div class="card budget-card" id="budget-tracker-card">
            <div class="card-body">
                <div class="budget-card-inner">
                    <div class="budget-left">
                        {{-- Header row --}}
                        <div class="stat-header" style="margin-bottom:0.75rem">
                            <span class="stat-label">
                                <svg style="width:13px;height:13px;display:inline;vertical-align:-1px;margin-right:4px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="7" width="20" height="14" rx="2"/>
                                    <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                                </svg>
                                Monthly Budget
                            </span>
                            <div class="stat-icon icon-{{ $fillClass === 'danger' ? 'orange' : ($fillClass === 'warn' ? 'orange' : 'blue') }}">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="12" y1="1" x2="12" y2="23"/>
                                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                                </svg>
                            </div>
                        </div>

                        @if($budget)
                            {{-- Amounts --}}
                            <div class="budget-amounts">
                                <span class="budget-used">{{ $usedFmt }}</span>
                                <span class="budget-sep">/</span>
                                <span class="budget-total">{{ $budgetFmt }}</span>
                            </div>

                            {{-- Progress bar --}}
                            <div class="budget-track" role="progressbar" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100" aria-label="Budget usage">
                                <div class="budget-fill {{ $fillClass }}" id="budget-fill-bar" style="width:{{ $pct }}%"></div>
                            </div>

                            {{-- Meta info --}}
                            <div class="budget-meta">
                                <span class="budget-pct-badge {{ $fillClass }}" id="budget-pct-badge">{{ $pct }}%</span>
                                <span class="budget-pct-label">
                                    @if($fillClass === 'danger')
                                        ⚠️ Approaching or over budget!
                                    @elseif($fillClass === 'warn')
                                        🟡 Getting close to your limit
                                    @else
                                        ✅ Within budget
                                    @endif
                                </span>
                            </div>
                        @else
                            <div class="budget-no-set">No monthly budget set yet.</div>
                            <div class="budget-track">
                                <div class="budget-fill" style="width:0%"></div>
                            </div>
                            <div class="budget-meta">
                                <span class="budget-pct-label">Set a budget to start tracking your usage</span>
                            </div>
                        @endif
                    </div>

                    {{-- Right side: actions --}}
                    <div class="budget-right">
                        <div class="budget-actions">
                            <button class="btn-budget-set" id="open-budget-modal" type="button" aria-haspopup="dialog">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                                {{ $budget ? 'Edit Budget' : 'Set Budget' }}
                            </button>

                            @if($budget)
                            <form method="POST" action="{{ route('budget.clear') }}" style="margin:0">
                                @csrf
                                <button type="submit" class="btn-budget-clear"
                                        onclick="return confirm('Remove your monthly budget?')">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6l-1 14H6L5 6"/>
                                        <path d="M10 11v6M14 11v6"/>
                                        <path d="M9 6V4h6v2"/>
                                    </svg>
                                    Remove
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Chart Cards --}}
        <div class="chart-row">
            <div class="card chart-card">
                <div class="card-body" style="flex:1;display:flex;flex-direction:column;">
                    <div class="card-title">Top 5 Energy Consumers</div>
                    <div class="card-subtitle">Today's energy consumption by device</div>
                    @if($devices->isEmpty())
                        <div class="empty-state">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="18" rx="2"/>
                                <line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/>
                                <line x1="9" y1="3" x2="9" y2="21"/>
                            </svg>
                            <p>No devices added yet. Add some devices to see your consumption.</p>
                        </div>
                    @else
                        <div class="list-block" style="margin-top:1rem;">
                            @foreach($devices->sortByDesc('daily_energy_kwh')->take(5) as $device)
                                <div style="display:flex;justify-content:space-between;align-items:center;padding:0.75rem 0;border-bottom:1px solid var(--border);">
                                    <span>{{ $device->name }}</span>
                                    <span>{{ number_format($device->daily_energy_kwh, 2) }} kWh</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="card chart-card">
                <div class="card-body" style="flex:1;display:flex;flex-direction:column;">
                    <div class="card-title">Energy by Category</div>
                    <div class="card-subtitle">Distribution across device categories</div>
                    @if($energyByCategory->isEmpty())
                        <div class="empty-state">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21.21 15.89A10 10 0 1 1 8 2.83"/>
                                <path d="M22 12A10 10 0 0 0 12 2v10z"/>
                            </svg>
                            <p>No data available</p>
                        </div>
                    @else
                        <div class="list-block" style="margin-top:1rem;">
                            @foreach($energyByCategory as $category => $kwh)
                                <div style="display:flex;justify-content:space-between;align-items:center;padding:0.75rem 0;border-bottom:1px solid var(--border);">
                                    <span>{{ $category }}</span>
                                    <span>{{ number_format($kwh, 2) }} kWh</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- 7-Day Trend + Donut --}}
        <div class="chart-row" style="margin-bottom:1.5rem;">
            @if($devices->isNotEmpty())
            <div class="card chart-card" style="min-height:280px;">
                <div class="card-body" style="flex:1;display:flex;flex-direction:column;">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:0.5rem;margin-bottom:0.75rem;">
                        <div>
                            <div class="card-title">7-Day Energy Trend</div>
                            <div class="card-subtitle">Daily consumption per device (kWh)</div>
                        </div>
                        <div class="chart-legend" id="trendLegend"></div>
                    </div>
                    <div style="flex:1;position:relative;min-height:200px;">
                        <canvas id="energyLineChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="card chart-card" style="min-height:280px;">
                <div class="card-body" style="flex:1;display:flex;flex-direction:column;">
                    <div class="card-title">Energy Distribution</div>
                    <div class="card-subtitle">Share by device this week</div>
                    <div style="flex:1;position:relative;min-height:200px;">
                        <canvas id="energyDonutChart"></canvas>
                    </div>
                </div>
            </div>
            @else
            <div class="card chart-card">
                <div class="card-body" style="flex:1;display:flex;flex-direction:column;">
                    <div class="card-title">7-Day Energy Trend</div>
                    <div class="card-subtitle">Daily consumption per device (kWh)</div>
                    <div class="empty-state">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                        </svg>
                        <p>No devices added yet. Add some devices to see your trend.</p>
                    </div>
                </div>
            </div>
            <div class="card chart-card">
                <div class="card-body" style="flex:1;display:flex;flex-direction:column;">
                    <div class="card-title">Energy Distribution</div>
                    <div class="card-subtitle">Share by device this week</div>
                    <div class="empty-state">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21.21 15.89A10 10 0 1 1 8 2.83"/>
                            <path d="M22 12A10 10 0 0 0 12 2v10z"/>
                        </svg>
                        <p>No data available</p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- All Devices Table --}}
        @if($devices->isNotEmpty())
        @php
            $colors = ['#4A7CF6','#10b981','#8b5cf6','#f59e0b','#f87171','#06b6d4','#ec4899'];
            $avClasses = ['av-blue','av-green','av-purple','av-yellow','av-red','av-blue','av-green'];
            $avBgs     = ['#dbeafe','#d1fae5','#ede9fe','#fef9c3','#fee2e2','#cffafe','#fce7f3'];
            $maxKwh = $devices->max('daily_energy_kwh') ?: 1;
        @endphp
        <div class="card" style="margin-bottom:1.5rem;">
            <div class="card-body">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
                    <div>
                        <div class="card-title">All Devices</div>
                        <div class="card-subtitle">Device-level breakdown &middot; {{ now()->format('j F Y') }}</div>
                    </div>
                </div>
                <table class="consumers-table">
                    <thead>
                        <tr>
                            <th style="width:28px;">#</th>
                            <th>Device</th>
                            <th>Status</th>
                            <th style="min-width:160px;">Daily Usage</th>
                            <th>Power</th>
                            <th>Daily Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($devices as $i => $device)
                        @php
                            $ci = $i % count($colors);
                            $pctBar = $maxKwh > 0 ? round(($device->daily_energy_kwh / $maxKwh) * 100) : 0;
                            $initials = strtoupper(substr($device->name, 0, 2));
                            $cost = $device->daily_energy_kwh * 1444;
                        @endphp
                        <tr>
                            <td style="color:var(--text-faint);font-size:0.72rem;">{{ $i + 1 }}</td>
                            <td>
                                <div style="display:flex;align-items:center;gap:0.6rem;">
                                    <div class="dev-avatar {{ $avClasses[$ci] }}" style="background:{{ $avBgs[$ci] }};color:{{ $colors[$ci] }};">{{ $initials }}</div>
                                    <div>
                                        <div style="font-weight:600;font-size:0.8rem;color:var(--text-primary);">{{ $device->name }}</div>
                                        <div style="font-size:0.68rem;color:var(--text-faint);">{{ $device->category ?? 'Device' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="status-badge status-active">active</span></td>
                            <td>
                                <div class="kwh-bar-wrap">
                                    <div class="kwh-bar-bg"><div class="kwh-bar-fill" style="width:{{ $pctBar }}%;background:{{ $colors[$ci] }};"></div></div>
                                    <span class="kwh-bar-val">{{ number_format($device->daily_energy_kwh, 2) }} kWh</span>
                                </div>
                            </td>
                            <td style="font-weight:500;">{{ $device->watt ?? '-' }} W</td>
                            <td style="color:var(--icon-green-fg);font-weight:600;">Rp {{ number_format($cost, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <div class="card" style="margin-bottom:1.5rem;">
            <div class="card-body" style="display:flex;flex-direction:column;min-height:180px;">
                <div class="card-title">All Devices</div>
                <div class="card-subtitle" style="margin-bottom:0.75rem;">Device-level breakdown &middot; {{ now()->format('j F Y') }}</div>
                <div class="empty-state" style="flex:1;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                        <line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/>
                        <line x1="9" y1="3" x2="9" y2="21"/>
                    </svg>
                    <p>No devices added yet. Add some devices to see your energy breakdown.</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Energy Tips --}}
        <div class="card">
            <div class="card-body">
                <div class="card-title">Energy Saving Tips</div>
                <div class="card-subtitle" style="margin-bottom:1rem;">Simple actions to reduce your electricity bill</div>
                <div class="tips-grid">
                    <div class="tip-item">
                        <div class="tip-icon tip-icon-yellow">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="9" y1="18" x2="15" y2="18"/><line x1="10" y1="22" x2="14" y2="22"/>
                                <path d="M15.09 14c.18-.98.65-1.74 1.41-2.5A4.65 4.65 0 0 0 18 8 6 6 0 0 0 6 8c0 1 .23 2.23 1.5 3.5A4.61 4.61 0 0 1 8.91 14"/>
                            </svg>
                        </div>
                        <div>
                            <div class="tip-title">Switch to LED bulbs</div>
                            <div class="tip-desc">Save up to 75% on lighting costs</div>
                        </div>
                    </div>

                    <div class="tip-item">
                        <div class="tip-icon tip-icon-blue">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 16a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/>
                                <line x1="12" y1="2"  x2="12" y2="4"/><line x1="12" y1="20" x2="12" y2="22"/>
                                <line x1="4.93" y1="4.93" x2="6.34" y2="6.34"/>
                                <line x1="17.66" y1="17.66" x2="19.07" y2="19.07"/>
                                <line x1="2" y1="12" x2="4" y2="12"/><line x1="20" y1="12" x2="22" y2="12"/>
                                <line x1="4.93" y1="19.07" x2="6.34" y2="17.66"/>
                                <line x1="17.66" y1="6.34" x2="19.07" y2="4.93"/>
                            </svg>
                        </div>
                        <div>
                            <div class="tip-title">Use AC efficiently</div>
                            <div class="tip-desc">Set temperature to 24–26°C for optimal savings</div>
                        </div>
                    </div>

                    <div class="tip-item">
                        <div class="tip-icon tip-icon-orange">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                                <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                            </svg>
                        </div>
                        <div>
                            <div class="tip-title">Unplug idle devices</div>
                            <div class="tip-desc">Phantom power can cost $100+ per year</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== SET BUDGET MODAL ===== --}}
        <div class="modal-overlay" id="budget-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="budget-modal-title">
            <div class="modal" id="budget-modal">
                <div class="modal-header">
                    <span class="modal-title" id="budget-modal-title">
                        <svg style="width:15px;height:15px;display:inline;vertical-align:-2px;margin-right:6px;color:var(--blue-600)" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="1" x2="12" y2="23"/>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                        </svg>
                        {{ $budget ? 'Edit Monthly Budget' : 'Set Monthly Budget' }}
                    </span>
                    <button class="modal-close" id="close-budget-modal" type="button" aria-label="Close">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </button>
                </div>

                <p class="modal-desc">
                    Set a monthly energy cost limit to help you stay on track.
                    You'll see a visual indicator on your dashboard whenever your usage approaches the limit.
                </p>

                <form method="POST" action="{{ route('budget.update') }}" id="budget-form">
                    @csrf
                    <label for="monthly_budget" class="modal-label">Monthly Budget (Rupiah)</label>
                    <div class="input-with-prefix">
                        <span class="input-prefix">Rp</span>
                        <input
                            type="number"
                            id="monthly_budget"
                            name="monthly_budget"
                            class="input-budget"
                            placeholder="e.g. 500000"
                            min="0"
                            step="1000"
                            value="{{ old('monthly_budget', $budget ? intval($budget) : '') }}"
                            required
                            autocomplete="off"
                        >
                    </div>
                    <p class="modal-hint">Enter the maximum amount in Rupiah you want to spend on electricity per month.</p>

                    @error('monthly_budget')
                        <p style="color:#ef4444;font-size:0.75rem;margin-top:0.375rem;">{{ $message }}</p>
                    @enderror

                    <div class="modal-actions">
                        <button type="button" class="btn-modal-cancel" id="cancel-budget-modal">Cancel</button>
                        <button type="submit" class="btn-modal-save">Save Budget</button>
                    </div>
                </form>
            </div>
        </div>

    @endsection

    @section('scripts')
        <script>
            (function () {
                function initCharts() {
                    var isDark = document.documentElement.getAttribute('data-theme') === 'dark';
                    var gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';
                    var tickColor = isDark ? '#64748b' : '#94a3b8';

                    // Get dynamic devices data from backend
                    var devicesData = @json($devices->values());
                    var colors = ['#4A7CF6','#10b981','#8b5cf6','#f59e0b','#f87171','#06b6d4','#ec4899'];
                    
                    var lineDatasets = [];
                    var donutLabels = [];
                    var donutData = [];
                    var donutColors = [];

                    devicesData.forEach(function(device, index) {
                        var color = colors[index % colors.length];
                        var baseVal = device.daily_energy_kwh || 0;
                        
                        var trendData = [
                            baseVal * 0.9, 
                            baseVal * 1.05, 
                            baseVal * 0.95, 
                            baseVal * 1.0, 
                            baseVal * 1.1, 
                            baseVal * 0.85, 
                            baseVal
                        ];
                        
                        lineDatasets.push({
                            label: device.name,
                            data: trendData,
                            borderColor: color,
                            backgroundColor: index === 0 ? 'rgba(74,124,246,0.08)' : 'transparent',
                            fill: index === 0,
                            tension: 0.4,
                            pointRadius: 3,
                            borderWidth: 2
                        });

                        donutLabels.push(device.name);
                        donutData.push(baseVal);
                        donutColors.push(color);
                    });

                    // -- 7-Day Line Chart --
                    var lineEl = document.getElementById('energyLineChart');
                    if (lineEl) {
                        new Chart(lineEl, {
                            type: 'line',
                            data: {
                                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                                datasets: lineDatasets
                            },
                            options: {
                                responsive:true, maintainAspectRatio:false,
                                plugins: {
                                    legend: { display:false },
                                    tooltip: { mode:'index', intersect:false }
                                },
                                scales: {
                                    x: { grid:{ display:false }, ticks:{ color:tickColor, font:{ size:11 } } },
                                    y: { grid:{ color:gridColor }, beginAtZero:true, ticks:{ color:tickColor, font:{ size:11 }, callback: function(v){ return v.toFixed(1)+' kWh'; } } }
                                }
                            }
                        });
                    }

                    // -- Donut Chart --
                    var donutEl = document.getElementById('energyDonutChart');
                    if (donutEl) {
                        new Chart(donutEl, {
                            type: 'doughnut',
                            data: {
                                labels: donutLabels,
                                datasets: [{ data: donutData, backgroundColor: donutColors, borderWidth: 0, hoverOffset: 8 }]
                            },
                            options: {
                                responsive:true, maintainAspectRatio:false, cutout:'66%',
                                plugins: {
                                    legend: { position:'bottom', labels:{ color:tickColor, font:{ size:11 }, padding:12, boxWidth:10, usePointStyle:true } }
                                }
                            }
                        });
                    }

                    // -- Sparklines --
                    document.querySelectorAll('.spark-canvas').forEach(function(canvas) {
                        var vals  = canvas.getAttribute('data-vals').split(',').map(Number);
                        var color = canvas.getAttribute('data-color');
                        var ctx2  = canvas.getContext('2d');
                        var w = canvas.width, h = canvas.height;
                        var min = Math.min.apply(null, vals), max = Math.max.apply(null, vals);
                        var range = max - min || 1;
                        ctx2.clearRect(0,0,w,h);
                        ctx2.beginPath();
                        vals.forEach(function(v,i) {
                            var x = (i / (vals.length-1)) * (w-2) + 1;
                            var y = h - ((v - min) / range) * (h-6) - 3;
                            if (i === 0) { ctx2.moveTo(x,y); } else { ctx2.lineTo(x,y); }
                        });
                        ctx2.strokeStyle = color;
                        ctx2.lineWidth   = 1.8;
                        ctx2.lineJoin    = 'round';
                        ctx2.stroke();
                    });
                }

                if (typeof Chart === 'undefined') {
                    var s = document.createElement('script');
                    s.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js';
                    s.onload = initCharts;
                    document.head.appendChild(s);
                } else {
                    initCharts();
                }
            })();

            // -- Welcome banner toggle --
            (function(){
                var banner   = document.getElementById('welcomeBanner');
                var btn      = document.getElementById('welcomeToggle');
                var chevron  = document.getElementById('bannerChevron');
                var body     = document.getElementById('welcomeBody');
                var collapsed = localStorage.getItem('vw-banner-collapsed') === '1';

                function collapse() {
                    if (!body) return;
                    body.style.maxHeight = body.scrollHeight + 'px';
                    requestAnimationFrame(function(){
                        body.style.transition = 'max-height 0.35s ease, opacity 0.3s ease';
                        body.style.maxHeight  = '0';
                        body.style.opacity    = '0';
                        body.style.overflow   = 'hidden';
                    });
                    if (chevron) chevron.style.transform = 'rotate(180deg)';
                    if (btn) btn.setAttribute('aria-expanded', 'false');
                    localStorage.setItem('vw-banner-collapsed', '1');
                }

                function expand() {
                    if (!body) return;
                    body.style.transition = 'max-height 0.35s ease, opacity 0.3s ease';
                    body.style.maxHeight  = body.scrollHeight + 300 + 'px';
                    body.style.opacity    = '1';
                    if (chevron) chevron.style.transform = 'rotate(0deg)';
                    if (btn) btn.setAttribute('aria-expanded', 'true');
                    localStorage.setItem('vw-banner-collapsed', '0');
                    setTimeout(function(){ body.style.maxHeight = 'none'; }, 380);
                }

                if (body && collapsed) {
                    body.style.maxHeight = '0';
                    body.style.opacity   = '0';
                    body.style.overflow  = 'hidden';
                    if (chevron) chevron.style.transform = 'rotate(180deg)';
                    if (btn) btn.setAttribute('aria-expanded', 'false');
                }

                if (btn) {
                    btn.addEventListener('click', function(){
                        if (localStorage.getItem('vw-banner-collapsed') === '1') {
                            expand();
                        } else {
                            collapse();
                        }
                    });
                }
            })();

            // ---- Budget Modal ----
            const overlay     = document.getElementById('budget-modal-overlay');
            const openBtn     = document.getElementById('open-budget-modal');
            const closeBtn    = document.getElementById('close-budget-modal');
            const cancelBtn   = document.getElementById('cancel-budget-modal');
            const budgetInput = document.getElementById('monthly_budget');

            function openModal() {
                overlay.classList.add('open');
                setTimeout(() => budgetInput && budgetInput.focus(), 150);
                document.body.style.overflow = 'hidden';
            }
            function closeModal() {
                overlay.classList.remove('open');
                document.body.style.overflow = '';
            }

            openBtn   && openBtn.addEventListener('click', openModal);
            closeBtn  && closeBtn.addEventListener('click', closeModal);
            cancelBtn && cancelBtn.addEventListener('click', closeModal);

            // Close on overlay click (outside modal box)
            overlay && overlay.addEventListener('click', function(e) {
                if (e.target === overlay) closeModal();
            });

            // Close on Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && overlay.classList.contains('open')) closeModal();
            });

            // Auto-open modal if there's a validation error on the budget field
            @error('monthly_budget')
                openModal();
            @enderror

            // Animate the progress bar on load
            window.addEventListener('load', function () {
                const fill  = document.getElementById('budget-fill-bar');
                const badge = document.getElementById('budget-pct-badge');
                if (!fill) return;
                const target = parseFloat(fill.style.width) || 0;
                fill.style.width = '0%';
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        fill.style.width = target + '%';
                    });
                });
            });
        </script>
    @endsection