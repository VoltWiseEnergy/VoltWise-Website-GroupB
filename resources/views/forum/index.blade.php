@extends('layouts.app')

@section('title', 'Forum')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">Community Forum</h1>
        <p class="page-subtitle">
            Share energy-saving discussions
        </p>
    </div>

<div style="display:flex; gap:10px; align-items:center;">

    <form method="GET" action="{{ route('forum.index') }}">

        <select
            name="sort"
            onchange="this.form.submit()"
            style="
                padding:10px 14px;
                border:1px solid var(--border);
                border-radius:8px;
                background:var(--bg-card);
                color:var(--text-primary);
                cursor:pointer;
            ">

            <option value=""
                {{ request('sort') == null ? 'selected' : '' }}>
                Newest
            </option>

            <option value="top"
                {{ request('sort') == 'top' ? 'selected' : '' }}>
                Most Liked
            </option>

        </select>

    </form>

    <a href="{{ route('forum.create') }}"
       class="btn-primary">
        Create Post
    </a>

</div>

</div>

<div style="display:flex; flex-direction:column; gap:1rem;">

@forelse($posts as $post)

    @php
        $userVote = $post->userVotes->first()?->vote;
    @endphp

    <div class="card">
        <div class="card-body">

            <div style="display:flex; gap:1rem;">

                {{-- VOTING --}}
                <div style="display:flex; flex-direction:column; align-items:center; gap:0.5rem; min-width:50px;">

                    <form method="POST"
                          action="{{ route('forum.upvote', $post->id) }}">
                        @csrf

                        <button
                            class="topbar-icon-btn"
                            type="submit"
                            style="
                                {{ $userVote == 1 ? 'color:#f97316;font-weight:bold;' : '' }}
                            ">
                            ▲
                        </button>

                    </form>

                    <span style="font-weight:700;">
                        {{ $post->votes }}
                    </span>

                    <form method="POST"
                          action="{{ route('forum.downvote', $post->id) }}">
                        @csrf

                        <button
                            class="topbar-icon-btn"
                            type="submit"
                            style="
                                {{ $userVote == -1 ? 'color:#f97316;font-weight:bold;' : '' }}
                            ">
                            ▼
                        </button>

                    </form>

                </div>

                {{-- POST CONTENT --}}
                <div style="flex:1;">

                    <a href="{{ route('forum.show', $post->id) }}"
                       style="text-decoration:none; color:inherit;">

                        <div style="font-size:0.75rem; color:var(--text-faint); margin-bottom:0.5rem;">
                            Posted by {{ $post->user->name ?? 'Unknown User' }}
                            •
                            {{ optional($post->created_at)->diffForHumans() }}
                            •
                            💬 {{ $post->comments_count }}
                            {{ $post->comments_count == 1 ? 'comment' : 'comments' }}
                        </div>

                        <h2 style="font-size:1.1rem; font-weight:700; margin-bottom:0.75rem;">
                            {{ $post->title }}
                        </h2>

                        <p style="color:var(--text-muted); line-height:1.6;">
                            {{ Str::limit($post->content, 250) }}
                        </p>

                    </a>

                </div>

            </div>

        </div>
    </div>

@empty

    <div class="card">
        <div class="card-body">
            No forum posts yet.
        </div>
    </div>

@endforelse

</div>

@endsection
