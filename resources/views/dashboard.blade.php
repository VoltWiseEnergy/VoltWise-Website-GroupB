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
    <div class="welcome-banner">
        <div class="welcome-top">
            <div class="welcome-bolt">
                <svg viewBox="0 0 24 24"><path d="M13 2L4.5 13.5H11L10 22L19.5 10.5H13L13 2Z"/></svg>
            </div>
            <div>
                <div class="welcome-heading">Welcome to <span>VoltWise!</span></div>
                <div class="welcome-user">Hi, {{ auth()->user()->name }}!</div>
            </div>
        </div>
        <p class="welcome-desc">
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
                <div class="stat-value">0</div>
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
                <div class="stat-value">0.00 <small>kWh</small></div>
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
                <div class="stat-value na">N/A</div>
                <div class="stat-detail">Highest energy usage</div>
            </div>
        </div>
    </div>

    {{-- Chart Cards --}}
    <div class="chart-row">
        <div class="card chart-card">
            <div class="card-body" style="flex:1;display:flex;flex-direction:column;">
                <div class="card-title">Top 5 Energy Consumers</div>
                <div class="card-subtitle">Today's energy consumption by device</div>
                <div class="empty-state">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                        <line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/>
                        <line x1="9" y1="3" x2="9" y2="21"/>
                    </svg>
                    <p>No devices added yet. Add some devices to see your consumption.</p>
                </div>
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
    </div>

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

@endsection
