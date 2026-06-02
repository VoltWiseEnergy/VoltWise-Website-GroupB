@extends('layouts.app')

@section('title', 'Leaderboard')
@section('meta-desc', 'VoltWise Leaderboard – See how you rank against other users')

@section('styles')
<style>
/* ── Page header ── */
.lb-header { display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:1rem; margin-bottom:2rem; }
.lb-subtitle { font-size:0.85rem; color:var(--text-muted); margin-top:0.25rem; }

/* ── Podium ── */
.podium-row {
    display:flex; align-items:flex-end; justify-content:center;
    gap:1.25rem; margin-bottom:2rem; flex-wrap:wrap;
}
.podium-card {
    display:flex; flex-direction:column; align-items:center;
    background:var(--card-bg); border:1px solid var(--border);
    border-radius:16px; padding:1.5rem 1.25rem 1rem;
    min-width:140px; flex:0 0 auto; position:relative;
    transition:transform 0.2s, box-shadow 0.2s;
}
.podium-card:hover { transform:translateY(-4px); box-shadow:0 12px 32px rgba(0,0,0,0.12); }
.podium-card.rank-1 { padding-top:2rem; border-color:rgba(245,158,11,0.4); box-shadow:0 4px 24px rgba(245,158,11,0.12); }
.podium-card.rank-2 { border-color:rgba(148,163,184,0.4); }
.podium-card.rank-3 { border-color:rgba(205,127,50,0.4); }

.podium-medal { font-size:2rem; margin-bottom:0.5rem; }
.podium-avatar {
    width:56px; height:56px; border-radius:50%;
    display:flex; align-items:center; justify-content:center;
    font-size:1.1rem; font-weight:800; margin-bottom:0.6rem;
    border:3px solid var(--border);
}
.podium-card.rank-1 .podium-avatar { border-color:#f59e0b; width:64px; height:64px; font-size:1.25rem; }
.podium-name { font-size:0.82rem; font-weight:700; color:var(--text-primary); text-align:center; max-width:110px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.podium-pts  { font-size:1.1rem; font-weight:800; color:var(--text-primary); margin-top:0.2rem; }
.podium-pts span { font-size:0.7rem; font-weight:500; color:var(--text-muted); }
.podium-level { font-size:0.7rem; font-weight:600; padding:0.15rem 0.5rem; border-radius:99px; margin-top:0.35rem; }
.podium-you { position:absolute; top:0.55rem; right:0.6rem; font-size:0.62rem; font-weight:700; background:var(--blue-600); color:#fff; padding:0.15rem 0.45rem; border-radius:99px; }

/* ── Full list ── */
.lb-list { display:flex; flex-direction:column; gap:0; }
.lb-row {
    display:grid;
    grid-template-columns: 44px 1fr auto;
    align-items:center;
    gap:1rem;
    padding:0.85rem 1.25rem;
    border-bottom:1px solid var(--border);
    transition:background 0.15s;
}
.lb-row:last-child { border-bottom:none; }
.lb-row:hover { background:rgba(74,124,246,0.04); }
.lb-row.is-me { background:rgba(74,124,246,0.07); }

.lb-rank { font-size:0.82rem; font-weight:700; color:var(--text-muted); text-align:center; }
.lb-user { display:flex; align-items:center; gap:0.75rem; min-width:0; }
.lb-avatar {
    width:36px; height:36px; border-radius:50%; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    font-size:0.72rem; font-weight:800;
}
.lb-name { font-size:0.85rem; font-weight:600; color:var(--text-primary); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.lb-you-tag { font-size:0.62rem; font-weight:700; background:var(--blue-600); color:#fff; padding:0.1rem 0.4rem; border-radius:99px; margin-left:0.4rem; flex-shrink:0; }
.lb-level-badge { font-size:0.68rem; font-weight:600; padding:0.1rem 0.45rem; border-radius:99px; margin-top:0.15rem; display:inline-block; }

.lb-right { display:flex; flex-direction:column; align-items:flex-end; gap:0.2rem; flex-shrink:0; }
.lb-pts { font-size:0.88rem; font-weight:800; color:var(--text-primary); }
.lb-pts span { font-size:0.7rem; font-weight:500; color:var(--text-muted); }
.lb-badges { font-size:0.68rem; color:var(--text-faint); }

/* ── My rank banner ── */
.my-rank-banner {
    display:flex; align-items:center; gap:1rem; flex-wrap:wrap;
    background:linear-gradient(135deg,rgba(74,124,246,0.12),rgba(139,92,246,0.08));
    border:1px solid rgba(74,124,246,0.25); border-radius:12px;
    padding:1rem 1.25rem; margin-bottom:1.5rem;
}
.my-rank-num { font-size:2rem; font-weight:900; color:var(--blue-600); line-height:1; }
.my-rank-label { font-size:0.78rem; color:var(--text-muted); }
.my-rank-sep { width:1px; height:36px; background:var(--border); }

/* ── Empty state ── */
.lb-empty { text-align:center; padding:3rem 1rem; color:var(--text-faint); font-size:0.85rem; }

/* ── Avatar color palette ── */
.av-c0{background:#dbeafe;color:#1d4ed8;} .av-c1{background:#d1fae5;color:#065f46;}
.av-c2{background:#ede9fe;color:#5b21b6;} .av-c3{background:#fef9c3;color:#92400e;}
.av-c4{background:#fee2e2;color:#991b1b;} .av-c5{background:#cffafe;color:#155e75;}
.av-c6{background:#fce7f3;color:#9d174d;} .av-c7{background:#fef3c7;color:#78350f;}
[data-theme="dark"] .av-c0{background:rgba(29,78,216,0.2);color:#93b4fb;}
[data-theme="dark"] .av-c1{background:rgba(5,150,105,0.2);color:#6ee7b7;}
[data-theme="dark"] .av-c2{background:rgba(91,33,182,0.2);color:#c4b5fd;}
[data-theme="dark"] .av-c3{background:rgba(146,64,14,0.2);color:#fde047;}
[data-theme="dark"] .av-c4{background:rgba(153,27,27,0.2);color:#fca5a5;}
[data-theme="dark"] .av-c5{background:rgba(21,94,117,0.2);color:#67e8f9;}
[data-theme="dark"] .av-c6{background:rgba(157,23,77,0.2);color:#f9a8d4;}
[data-theme="dark"] .av-c7{background:rgba(120,53,15,0.2);color:#fcd34d;}
</style>
@endsection

@section('content')

<div class="lb-header">
    <div>
        <h1 class="page-title">🏆 Leaderboard</h1>
        <p class="lb-subtitle">Ranked by total points &mdash; updated daily</p>
    </div>
    <a href="{{ route('points.index') }}" style="text-decoration:none;">
        <button class="btn-primary" style="gap:0.4rem;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            My Points
        </button>
    </a>
</div>

{{-- My rank banner --}}
@if($myRank)
<div class="my-rank-banner">
    <div>
        <div class="my-rank-num">#{{ $myRank }}</div>
        <div class="my-rank-label">Your rank</div>
    </div>
    <div class="my-rank-sep"></div>
    @php $me = $ranked->firstWhere('is_me', true); @endphp
    <div>
        <div style="font-size:1.1rem;font-weight:800;color:var(--text-primary);">{{ number_format($me['points']) }} <span style="font-size:0.75rem;font-weight:500;color:var(--text-muted);">pts</span></div>
        <div style="margin-top:0.15rem;">
            <span class="lb-level-badge" style="background:{{ $me['level']['color'] }}22;color:{{ $me['level']['color'] }};border:1px solid {{ $me['level']['color'] }}44;">{{ $me['level']['emoji'] }} {{ $me['level']['name'] }}</span>
        </div>
    </div>
    <div class="my-rank-sep"></div>
    <div>
        <div style="font-size:1.1rem;font-weight:800;color:var(--text-primary);">{{ $me['badges_count'] }}</div>
        <div class="my-rank-label">badges earned</div>
    </div>
    <div style="margin-left:auto;font-size:0.78rem;color:var(--text-muted);">
        {{ $ranked->count() }} participants total
    </div>
</div>
@endif

{{-- Podium (top 3) --}}
@if($podium->count() > 0)
<div class="podium-row">
    @php
        $podiumOrder = [1 => null, 0 => null, 2 => null]; // centre, left, right
        if ($podium->count() >= 1) $podiumOrder[0] = $podium[0];
        if ($podium->count() >= 2) $podiumOrder[1] = $podium[1];
        if ($podium->count() >= 3) $podiumOrder[2] = $podium[2];
        $medals = [1=>'🥇', 2=>'🥈', 3=>'🥉'];
        $display = [$podiumOrder[1], $podiumOrder[0], $podiumOrder[2]]; // 2nd, 1st, 3rd
        $avatarColors = ['av-c0','av-c1','av-c2','av-c3','av-c4','av-c5','av-c6','av-c7'];
    @endphp
    @foreach($display as $p)
        @if($p)
        @php $ci = 'av-c' . (($p['rank'] - 1) % 8); @endphp
        <div class="podium-card rank-{{ $p['rank'] }}">
            @if($p['is_me'])<span class="podium-you">You</span>@endif
            <div class="podium-medal">{{ $medals[$p['rank']] }}</div>
            <div class="podium-avatar {{ $ci }}">{{ $p['initials'] }}</div>
            <div class="podium-name" title="{{ $p['name'] }}">{{ $p['name'] }}</div>
            <div class="podium-pts">{{ number_format($p['points']) }}<span> pts</span></div>
            <span class="podium-level" style="background:{{ $p['level']['color'] }}22;color:{{ $p['level']['color'] }};border:1px solid {{ $p['level']['color'] }}44;">
                {{ $p['level']['emoji'] }} {{ $p['level']['name'] }}
            </span>
        </div>
        @endif
    @endforeach
</div>
@endif

{{-- Full rankings table --}}
<div class="card">
    <div class="card-body" style="padding:0;">
        <div style="padding:1rem 1.25rem 0.75rem;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
            <div class="card-title" style="margin:0;">All Rankings</div>
            <div style="font-size:0.72rem;color:var(--text-faint);">{{ $ranked->count() }} users</div>
        </div>

        @if($ranked->count() === 0)
            <div class="lb-empty">No users found yet.</div>
        @else
        <div class="lb-list">
            {{-- Top 3 already shown in podium, style them distinctly --}}
            @foreach($ranked as $row)
            @php $ci = 'av-c' . (($row['rank'] - 1) % 8); @endphp
            <div class="lb-row {{ $row['is_me'] ? 'is-me' : '' }}">
                {{-- Rank --}}
                <div class="lb-rank">
                    @if($row['rank'] === 1) 🥇
                    @elseif($row['rank'] === 2) 🥈
                    @elseif($row['rank'] === 3) 🥉
                    @else <span style="color:var(--text-faint);font-size:0.8rem;">#{{ $row['rank'] }}</span>
                    @endif
                </div>
                {{-- User --}}
                <div class="lb-user">
                    <div class="lb-avatar {{ $ci }}">{{ $row['initials'] }}</div>
                    <div style="min-width:0;">
                        <div style="display:flex;align-items:center;flex-wrap:wrap;gap:0.3rem;">
                            <span class="lb-name">{{ $row['name'] }}</span>
                            @if($row['is_me'])<span class="lb-you-tag">You</span>@endif
                        </div>
                        <span class="lb-level-badge" style="background:{{ $row['level']['color'] }}22;color:{{ $row['level']['color'] }};border:1px solid {{ $row['level']['color'] }}44;">
                            {{ $row['level']['emoji'] }} {{ $row['level']['name'] }}
                        </span>
                    </div>
                </div>
                {{-- Points + badges --}}
                <div class="lb-right">
                    <div class="lb-pts">{{ number_format($row['points']) }}<span> pts</span></div>
                    <div class="lb-badges">🏅 {{ $row['badges_count'] }} badge{{ $row['badges_count'] !== 1 ? 's' : '' }}</div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

@endsection
