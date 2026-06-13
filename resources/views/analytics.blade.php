@extends('layouts.app')

@section('title', 'Analytics')
@section('meta-desc', 'VoltWise Analytics – Top consumers, category distribution, and peak usage heatmap')

@section('styles')
<style>
/* ── Page-level layout ─────────────────────────────────── */
.analytics-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.25rem;
    margin-bottom: 1.5rem;
}
.analytics-full { grid-column: 1 / -1; }

/* ── Section header pills ──────────────────────────────── */
.pbi-badge {
    display: inline-flex; align-items: center; gap: 0.35rem;
    padding: 0.22rem 0.65rem; border-radius: 99px;
    font-size: 0.68rem; font-weight: 700; letter-spacing: 0.05em;
    background: rgba(74,124,246,0.12); color: var(--blue-600);
    border: 1px solid rgba(74,124,246,0.22);
    margin-bottom: 0.35rem;
}
.pbi-badge.green  { background: rgba(16,185,129,0.12); color:#10b981; border-color:rgba(16,185,129,0.22); }
.pbi-badge.purple { background: rgba(139,92,246,0.12); color:#8b5cf6; border-color:rgba(139,92,246,0.22); }

/* ── Top Consumer bar rows (PBI-36) ────────────────────── */
.consumer-row {
    display: grid;
    grid-template-columns: 28px 1fr 90px 80px;
    align-items: center;
    gap: 0.75rem;
    padding: 0.65rem 0;
    border-bottom: 1px solid var(--border);
    transition: background 0.15s;
}
.consumer-row:last-child { border-bottom: none; }
.consumer-row:hover { background: rgba(74,124,246,0.04); border-radius: 8px; }
.consumer-rank {
    width: 24px; height: 24px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.68rem; font-weight: 700;
    background: var(--icon-btn-bg); color: var(--text-muted);
    flex-shrink: 0;
}
.consumer-rank.top1 { background: #fef9c3; color: #ca8a04; }
.consumer-rank.top2 { background: #f1f5f9; color: #64748b; }
.consumer-rank.top3 { background: #ffedd5; color: #f97316; }
[data-theme="dark"] .consumer-rank.top1 { background: rgba(202,138,4,0.2);  color: #fde047; }
[data-theme="dark"] .consumer-rank.top2 { background: rgba(100,116,139,0.2); color: #94a3b8; }
[data-theme="dark"] .consumer-rank.top3 { background: rgba(249,115,22,0.2); color: #fdba74; }

.consumer-info { min-width: 0; }
.consumer-name { font-size: 0.8rem; font-weight: 600; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.consumer-cat  { font-size: 0.68rem; color: var(--text-faint); margin-top: 1px; }

.consumer-bar-wrap { display: flex; flex-direction: column; gap: 3px; }
.consumer-bar-bg   { height: 6px; background: var(--border); border-radius: 3px; overflow: hidden; }
.consumer-bar-fill { height: 100%; border-radius: 3px; transition: width 0.9s cubic-bezier(.4,0,.2,1); }

.consumer-kwh  { font-size: 0.78rem; font-weight: 700; color: var(--text-primary); text-align: right; white-space: nowrap; }
.consumer-cost { font-size: 0.68rem; color: var(--text-faint); text-align: right; }

/* ── Category donut + legend (PBI-37) ──────────────────── */
.category-layout {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    flex-wrap: wrap;
}
.category-chart-wrap {
    position: relative;
    width: 180px;
    height: 180px;
    flex-shrink: 0;
}
.donut-center-label {
    position: absolute; top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    text-align: center; pointer-events: none;
}
.donut-center-val  { font-size: 1.1rem; font-weight: 700; color: var(--text-primary); line-height: 1; }
.donut-center-sub  { font-size: 0.65rem; color: var(--text-faint); margin-top: 2px; }
.category-legend   { flex: 1; min-width: 140px; display: flex; flex-direction: column; gap: 0.55rem; }
.legend-row {
    display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;
}
.legend-left  { display: flex; align-items: center; gap: 0.5rem; min-width: 0; }
.legend-dot   { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
.legend-label { font-size: 0.78rem; color: var(--text-secondary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 120px; }
.legend-pct   { font-size: 0.75rem; font-weight: 700; color: var(--text-primary); flex-shrink: 0; }

/* ── Peak Heatmap (PBI-38) ────────────────────────────── */
.heatmap-wrap {
    overflow-x: auto;
}
.heatmap-table {
    border-collapse: separate;
    border-spacing: 3px;
    width: 100%;
}
.heatmap-table th {
    font-size: 0.6rem; font-weight: 600; color: var(--text-faint);
    text-align: center; padding: 2px 1px;
    white-space: nowrap;
}
.heatmap-table td.day-label {
    font-size: 0.68rem; font-weight: 600; color: var(--text-muted);
    padding-right: 0.5rem; white-space: nowrap; text-align: right; min-width: 72px;
}
.heatmap-cell {
    width: 28px; height: 22px; border-radius: 4px;
    transition: transform 0.1s;
    cursor: default;
}
.heatmap-cell:hover { transform: scale(1.15); z-index: 2; position: relative; }

.heatmap-legend {
    display: flex; align-items: center; gap: 0.5rem;
    margin-top: 0.75rem; font-size: 0.68rem; color: var(--text-faint);
}
.heatmap-gradient {
    width: 120px; height: 8px; border-radius: 4px;
    background: linear-gradient(to right, #e0f2fe, #0ea5e9, #0369a1);
}
[data-theme="dark"] .heatmap-gradient {
    background: linear-gradient(to right, #0c2233, #0ea5e9, #7dd3fc);
}

/* ── Tooltip ─────────────────────────────────────────── */
#heatmap-tooltip {
    position: fixed; z-index: 9999;
    background: var(--modal-bg); border: 1px solid var(--border);
    border-radius: 8px; padding: 0.45rem 0.75rem;
    font-size: 0.75rem; color: var(--text-primary);
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    pointer-events: none; opacity: 0; transition: opacity 0.15s;
    white-space: nowrap;
}

/* ── Empty states ────────────────────────────────────── */
.an-empty {
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    gap: 0.5rem; padding: 2.5rem 1rem;
    color: var(--text-faint); text-align: center;
}
.an-empty svg { width: 40px; height: 40px; opacity: 0.3; }
.an-empty p   { font-size: 0.8125rem; }

/* ── Responsive ──────────────────────────────────────── */
@media (max-width: 900px) {
    .analytics-grid { grid-template-columns: 1fr; }
    .analytics-full { grid-column: 1; }
}
@media (max-width: 600px) {
    .category-layout { flex-direction: column; align-items: flex-start; }
    .category-chart-wrap { width: 150px; height: 150px; }
}
</style>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">Analytics</h1>
        <p class="page-subtitle">Deep dive into your energy consumption patterns</p>
    </div>
</div>

<div class="analytics-grid">

    {{-- ─────────────────────────────────────────────────────────────────────
         PBI-36 · Top Consumer Analysis
         ───────────────────────────────────────────────────────────────────── --}}
    <div class="card analytics-full">
        <div class="card-body">
            <div class="card-title">Top Consumer Analysis</div>
            <div class="card-subtitle" style="margin-bottom:1.1rem;">Devices ranked by today's energy consumption — identify what to reduce or replace</div>

            @if($topConsumers->isEmpty())
                <div class="an-empty">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                    </svg>
                    <p>No devices found. Add devices to see your top consumers.</p>
                </div>
            @else
                @php
                    $colors36 = ['#4A7CF6','#10b981','#8b5cf6','#f59e0b','#f87171','#06b6d4','#ec4899','#84cc16','#f97316','#a855f7'];
                @endphp
                @foreach($topConsumers as $i => $device)
                    @php
                        $bar  = $maxKwh > 0 ? round(($device->daily_energy_kwh / $maxKwh) * 100) : 0;
                        $col  = $colors36[$i % count($colors36)];
                        $rankClass = $i === 0 ? 'top1' : ($i === 1 ? 'top2' : ($i === 2 ? 'top3' : ''));
                    @endphp
                    <div class="consumer-row">
                        <div class="consumer-rank {{ $rankClass }}">{{ $i + 1 }}</div>
                        <div class="consumer-info">
                            <div class="consumer-name" title="{{ $device->name }}">{{ $device->name }}</div>
                            <div class="consumer-cat">{{ $device->category ?? 'Device' }} · {{ $device->wattage ?? '-' }} W</div>
                        </div>
                        <div class="consumer-bar-wrap">
                            <div class="consumer-bar-bg">
                                <div class="consumer-bar-fill" style="width:{{ $bar }}%; background:{{ $col }};"></div>
                            </div>
                        </div>
                        <div>
                            <div class="consumer-kwh">{{ number_format($device->daily_energy_kwh, 3) }} kWh</div>
                            <div class="consumer-cost">Rp {{ number_format($device->daily_cost, 0, ',', '.') }}/day</div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    {{-- ─────────────────────────────────────────────────────────────────────
         PBI-37 · Category Distribution
         ───────────────────────────────────────────────────────────────────── --}}
    <div class="card">
        <div class="card-body" style="display:flex;flex-direction:column;height:100%;">
            <div class="card-title">Category Distribution</div>
            <div class="card-subtitle" style="margin-bottom:1.1rem;">Energy usage broken down by device category</div>

            @if($categoryData->isEmpty())
                <div class="an-empty" style="flex:1;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21.21 15.89A10 10 0 1 1 8 2.83"/><path d="M22 12A10 10 0 0 0 12 2v10z"/>
                    </svg>
                    <p>No category data. Add devices to see distribution.</p>
                </div>
            @else
                @php
                    $colors37 = ['#4A7CF6','#10b981','#8b5cf6','#f59e0b','#f87171','#06b6d4','#ec4899','#84cc16'];
                    $totalKwh = $categoryData->sum();
                @endphp
                <div class="category-layout" style="flex:1;">
                    <div class="category-chart-wrap">
                        <canvas id="categoryDonut"></canvas>
                        <div class="donut-center-label">
                            <div class="donut-center-val">{{ number_format($totalKwh, 2) }}</div>
                            <div class="donut-center-sub">kWh total</div>
                        </div>
                    </div>
                    <div class="category-legend">
                        @foreach($categoryData as $cat => $kwh)
                            @php
                                $ci  = $loop->index % count($colors37);
                                $pct = $totalKwh > 0 ? round(($kwh / $totalKwh) * 100, 1) : 0;
                            @endphp
                            <div class="legend-row">
                                <div class="legend-left">
                                    <span class="legend-dot" style="background:{{ $colors37[$ci] }};"></span>
                                    <span class="legend-label" title="{{ $cat }}">{{ $cat }}</span>
                                </div>
                                <span class="legend-pct">{{ $pct }}%</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ─────────────────────────────────────────────────────────────────────
         PBI-38 · Peak Usage Heatmap (last 7 days × 24 hours)
         ───────────────────────────────────────────────────────────────────── --}}
    <div class="card">
        <div class="card-body">
            <div class="card-title">Peak Usage Heatmap</div>
            <div class="card-subtitle" style="margin-bottom:1.1rem;">Estimated energy intensity by hour of day — last 7 days</div>

            @if($devices->isEmpty())
                <div class="an-empty">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                        <line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="3" x2="9" y2="21"/>
                    </svg>
                    <p>No usage data available. Log usage to generate heatmap.</p>
                </div>
            @else
                <div class="heatmap-wrap">
                    <table class="heatmap-table" id="heatmap-table">
                        <thead>
                            <tr>
                                <th></th>
                                @for($h = 0; $h < 24; $h++)
                                    <th>{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}</th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($days as $di => $dayLabel)
                                <tr>
                                    <td class="day-label">{{ $dayLabel }}</td>
                                    @for($h = 0; $h < 24; $h++)
                                        @php
                                            $val = $matrix[$di][$h] ?? 0;
                                            $norm = $heatmapMax > 0 ? $val / $heatmapMax : 0;
                                        @endphp
                                        <td>
                                            <div class="heatmap-cell"
                                                 data-val="{{ number_format($val, 4) }}"
                                                 data-hour="{{ $h }}"
                                                 data-day="{{ $dayLabel }}"
                                                 style="background: {{ $norm < 0.01
                                                     ? 'var(--border)'
                                                     : 'rgba(' . round(14 + (3-14)*$norm) . ',' . round(165 + (130-165)*$norm) . ',' . round(233 + (15-233)*$norm) . ',' . min(0.15 + $norm * 0.85, 1) . ')' }};">
                                            </div>
                                        </td>
                                    @endfor
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="heatmap-legend">
                        <span>Low</span>
                        <div class="heatmap-gradient"></div>
                        <span>High</span>
                        <span style="margin-left:1rem;">{{ $heatmapMax > 0 ? '(max ≈ ' . number_format($heatmapMax, 3) . ' kWh/h)' : '' }}</span>
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>

{{-- Tooltip element --}}
<div id="heatmap-tooltip"></div>

@endsection

@section('scripts')
<script>
(function () {

    // ── Category Donut (PBI-37) ───────────────────────────────────────────
    var categoryData = @json($categoryData);
    var colors37 = ['#4A7CF6','#10b981','#8b5cf6','#f59e0b','#f87171','#06b6d4','#ec4899','#84cc16'];

    function initDonut() {
        var el = document.getElementById('categoryDonut');
        if (!el || !categoryData || Object.keys(categoryData).length === 0) return;

        var labels = Object.keys(categoryData);
        var data   = Object.values(categoryData).map(Number);
        var bgColors = labels.map(function(_, i) { return colors37[i % colors37.length]; });

        new Chart(el, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: bgColors,
                    borderWidth: 2,
                    borderColor: 'transparent',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '68%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                var total = ctx.dataset.data.reduce(function(a,b){ return a+b; }, 0);
                                var pct   = total > 0 ? ((ctx.parsed / total) * 100).toFixed(1) : '0';
                                return ' ' + ctx.parsed.toFixed(3) + ' kWh (' + pct + '%)';
                            }
                        }
                    }
                }
            }
        });
    }

    // ── Heatmap tooltip (PBI-38) ─────────────────────────────────────────
    var tooltip = document.getElementById('heatmap-tooltip');
    document.querySelectorAll('.heatmap-cell').forEach(function(cell) {
        cell.addEventListener('mousemove', function(e) {
            var val  = parseFloat(cell.getAttribute('data-val'));
            var hour = cell.getAttribute('data-hour');
            var day  = cell.getAttribute('data-day');
            tooltip.innerHTML =
                '<strong>' + day + '</strong> &mdash; ' +
                String(hour).padStart(2,'0') + ':00&ndash;' + String(hour).padStart(2,'0') + ':59<br>' +
                '≈ <strong>' + val.toFixed(4) + ' kWh</strong>';
            tooltip.style.opacity = '1';
            tooltip.style.left    = (e.clientX + 14) + 'px';
            tooltip.style.top     = (e.clientY - 40) + 'px';
        });
        cell.addEventListener('mouseleave', function() {
            tooltip.style.opacity = '0';
        });
    });

    // ── Bootstrap Chart.js ───────────────────────────────────────────────
    if (typeof Chart === 'undefined') {
        var s = document.createElement('script');
        s.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js';
        s.onload = initDonut;
        document.head.appendChild(s);
    } else {
        initDonut();
    }

    // ── Animate consumer bars on load (PBI-36) ───────────────────────────
    window.addEventListener('load', function() {
        document.querySelectorAll('.consumer-bar-fill').forEach(function(bar) {
            var target = bar.style.width;
            bar.style.width = '0%';
            requestAnimationFrame(function() {
                requestAnimationFrame(function() {
                    bar.style.width = target;
                });
            });
        });
    });

})();
</script>
@endsection
