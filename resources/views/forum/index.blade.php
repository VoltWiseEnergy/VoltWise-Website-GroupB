@extends('layouts.app')

@section('title', 'Forum')

@section('styles')
<style>
.verified-badge-sm {
    display:inline-flex;
    align-items:center;
    gap:3px;
    background:linear-gradient(135deg, #dbeafe, #ede9fe);
    color:#4A7CF6;
    padding:2px 8px;
    border-radius:20px;
    font-size:0.65rem;
    font-weight:700;
    margin-left:6px;
    vertical-align:middle;
}
[data-theme="dark"] .verified-badge-sm {
    background:rgba(74,124,246,0.18);
    color:#93b4fb;
}
</style>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">Community Forum</h1>
        <p class="page-subtitle">Share energy-saving discussions</p>
    </div>

    <a href="{{ route('forum.create') }}" class="btn-primary">
        Create Post
    </a>
</div>

<div style="display:flex; flex-direction:column; gap:1rem;">

    @forelse($posts as $post)

        <a href="{{ route('forum.show', $post->id) }}" style="text-decoration:none; color:inherit;">

            <div class="card">
                <div class="card-body">

                    <div style="display:flex; gap:1rem;">

                        <div style="display:flex; flex-direction:column; align-items:center; gap:0.5rem; min-width:50px;">

                            <button class="topbar-icon-btn" type="button">▲</button>

                            <span style="font-weight:700;">
                                {{ $post->votes }}
                            </span>

                            <button class="topbar-icon-btn" type="button">▼</button>

                        </div>

                        <div style="flex:1;">

                            <div style="font-size:0.75rem; color:var(--text-faint); margin-bottom:0.5rem;">
                                Posted by {{ $post->user->name ?? 'Unknown User' }}
                                •
                                {{ optional($post->created_at)->diffForHumans() }}
                            </div>

                            <h2 style="font-size:1.1rem; font-weight:700; margin-bottom:0.75rem;">
                                {{ $post->title }}
                                @if($post->is_verified)
                                    <span class="verified-badge-sm">✓ Verified</span>
                                @endif
                            </h2>

                            <p style="color:var(--text-muted); line-height:1.6;">
                                {{ $post->content }}
                            </p>

                        </div>

                    </div>

                </div>
            </div>

        </a>

    @empty

        <div class="card">
            <div class="card-body">
                No forum posts yet.
            </div>
        </div>

    @endforelse

</div>

@endsection
