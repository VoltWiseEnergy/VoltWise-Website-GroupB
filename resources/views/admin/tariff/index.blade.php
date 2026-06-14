@extends('layouts.app')

@section('title', 'Tariff Management')

@section('styles')
<style>
.tariff-grid { display:grid; grid-template-columns:300px 1fr; gap:1.5rem; align-items:start; }
.tariff-history-table { width:100%; border-collapse:collapse; }
.tariff-history-table th { text-align:left; padding:0.75rem 1rem; font-size:0.75rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; border-bottom:1px solid var(--border); }
.tariff-history-table td { padding:0.875rem 1rem; font-size:0.875rem; color:var(--text-secondary); border-bottom:1px solid var(--border); }
.tariff-history-table tr:last-child td { border-bottom:none; }
.tariff-history-table tr:hover td { background:rgba(74,124,246,0.02); }
.badge-active { background:#10b981; color:#fff; padding:2px 8px; border-radius:12px; font-size:0.7rem; font-weight:700; letter-spacing:0.03em; }

@media (max-width: 900px) {
    .tariff-grid { grid-template-columns:1fr; }
}
</style>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">⚡ Tariff Management</h1>
        <p class="page-subtitle">Set and update electricity rates for accurate cost calculations</p>
    </div>
</div>

<div class="tariff-grid">
    <!-- Form to set new tariff -->
    <div class="card">
        <div class="card-body">
            <h3 class="card-title" style="margin-bottom:1rem;">Add New Tariff Record</h3>
            <form method="POST" action="{{ route('admin.tariff.store') }}">
                @csrf
                <div style="margin-bottom:1.25rem;">
                    <label style="display:block; font-size:0.8125rem; font-weight:600; color:var(--text-secondary); margin-bottom:0.4rem;">Rate per kWh (Rp)</label>
                    <input type="number" name="rate_per_kwh" step="0.01" required
                           placeholder="e.g. 1444.70"
                           style="width:100%; padding:0.65rem; border:1.5px solid var(--border); border-radius:8px; font-size:0.9375rem; background:transparent; color:var(--text-primary); transition:border-color 0.2s;">
                    @error('rate_per_kwh')
                        <div style="color:#ef4444; font-size:0.75rem; margin-top:0.375rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div style="margin-bottom:1.5rem;">
                    <label style="display:block; font-size:0.8125rem; font-weight:600; color:var(--text-secondary); margin-bottom:0.4rem;">Effective Date</label>
                    <input type="date" name="effective_date" required
                           value="{{ date('Y-m-d') }}"
                           style="width:100%; padding:0.65rem; border:1.5px solid var(--border); border-radius:8px; font-size:0.9375rem; background:transparent; color:var(--text-primary); transition:border-color 0.2s;">
                    @error('effective_date')
                        <div style="color:#ef4444; font-size:0.75rem; margin-top:0.375rem;">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn-primary" style="width:100%; justify-content:center;">
                    Save Tariff Record
                </button>
            </form>
        </div>
    </div>

    <!-- History table -->
    <div class="card">
        <div class="card-body" style="padding:0;">
            <div style="padding:1.25rem; border-bottom:1px solid var(--border);">
                <h3 class="card-title">Tariff History</h3>
                <p class="card-subtitle">All past and scheduled electricity rates</p>
            </div>
            
            <div style="overflow-x:auto;">
                <table class="tariff-history-table">
                    <thead>
                        <tr>
                            <th>Rate (Rp / kWh)</th>
                            <th>Effective Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $activeFound = false;
                        @endphp
                        @forelse($tariffs as $tariff)
                            @php
                                $isActive = false;
                                if (!$activeFound && $tariff->effective_date <= now()) {
                                    $isActive = true;
                                    $activeFound = true;
                                }
                                $isScheduled = $tariff->effective_date > now();
                            @endphp
                            <tr>
                                <td style="font-weight:600;">Rp {{ number_format($tariff->rate_per_kwh, 2, ',', '.') }}</td>
                                <td>{{ \Carbon\Carbon::parse($tariff->effective_date)->format('d M Y') }}</td>
                                <td>
                                    @if($isActive)
                                        <span class="badge-active">ACTIVE</span>
                                    @elseif($isScheduled)
                                        <span style="color:var(--text-muted); font-size:0.75rem; font-weight:600;">SCHEDULED</span>
                                    @else
                                        <span style="color:var(--text-faint); font-size:0.75rem;">PAST</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align:center; padding:2rem; color:var(--text-faint);">
                                    No tariff records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    // Add focus ring effect to inputs
    document.querySelectorAll('input').forEach(input => {
        input.addEventListener('focus', function() {
            this.style.borderColor = 'var(--blue-600)';
            this.style.outline = 'none';
            this.style.boxShadow = '0 0 0 3px rgba(74,124,246,0.15)';
        });
        input.addEventListener('blur', function() {
            this.style.borderColor = 'var(--border)';
            this.style.boxShadow = 'none';
        });
    });
</script>
@endsection
