@extends('layouts.app')

@section('title', $post->title)

@section('content')

<div class="card">
    <div class="card-body">

        <div style="display:flex; justify-content:space-between; align-items:flex-start;">

            <div>
                <h2>{{ $post->title }}</h2>

                <small style="color:var(--text-faint);">
                    Posted by {{ $post->user->name }}
                </small>
            </div>

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

window.addEventListener('click', function()
{
    const menu = document.getElementById('postMenu');

    if(menu)
    {
        menu.style.display = 'none';
    }
});

</script>

@endsection