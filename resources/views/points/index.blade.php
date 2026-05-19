@extends('layouts.app')

@section('title', 'My Points')
@section('meta-desc', 'VoltWise – Your points, level, and achievements history')

@section('styles')
<style>
/* ── Page layout ── */
.points-hero {
    background: linear-gradient(135deg, #4A7CF6 0%, #8b5cf6 100%);
    border-radius: 16px;
    padding: 2rem 2.25rem;
    margin-bottom: 1.5rem;
    color: #fff;
    position: relative;
    overflow: hidden;
}
.points-hero::before {
    content: '';
    position: absolute;
    top: -40px; right: -40px;
    width: 180px; height: 180px;
    border-radius: 50%;
    background: rgba(255,255,255,0.08);
}
.points-hero::after {
    content: '';
    position: absolute;
    bottom: -60px; left: 30%;
    width: 220px; height: 220px;
    border-radius: 50%;
    background: rgba(255,255,255,0.06);
}
.hero-inner { display: flex; align-items: center; justify-content: space-between; gap: 1.5rem; flex-wrap: wrap; position: relative; z-index: 1; }
.hero-pts { font-size: 3.5rem; font-weight: 900; letter-spacing: -0.05em; line-height: 1; }
.hero-pts small { font-size: 1.25rem; font-weight: 500; opacity: 0.75; margin-left: 6px; }
.hero-label { font-size: 0.85rem; opacity: 0.8; margin-top: 4px; }
.hero-right { text-align: right; }
.hero-level-name { font-size: 1.75rem; font-weight: 800; line-height: 1; }
.hero-level-sub  { font-size: 0.8rem; opacity: 0.75; margin-top: 4px; }

/* Level progress bar */
.hero-progress-wrap { margin-top: 1.25rem; position: relative; z-index: 1; }
.hero-progress-label { display: flex; justify-content: space-between; font-size: 0.75rem; opacity: 0.8; margin-bottom: 6px; }
.hero-progress-track { height: 10px; background: rgba(255,255,255,0.2); border-radius: 99px; overflow: hidden; }
.hero-progress-fill  { height: 100%; background: #fff; border-radius: 99px; transition: width 1s cubic-bezier(.4,0,.2,1); }

/* ── Levels grid ── */
.levels-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.75rem; margin-bottom: 1.5rem; }
.level-card {
    background: var(--bg-card); border: 1px solid var(--border);
    border-radius: 12px; padding: 1rem;
    display: flex; flex-direction: column; align-items: center; gap: 0.35rem;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.level-card.active {
    border-color: var(--blue-600);
    box-shadow: 0 0 0 3px rgba(74,124,246,0.15);
}
.level-emoji { font-size: 1.75rem; line-height: 1; }
.level-name  { font-size: 0.8rem; font-weight: 700; color: var(--text-primary); }
.level-range { font-size: 0.68rem; color: var(--text-faint); }
.level-check { font-size: 0.7rem; color: #10b981; font-weight: 600; }

/* ── How to earn ── */
.earn-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; margin-bottom: 1.5rem; }
.earn-item {
    background: var(--bg-card); border: 1px solid var(--border);
    border-radius: 10px; padding: 1rem;
    display: flex; align-items: flex-start; gap: 0.75rem;
}
.earn-icon { width: 36px; height: 36px; border-radius: 9px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.earn-icon svg { width: 17px; height: 17px; }
.earn-pts-badge { display: inline-flex; align-items: center; padding: 0.15rem 0.5rem; border-radius: 99px; font-size: 0.7rem; font-weight: 700; margin-top: 4px; }
.earn-title { font-size: 0.82rem; font-weight: 600; color: var(--text-primary); }
.earn-desc  { font-size: 0.72rem; color: var(--text-muted); margin-top: 2px; line-height: 1.4; }

/* ── History table ── */
.history-date-group { margin-bottom: 1.25rem; }
.history-date-label {
    font-size: 0.72rem; font-weight: 700; color: var(--text-muted);
    text-transform: uppercase; letter-spacing: 0.06em;
    padding: 0.35rem 0; border-bottom: 1px solid var(--border);
    margin-bottom: 0.5rem;
}
.history-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: 0.55rem 0.5rem; border-radius: 8px;
    transition: background 0.12s;
}
.history-row:hover { background: rgba(74,124,246,0.05); }
.history-event { display: flex; align-items: center; gap: 0.6rem; }
.history-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.history-evt-name { font-size: 0.8rem; font-weight: 500; color: var(--text-secondary); }
.history-pts { font-size: 0.82rem; font-weight: 700; color: #10b981; }

/* responsive */
@media (max-width: 900px) { .levels-grid { grid-template-columns: repeat(2,1fr); } }
@media (max-width: 640px) { .earn-grid { grid-template-columns: 1fr; } .hero-pts { font-size: 2.5rem; } }
</style>
@endsection

@section('content')

@php
    $levels = [
        ['name'=>'Bronze',   'emoji'=>'🥉', 'min'=>0,   'max'=>99,  'color'=>'#cd7f32'],
        ['name'=>'Silver',   'emoji'=>'🥈', 'min'=>100, 'max'=>299, 'color'=>'#94a3b8'],
        ['name'=>'Gold',     'emoji'=>'🥇', 'min'=>300, 'max'=>699, 'color'=>'#f59e0b'],
        ['name'=>'Platinum', 'emoji'=>'💎', 'min'=>700, 'max'=>null,'color'=>'#8b5cf6'],
    ];
    $eventMeta = [
        'consistent_logging' => ['label'=>'Logged Usage',        'color'=>'#10b981', 'bg'=>'rgba(16,185,129,0.12)', 'pts'=>'+5'],
        'under_budget'       => ['label'=>'Stayed Under Budget', 'color'=>'#f59e0b', 'bg'=>'rgba(245,158,11,0.12)', 'pts'=>'+50'],
        'low_usage'          => ['label'=>'Low-Usage Day',       'color'=>'#4A7CF6', 'bg'=>'rgba(74,124,246,0.12)', 'pts'=>'+10'],
        'very_low_usage'     => ['label'=>'Very Low Usage',      'color'=>'#8b5cf6', 'bg'=>'rgba(139,92,246,0.12)','pts'=>'+20'],
    ];
@endphp

{{-- Page Header --}}
<div class="page-header">
    <div>
        <h1 class="page-title">My Points</h1>
        <p class="page-subtitle">Your energy-saving achievements and level progress</p>
    </div>
    <a href="{{ url('/dashboard') }}" class="btn-primary" style="text-decoration:none;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        Back to Dashboard
    </a>
</div>

{{-- Hero Card --}}
<div class="points-hero">
    <div class="hero-inner">
        <div>
            <div class="hero-pts">{{ number_format($totalPoints) }}<small>pts</small></div>
            <div class="hero-label">Total points earned</div>
        </div>
        <div class="hero-right">
            <div class="hero-level-name">{{ $level['current']['emoji'] }} {{ $level['current']['name'] }}</div>
            <div class="hero-level-sub">Current Level</div>
        </div>
    </div>
    <div class="hero-progress-wrap">
        <div class="hero-progress-label">
            <span>{{ $level['current']['emoji'] }} {{ $level['current']['name'] }}</span>
            @if($level['next'])
                <span>{{ $level['points_to_next'] }} pts to {{ $level['next']['emoji'] }} {{ $level['next']['name'] }}</span>
            @else
                <span>🏆 Max Level</span>
            @endif
        </div>
        <div class="hero-progress-track">
            <div class="hero-progress-fill" id="hero-fill" style="width: {{ $level['progress'] }}%;"></div>
        </div>
    </div>
</div>

{{-- Level Cards --}}
<div class="levels-grid">
    @foreach($levels as $lvl)
        @php $isActive = $level['current']['name'] === $lvl['name']; @endphp
        <div class="level-card {{ $isActive ? 'active' : '' }}">
            <div class="level-emoji">{{ $lvl['emoji'] }}</div>
            <div class="level-name">{{ $lvl['name'] }}</div>
            <div class="level-range">
                @if($lvl['max'])
                    {{ $lvl['min'] }}–{{ $lvl['max'] }} pts
                @else
                    {{ $lvl['min'] }}+ pts
                @endif
            </div>
            @if($isActive)
                <div class="level-check">◉ Current Level</div>
            @elseif($totalPoints > ($lvl['max'] ?? $totalPoints))
                <div class="level-check">✓ Achieved</div>
            @endif
        </div>
    @endforeach
</div>

{{-- How to Earn --}}
<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-body">
        <div class="card-title" style="margin-bottom:0.25rem;">How to Earn Points</div>
        <div class="card-subtitle" style="margin-bottom:1rem;">Complete these actions daily to grow your score</div>
        <div class="earn-grid">
            <div class="earn-item">
                <div class="earn-icon" style="background:rgba(16,185,129,0.12);color:#10b981;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                    </svg>
                </div>
                <div>
                    <div class="earn-title">Log Your Usage</div>
                    <div class="earn-desc">Record at least one device's usage in the Daily Tracker</div>
                    <span class="earn-pts-badge" style="background:rgba(16,185,129,0.12);color:#10b981;">+5 pts/day</span>
                </div>
            </div>
            <div class="earn-item">
                <div class="earn-icon" style="background:rgba(245,158,11,0.12);color:#d97706;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                    </svg>
                </div>
                <div>
                    <div class="earn-title">Stay Under Budget</div>
                    <div class="earn-desc">Keep your monthly cost below your set budget limit</div>
                    <span class="earn-pts-badge" style="background:rgba(245,158,11,0.12);color:#d97706;">+50 pts/day</span>
                </div>
            </div>
            <div class="earn-item">
                <div class="earn-icon" style="background:rgba(74,124,246,0.12);color:#4A7CF6;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/>
                    </svg>
                </div>
                <div>
                    <div class="earn-title">Low-Usage Day</div>
                    <div class="earn-desc">Use less energy than your 7-day daily average</div>
                    <span class="earn-pts-badge" style="background:rgba(74,124,246,0.12);color:#4A7CF6;">+10 pts/day</span>
                </div>
            </div>
            <div class="earn-item">
                <div class="earn-icon" style="background:rgba(139,92,246,0.12);color:#8b5cf6;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                </div>
                <div>
                    <div class="earn-title">Very Low Usage Day</div>
                    <div class="earn-desc">Use less than 50% of your 7-day average — bonus points!</div>
                    <span class="earn-pts-badge" style="background:rgba(139,92,246,0.12);color:#8b5cf6;">+20 pts/day</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Points History --}}
<div class="card">
    <div class="card-body">
        <div class="card-title" style="margin-bottom:0.25rem;">Points History</div>
        <div class="card-subtitle" style="margin-bottom:1rem;">Last 30 days of achievements</div>

        @if($historyByDate->isEmpty())
            <div class="empty-state" style="min-height:140px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                </svg>
                <p>No points earned yet.<br>Start by logging your device usage!</p>
                <a href="{{ route('usage.tracker') }}" class="btn-primary" style="text-decoration:none;margin-top:0.5rem;">
                    Go to Daily Tracker
                </a>
            </div>
        @else
            @foreach($historyByDate as $date => $logs)
                @php $dayTotal = $logs->sum('points'); @endphp
                <div class="history-date-group">
                    <div class="history-date-label">
                        {{ \Carbon\Carbon::parse($date)->format('l, j F Y') }}
                        <span style="float:right;color:var(--text-secondary);">+{{ $dayTotal }} pts</span>
                    </div>
                    @foreach($logs as $log)
                        @php $meta = $eventMeta[$log->event_type] ?? ['label'=>$log->event_type,'color'=>'#94a3b8','bg'=>'#f1f5f9','pts'=>'+'.$log->points]; @endphp
                        <div class="history-row">
                            <div class="history-event">
                                <div class="history-dot" style="background:{{ $meta['color'] }};"></div>
                                <span class="history-evt-name">{{ $meta['label'] }}</span>
                            </div>
                            <span class="history-pts">{{ $meta['pts'] }} pts</span>
                        </div>
                    @endforeach
                </div>
            @endforeach
        @endif
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Animate the hero progress bar on load
    window.addEventListener('load', function () {
        const fill = document.getElementById('hero-fill');
        if (!fill) return;
        const target = parseFloat(fill.style.width) || 0;
        fill.style.width = '0%';
        requestAnimationFrame(() => requestAnimationFrame(() => {
            fill.style.width = target + '%';
        }));
    });
</script>
@endsection
