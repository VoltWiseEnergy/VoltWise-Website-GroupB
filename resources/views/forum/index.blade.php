@extends('layouts.app')

@section('title', 'Forum')

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