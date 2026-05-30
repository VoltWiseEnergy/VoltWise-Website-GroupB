@extends('layouts.app')

@section('title', 'Edit Post')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">Edit Post</h1>
        <p class="page-subtitle">Update your forum post</p>
    </div>
</div>

<div class="card">
    <div class="card-body">

        <form method="POST" action="{{ route('forum.update', $post->id) }}">
            @csrf
            @method('PUT')

            <div style="margin-bottom:1rem;">
                <label>Title</label>

                <input
                    type="text"
                    name="title"
                    value="{{ $post->title }}"
                    required
                    style="width:100%; padding:0.75rem; border:1px solid #ccc; border-radius:8px;">
            </div>

            <div style="margin-bottom:1rem;">
                <label>Content</label>

                <textarea
                    name="content"
                    required
                    style="width:100%; height:200px; padding:0.75rem; border:1px solid #ccc; border-radius:8px; font-family:inherit;">{{ $post->content }}</textarea>
            </div>

            <button type="submit" class="btn-primary">
                Save Changes
            </button>

        </form>

    </div>
</div>

@endsection