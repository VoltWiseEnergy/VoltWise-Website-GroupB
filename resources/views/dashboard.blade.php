@extends('layouts.app')

@section('title', 'Dashboard')
@section('meta-desc', 'VoltWise Energy Dashboard - Monitor your electricity consumption')

@section('content')

    {{-- Page Header --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Energy Dashboard</h1>
            <p class="page-subtitle">Monitor your electricity consumption and savings</p>
        </div>
        <button class="btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Add Device
        </button>
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
                <div class="stat-value">Rp.0</div>
                <div class="stat-detail">Rp.0/month</div>
                <div style="margin-top:0.4rem;font-size:0.65rem;color:var(--text-faint);font-family:monospace;">W &times; h &divide; 1000 = kWh &rarr; cost</div>
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

    {{-- Energy Consumers Table --}}
    @if($hasDevices)
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
                        <th>Monthly Cost</th>
                        <th>7-Day Trend</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="color:var(--text-faint);font-size:0.72rem;">1</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:0.6rem;">
                                <div class="dev-avatar av-blue" style="background:#dbeafe;color:#4A7CF6;">AC</div>
                                <div>
                                    <div style="font-weight:600;font-size:0.8rem;color:var(--text-primary);">Air Conditioner</div>
                                    <div style="font-size:0.68rem;color:var(--text-faint);">Climate &middot; Living Room</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="status-badge status-active">active</span></td>
                        <td><div class="kwh-bar-wrap"><div class="kwh-bar-bg"><div class="kwh-bar-fill" style="width:78%;background:#4A7CF6;"></div></div><span class="kwh-bar-val">2.40 kWh</span></div></td>
                        <td style="font-weight:500;">900 W</td>
                        <td style="color:var(--icon-green-fg);font-weight:600;">Rp 25,920</td>
                        <td><canvas class="spark-canvas" data-vals="1.8,2.1,2.3,2.0,2.5,2.2,2.4" data-color="#4A7CF6" width="64" height="28"></canvas></td>
                    </tr>
                    <tr>
                        <td style="color:var(--text-faint);font-size:0.72rem;">2</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:0.6rem;">
                                <div class="dev-avatar av-green" style="background:#d1fae5;color:#10b981;">WH</div>
                                <div>
                                    <div style="font-weight:600;font-size:0.8rem;color:var(--text-primary);">Water Heater</div>
                                    <div style="font-size:0.68rem;color:var(--text-faint);">Appliance &middot; Bathroom</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="status-badge status-active">active</span></td>
                        <td><div class="kwh-bar-wrap"><div class="kwh-bar-bg"><div class="kwh-bar-fill" style="width:58%;background:#10b981;"></div></div><span class="kwh-bar-val">1.80 kWh</span></div></td>
                        <td style="font-weight:500;">1200 W</td>
                        <td style="color:var(--icon-green-fg);font-weight:600;">Rp 19,440</td>
                        <td><canvas class="spark-canvas" data-vals="1.6,1.9,1.7,1.8,2.0,1.7,1.8" data-color="#10b981" width="64" height="28"></canvas></td>
                    </tr>
                    <tr>
                        <td style="color:var(--text-faint);font-size:0.72rem;">3</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:0.6rem;">
                                <div class="dev-avatar av-purple" style="background:#ede9fe;color:#8b5cf6;">RF</div>
                                <div>
                                    <div style="font-weight:600;font-size:0.8rem;color:var(--text-primary);">Refrigerator</div>
                                    <div style="font-size:0.68rem;color:var(--text-faint);">Appliance &middot; Kitchen</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="status-badge status-active">active</span></td>
                        <td><div class="kwh-bar-wrap"><div class="kwh-bar-bg"><div class="kwh-bar-fill" style="width:31%;background:#8b5cf6;"></div></div><span class="kwh-bar-val">0.96 kWh</span></div></td>
                        <td style="font-weight:500;">80 W</td>
                        <td style="color:var(--icon-orange-fg);font-weight:600;">Rp 10,368</td>
                        <td><canvas class="spark-canvas" data-vals="0.9,0.95,0.92,0.98,0.96,0.94,0.96" data-color="#8b5cf6" width="64" height="28"></canvas></td>
                    </tr>
                    <tr>
                        <td style="color:var(--text-faint);font-size:0.72rem;">4</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:0.6rem;">
                                <div class="dev-avatar av-yellow" style="background:#fef9c3;color:#ca8a04;">WM</div>
                                <div>
                                    <div style="font-weight:600;font-size:0.8rem;color:var(--text-primary);">Washing Machine</div>
                                    <div style="font-size:0.68rem;color:var(--text-faint);">Appliance &middot; Laundry</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="status-badge status-standby">standby</span></td>
                        <td><div class="kwh-bar-wrap"><div class="kwh-bar-bg"><div class="kwh-bar-fill" style="width:24%;background:#f59e0b;"></div></div><span class="kwh-bar-val">0.75 kWh</span></div></td>
                        <td style="font-weight:500;">500 W</td>
                        <td style="color:var(--icon-green-fg);font-weight:600;">Rp 8,100</td>
                        <td><canvas class="spark-canvas" data-vals="0.5,0.8,0.6,0.9,0.7,0.75,0.75" data-color="#f59e0b" width="64" height="28"></canvas></td>
                    </tr>
                    <tr>
                        <td style="color:var(--text-faint);font-size:0.72rem;">5</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:0.6rem;">
                                <div class="dev-avatar av-red" style="background:#fee2e2;color:#dc2626;">TV</div>
                                <div>
                                    <div style="font-weight:600;font-size:0.8rem;color:var(--text-primary);">Smart TV</div>
                                    <div style="font-size:0.68rem;color:var(--text-faint);">Entertainment &middot; Living Room</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="status-badge status-off">off</span></td>
                        <td><div class="kwh-bar-wrap"><div class="kwh-bar-bg"><div class="kwh-bar-fill" style="width:10%;background:#f87171;"></div></div><span class="kwh-bar-val">0.30 kWh</span></div></td>
                        <td style="font-weight:500;">150 W</td>
                        <td style="color:var(--text-muted);font-weight:600;">Rp 3,240</td>
                        <td><canvas class="spark-canvas" data-vals="0.4,0.35,0.3,0.45,0.25,0.3,0.3" data-color="#f87171" width="64" height="28"></canvas></td>
                    </tr>
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

    {{-- Smart Recommendations (PBI 2) --}}
    <div class="card" style="margin-bottom:1.5rem;">
        <div class="card-body">
            <div class="card-title" style="margin-bottom:0.25rem;">Smart Recommendations</div>
            <div class="card-subtitle" style="margin-bottom:1rem;">Personalized tips based on your highest-consuming devices</div>
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
                        <div class="tip-desc">Set temperature to 24-26&deg;C for optimal savings</div>
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

@endsection

