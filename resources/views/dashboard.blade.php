@extends('layouts.app')

@section('title', 'Dashboard')
@section('meta-desc', 'VoltWise Energy Dashboard - Monitor your electricity consumption')

@php
    /* ---- Budget calculations ---- */
    $budget      = auth()->user()->monthly_budget;         // null = not set
    $monthlyCost = 0;                                      // TODO: replace with real monthly cost
    $pct         = ($budget && $budget > 0)
                    ? min(round(($monthlyCost / $budget) * 100, 1), 100)
                    : 0;
    $fillClass   = $pct >= 90 ? 'danger' : ($pct >= 70 ? 'warn' : '');
    $budgetFmt   = $budget ? 'Rp ' . number_format($budget, 0, ',', '.') : null;
    $usedFmt     = 'Rp ' . number_format($monthlyCost, 0, ',', '.');
@endphp

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
