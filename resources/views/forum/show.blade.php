@extends('layouts.app')

@section('title', $post->title)

@section('styles')
<style>
.verified-badge {
    display:inline-flex;
    align-items:center;
    gap:4px;
    background:linear-gradient(135deg, #dbeafe, #ede9fe);
    color:#4A7CF6;
    padding:3px 10px;
    border-radius:20px;
    font-size:0.72rem;
    font-weight:700;
    letter-spacing:0.02em;
}
[data-theme="dark"] .verified-badge {
    background:rgba(74,124,246,0.18);
    color:#93b4fb;
}
.report-overlay {
    display:none;
    position:fixed;
    inset:0;
    background:rgba(0,0,0,0.45);
    z-index:999;
    justify-content:center;
    align-items:center;
}
.report-overlay.active { display:flex; }
.report-modal {
    background:var(--bg-card);
    border:1px solid var(--border);
    border-radius:12px;
    padding:1.5rem;
    width:100%;
    max-width:420px;
    box-shadow:0 12px 40px rgba(0,0,0,0.2);
}
.report-modal h3 { margin-bottom:1rem; font-size:1rem; }
.report-select {
    width:100%;
    padding:0.6rem 0.75rem;
    border:1px solid var(--border);
    border-radius:8px;
    background:var(--bg-base);
    color:var(--text-primary);
    font-family:inherit;
    font-size:0.875rem;
    margin-bottom:1rem;
}
.report-btn-row { display:flex; gap:0.5rem; justify-content:flex-end; }
.report-cancel {
    padding:0.5rem 1rem;
    border-radius:8px;
    border:1px solid var(--border);
    background:transparent;
    color:var(--text-muted);
    cursor:pointer;
    font-family:inherit;
    font-size:0.8125rem;
}
.report-submit {
    padding:0.5rem 1rem;
    border-radius:8px;
    border:none;
    background:#dc2626;
    color:#fff;
    cursor:pointer;
    font-family:inherit;
    font-weight:600;
    font-size:0.8125rem;
}
.alert-bar {
    padding:0.75rem 1rem;
    border-radius:8px;
    margin-bottom:1rem;
    font-size:0.85rem;
    font-weight:500;
}
.alert-success { background:#d1fae5; color:#065f46; }
.alert-error   { background:#fee2e2; color:#991b1b; }
[data-theme="dark"] .alert-success { background:rgba(5,150,105,0.2); color:#6ee7b7; }
[data-theme="dark"] .alert-error   { background:rgba(220,38,38,0.2); color:#fca5a5; }
</style>
@endsection

@section('content')

<a href="{{ route('forum.index') }}" style="display:inline-flex; align-items:center; gap:6px; text-decoration:none; color:var(--text-muted); font-size:0.875rem; margin-bottom:1rem; padding:6px 12px; border:1px solid var(--border); border-radius:8px; transition:all 0.15s;" onmouseover="this.style.background='var(--nav-hover-bg)'" onmouseout="this.style.background='transparent'">
    ← Back to Forum
</a>

{{-- Flash messages --}}
@if(session('success'))
    <div class="alert-bar alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert-bar alert-error">{{ session('error') }}</div>
@endif

<div class="card">
    <div class="card-body">

        <div style="display:flex; justify-content:space-between; align-items:flex-start;">

            <div>
                <h2>
                    {{ $post->title }}
                    @if($post->is_verified)
                        <span class="verified-badge">✓ Verified</span>
                    @endif
                </h2>

                <small style="color:var(--text-faint);">
                    Posted by {{ $post->user->name }}
                    • {{ $post->created_at->diffForHumans() }}
                </small>
            </div>

            <div style="display:flex; gap:0.5rem; align-items:center;">

                {{-- Report button (visible to everyone EXCEPT post owner) --}}
                @if(auth()->id() !== $post->user_id)
                    <button
                        onclick="document.getElementById('reportOverlay').classList.add('active')"
                        style="
                            background:none;
                            border:1px solid var(--border);
                            border-radius:8px;
                            padding:6px 12px;
                            cursor:pointer;
                            font-size:13px;
                            color:var(--text-muted);
                            display:flex;
                            align-items:center;
                            gap:4px;
                        ">
                        🚩 Report
                    </button>
                @endif

                {{-- Owner menu (edit / delete) — ONLY for post owner --}}
                @if(auth()->id() == $post->user_id)

                    <div style="position:relative;">

                        <button
                            onclick="toggleMenu(event)"
                            style="
                                background:none;
                                border:none;
                                cursor:pointer;
                                font-size:22px;
                                color:var(--text-muted);
                            ">
                            ⋮
                        </button>

                        <div
                            id="postMenu"
                            style="
                                display:none;
                                position:absolute;
                                right:0;
                                top:35px;
                                background:var(--bg-card);
                                border:1px solid var(--border);
                                border-radius:8px;
                                min-width:140px;
                                box-shadow:0 4px 10px rgba(0,0,0,0.1);
                                z-index:100;
                            ">

                            @if($post->created_at->diffInHours(now()) < 1)

                                <a
                                    href="{{ route('forum.edit', $post->id) }}"
                                    style="
                                        display:block;
                                        padding:10px;
                                        text-decoration:none;
                                        color:var(--text-primary);
                                    ">
                                    Edit Post
                                </a>

                            @else

                                <button
                                    type="button"
                                    onclick="alert('This post can only be edited within 1 hour of creation.')"
                                    style="
                                        width:100%;
                                        border:none;
                                        background:none;
                                        text-align:left;
                                        padding:10px;
                                        color:#9ca3af;
                                        cursor:not-allowed;
                                    ">
                                    Edit Post (Expired)
                                </button>

                            @endif

                            <form
                                method="POST"
                                action="{{ route('forum.destroy', $post->id) }}">

                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    onclick="return confirm('Delete this post?')"
                                    style="
                                        width:100%;
                                        border:none;
                                        background:none;
                                        text-align:left;
                                        padding:10px;
                                        cursor:pointer;
                                        color:red;
                                    ">
                                    Delete Post
                                </button>

                            </form>

                        </div>

                    </div>

                @endif
            </div>

        </div>

        <p style="margin-top:20px; line-height:1.7;">
            {{ $post->content }}
        </p>

    </div>
</div>

<br>

<div class="card">
    <div class="card-body">

        <h3>Comments</h3>

        <form method="POST" action="{{ route('forum.comment.store', $post->id) }}">
            @csrf

            <textarea
                name="content"
                required
                placeholder="Write a comment..."
                style="
                    width:100%;
                    height:80px;
                    padding:12px;
                    border:1px solid var(--border);
                    border-radius:8px;
                    background:var(--bg-base);
                    color:var(--text-primary);
                    font-family:inherit;
                    font-size:14px;
                    resize:none;
                "></textarea>

            <button
                type="submit"
                class="btn-primary"
                style="margin-top:10px;">
                Reply
            </button>
        </form>

        <hr style="margin:20px 0;">

        @foreach($post->comments as $comment)

            <div style="padding:10px 0; border-bottom:1px solid var(--border);">

                <strong>{{ $comment->user->name }}</strong>

                <p style="margin-top:5px;">
                    {{ $comment->content }}
                </p>

                <small style="color:var(--text-faint);">
                    Votes: {{ $comment->votes }}
                </small>

            </div>

        @endforeach

    </div>
</div>

{{-- ==================== REPORT MODAL (PBI #55) ==================== --}}
@if(auth()->id() !== $post->user_id)
<div id="reportOverlay" class="report-overlay">
    <div class="report-modal">
        <h3>🚩 Report Post</h3>
        <p style="font-size:0.8125rem; color:var(--text-muted); margin-bottom:1rem;">
            Why are you reporting this post?
        </p>
        <form method="POST" action="{{ route('forum.report', $post->id) }}">
            @csrf
            <select name="reason" class="report-select" required>
                <option value="" disabled selected>Select a reason…</option>
                <option value="Spam or misleading">Spam or misleading</option>
                <option value="Inappropriate or offensive content">Inappropriate or offensive content</option>
                <option value="Misinformation or inaccurate data">Misinformation or inaccurate data</option>
                <option value="Harassment or bullying">Harassment or bullying</option>
                <option value="Off-topic or irrelevant">Off-topic or irrelevant</option>
                <option value="Other">Other</option>
            </select>
            <div class="report-btn-row">
                <button type="button" class="report-cancel"
                    onclick="document.getElementById('reportOverlay').classList.remove('active')">
                    Cancel
                </button>
                <button type="submit" class="report-submit">Submit Report</button>
            </div>
        </form>
    </div>
</div>
@endif

<script>

function toggleMenu(event)
{
    event.stopPropagation();

    const menu = document.getElementById('postMenu');

    if(menu.style.display === 'block')
    {
        menu.style.display = 'none';
    }
    else
    {
        menu.style.display = 'block';
    }
}

window.addEventListener('click', function(e)
{
    const menu = document.getElementById('postMenu');

    if(menu)
    {
        menu.style.display = 'none';
    }

    // Also close report modal if clicking outside
    const overlay = document.getElementById('reportOverlay');
    if(overlay && e.target === overlay)
    {
        overlay.classList.remove('active');
    }
});

</script>

@endsection
