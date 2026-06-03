@extends('layouts.app')
@section('title', 'Forum Reports')
@section('styles')
<style>
.report-table { width:100%; border-collapse:collapse; }
.report-table th { text-align:left; padding:0.6rem 0.75rem; font-size:0.72rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.06em; border-bottom:1px solid var(--border); }
.report-table td { padding:0.75rem; font-size:0.8125rem; color:var(--text-secondary); border-bottom:1px solid var(--border); vertical-align:middle; }
.report-table tr:hover td { background:rgba(74,124,246,0.04); }
.badge-pending { background:#fef9c3; color:#ca8a04; padding:0.2rem 0.55rem; border-radius:20px; font-size:0.68rem; font-weight:600; }
.badge-reviewed { background:#d1fae5; color:#059669; padding:0.2rem 0.55rem; border-radius:20px; font-size:0.68rem; font-weight:600; }
.badge-dismissed { background:var(--icon-btn-bg); color:var(--text-faint); padding:0.2rem 0.55rem; border-radius:20px; font-size:0.68rem; font-weight:600; }
[data-theme="dark"] .badge-pending { background:rgba(202,138,4,0.2); color:#fde047; }
[data-theme="dark"] .badge-reviewed { background:rgba(5,150,105,0.2); color:#6ee7b7; }
.review-actions { display:flex; gap:0.375rem; }
.review-btn { padding:0.3rem 0.6rem; border-radius:6px; border:none; cursor:pointer; font-size:0.72rem; font-weight:600; font-family:'Inter',sans-serif; }
.review-btn-hide { background:#fee2e2; color:#dc2626; }
.review-btn-dismiss { background:var(--icon-btn-bg); color:var(--text-muted); }
.pagination-wrap { display:flex; justify-content:center; gap:0.375rem; margin-top:1rem; }
.pagination-wrap a, .pagination-wrap span { padding:0.4rem 0.75rem; border-radius:6px; font-size:0.8125rem; text-decoration:none; border:1px solid var(--border); color:var(--text-muted); }
.pagination-wrap .active-page { background:var(--blue-600); color:#fff; border-color:var(--blue-600); }
</style>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Report Queue</h1>
        <p class="page-subtitle">Review and take action on reported posts</p>
    </div>
    <a href="{{ route('admin.forum.index') }}" class="btn-primary" style="text-decoration:none;">← All Posts</a>
</div>

<div class="card">
    <div class="card-body">
        @if($reports->isEmpty())
            <div class="empty-state" style="min-height:150px;"><p>No reports to review.</p></div>
        @else
        <table class="report-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Post</th>
                    <th>Reported By</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reports as $i => $report)
                <tr>
                    <td style="color:var(--text-faint);">{{ $reports->firstItem() + $i }}</td>
                    <td style="font-weight:600;color:var(--text-primary);">{{ Str::limit($report->post->title ?? 'Deleted', 30) }}</td>
                    <td>{{ $report->user->name ?? 'Unknown' }}</td>
                    <td>{{ Str::limit($report->reason, 50) }}</td>
                    <td><span class="badge-{{ $report->status }}">{{ $report->status }}</span></td>
                    <td style="font-size:0.75rem;color:var(--text-faint);">{{ $report->created_at->diffForHumans() }}</td>
                    <td>
                        @if($report->status === 'pending')
                        <div class="review-actions">
                            <form method="POST" action="{{ route('admin.forum.reports.review', $report) }}">
                                @csrf
                                <input type="hidden" name="action" value="hide_post">
                                <button type="submit" class="review-btn review-btn-hide">Hide Post</button>
                            </form>
                            <form method="POST" action="{{ route('admin.forum.reports.review', $report) }}">
                                @csrf
                                <input type="hidden" name="action" value="dismiss">
                                <button type="submit" class="review-btn review-btn-dismiss">Dismiss</button>
                            </form>
                        </div>
                        @else
                            <span style="font-size:0.72rem;color:var(--text-faint);">{{ $report->status }}</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($reports->hasPages())
        <div class="pagination-wrap">
            @foreach($reports->getUrlRange(1, $reports->lastPage()) as $page => $url)
                @if($page == $reports->currentPage())
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
