@extends('layouts.app')

@section('title', 'Create Post')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">Create Forum Post</h1>
        <p class="page-subtitle">
            Share your thoughts with the community
        </p>
    </div>
</div>

<div class="card">
    <div class="card-body">

        @if ($errors->any())
            <div style="
                background:#fee2e2;
                color:#b91c1c;
                padding:12px;
                border-radius:8px;
                margin-bottom:16px;
                font-size:14px;
            ">
                <ul style="margin:0; padding-left:20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div style="
            background:#fef3c7;
            color:#92400e;
            padding:12px;
            border-radius:8px;
            margin-bottom:16px;
            font-size:14px;
        ">
            ⚠️ Posts can only be edited within 1 hour after creation.
        </div>

        <form method="POST"
              action="{{ route('forum.store') }}"
              novalidate>

            @csrf

            <div style="margin-bottom:1rem;">

                <label class="modal-label">
                    Title
                </label>

                <input
                    type="text"
                    name="title"
                    value="{{ old('title') }}"
                    required
                    style="
                        width:100%;
                        padding:0.75rem;
                        border:1px solid var(--border);
                        border-radius:8px;
                        background:var(--bg-card);
                        color:var(--text-primary);
                    "
                >

            </div>

            <div style="margin-bottom:1rem;">

                <label class="modal-label">
                    Content
                </label>

                <textarea
                    name="content"
                    rows="8"
                    required
                    style="
                        width:100%;
                        padding:0.75rem;
                        border:1px solid var(--border);
                        border-radius:8px;
                        background:var(--bg-card);
                        color:var(--text-primary);
                        resize:none;
                    "
                >{{ old('content') }}</textarea>

            </div>

            <button type="submit" class="btn-primary">
                Post Discussion
            </button>

        </form>

    </div>
</div>

@endsection