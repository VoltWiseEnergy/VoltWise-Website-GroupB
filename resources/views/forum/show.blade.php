@extends('layouts.app')

@section('title', $post->title)

@section('content')

<style>
    textarea[name="content"] {
        font-family: inherit;
    }
</style>

<div class="card">
    <div class="card-body">
        <h2>{{ $post->title }}</h2>
        <p style="margin-top:10px;">{{ $post->content }}</p>
        <small>Posted by {{ $post->user->name }}</small>
    </div>
</div>

<br>

<div class="card">
    <div class="card-body">

        <h3>Comments</h3>

        <form method="POST" action="{{ route('forum.comment.store', $post->id) }}">
            @csrf
            <textarea name="content" required placeholder="Write a comment..."
                style="width:100%;height:80px;"></textarea>

            <button type="submit" class="btn-primary" style="margin-top:10px;">
                Reply
            </button>
        </form>

        <hr>

        @foreach($post->comments as $comment)
            <div style="padding:10px;border-bottom:1px solid #eee;">
                <strong>{{ $comment->user->name }}</strong>
                <p>{{ $comment->content }}</p>
                <small>Votes: {{ $comment->votes }}</small>
            </div>
        @endforeach

    </div>
</div>

@endsection