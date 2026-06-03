@extends('layouts.app')
@section('title', 'Forum Moderation')
@section('styles')
<style>
.filter-tabs { display:flex; gap:0.5rem; margin-bottom:1.25rem; }
.filter-tab { padding:0.45rem 1rem; border-radius:8px; text-decoration:none; font-size:0.8125rem; font-weight:600; color:var(--text-muted); border:1px solid var(--border); transition:all 0.15s; }
.filter-tab:hover { background:var(--nav-hover-bg); color:var(--text-primary); }
.filter-tab.active { background:var(--blue-600); color:#fff; border-color:var(--blue-600); }
.mod-table { width:100%; border-collapse:collapse; }
.mod-table th { text-align:left; padding:0.6rem 0.75rem; font-size:0.72rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.06em; border-bottom:1px solid var(--border); }
.mod-table td { padding:0.75rem; font-size:0.8125rem; color:var(--text-secondary); border-bottom:1px solid var(--border); vertical-align:middle; }
.mod-table tr:hover td { background:rgba(74,124,246,0.04); }
.badge-status { padding:0.2rem 0.55rem; border-radius:20px; font-size:0.68rem; font-weight:600; }
.badge-published { background:#d1fae5; color:#059669; }
.badge-hidden { background:#fee2e2; color:#dc2626; }
.badge-reported { background:#fef9c3; color:#ca8a04; }
.badge-verified { background:#dbeafe; color:#4A7CF6; }
[data-theme="dark"] .badge-published { background:rgba(5,150,105,0.2); color:#6ee7b7; }
[data-theme="dark"] .badge-hidden { background:rgba(220,38,38,0.2); color:#fca5a5; }
[data-theme="dark"] .badge-reported { background:rgba(202,138,4,0.2); color:#fde047; }
[data-theme="dark"] .badge-verified { background:rgba(74,124,246,0.2); color:#93b4fb; }
.mod-actions { display:flex; gap:0.375rem; }
.mod-btn { padding:0.3rem 0.6rem; border-radius:6px; border:none; cursor:pointer; font-size:0.72rem; font-weight:600; font-family:'Inter',sans-serif; transition:opacity 0.15s; }
.mod-btn-verify { background:var(--icon-blue-bg); color:var(--icon-blue-fg); }
.mod-btn-hide { background:#fee2e2; color:#dc2626; }
.pagination-wrap { display:flex; justify-content:center; gap:0.375rem; margin-top:1rem; }
.pagination-wrap a, .pagination-wrap span { padding:0.4rem 0.75rem; border-radius:6px; font-size:0.8125rem; text-decoration:none; border:1px solid var(--border); color:var(--text-muted); }
.pagination-wrap .active-page { background:var(--blue-600); color:#fff; border-color:var(--blue-600); }
</style>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Forum Moderation</h1>
        <p class="page-subtitle">Manage forum posts, verify information, and review reports</p>
    </div>
    <a href="{{ route('admin.forum.reports') }}" class="btn-primary" style="text-decoration:none;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;">
            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
            <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
        </svg>
        View Reports
    </a>
</div>

<div class="filter-tabs">
    <a href="{{ route('admin.forum.index', ['filter'=>'all']) }}" class="filter-tab {{ $filter==='all' ? 'active' : '' }}">All Posts</a>
    <a href="{{ route('admin.forum.index', ['filter'=>'reported']) }}" class="filter-tab {{ $filter==='reported' ? 'active' : '' }}">Reported</a>
    <a href="{{ route('admin.forum.index', ['filter'=>'verified']) }}" class="filter-tab {{ $filter==='verified' ? 'active' : '' }}">Verified</a>
    <a href="{{ route('admin.forum.index', ['filter'=>'hidden']) }}" class="filter-tab {{ $filter==='hidden' ? 'active' : '' }}">Hidden</a>
</div>

<div class="card">
    <div class="card-body">
        @if($posts->isEmpty())
            <div class="empty-state" style="min-height:150px;">
                <p>No posts found for this filter.</p>
            </div>
        @else
        <table class="mod-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Reports</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($posts as $i => $post)
                <tr>
                    <td style="color:var(--text-faint);font-size:0.72rem;">{{ $posts->firstItem() + $i }}</td>
                    <td>
                        <a href="{{ route('forum.show', $post->id) }}" style="font-weight:600;color:var(--text-primary);text-decoration:none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">{{ Str::limit($post->title, 40) }}</a>
                    </td>
                    <td>{{ $post->user->name ?? 'Unknown' }}</td>
                    <td>
                        <span class="badge-status badge-{{ $post->status }}">{{ $post->status }}</span>
                        @if($post->is_verified)
                            <span class="badge-status badge-verified">✓ verified</span>
                        @endif
                    </td>
                    <td>{{ $post->reports_count }}</td>
                    <td style="font-size:0.75rem;color:var(--text-faint);">{{ $post->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="mod-actions">
                            <form method="POST" action="{{ route('admin.forum.verify', $post) }}" style="display:inline;">
                                @csrf
                                <button type="submit" class="mod-btn mod-btn-verify">
                                    {{ $post->is_verified ? 'Unverify' : 'Verify' }}
                                </button>
                            </form>
                            @if($post->status !== 'hidden')
                            <form method="POST" action="{{ route('admin.forum.destroy', $post) }}" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="mod-btn mod-btn-hide" onclick="return confirm('Hide this post?')">Hide</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($posts->hasPages())
        <div class="pagination-wrap">
            @foreach($posts->getUrlRange(1, $posts->lastPage()) as $page => $url)
                @if($page == $posts->currentPage())
                    <span class="active-page">{{ $page }}</span>
                @else
                    <a href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach
        </div>
        @endif
        @endif
    </div>
</div>
@endsection
