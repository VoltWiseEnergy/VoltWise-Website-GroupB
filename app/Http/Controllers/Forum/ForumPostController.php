<?php

namespace App\Http\Controllers\Forum;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ForumPost;
use App\Models\ForumComment;
use App\Models\ForumPostVote;

class ForumPostController extends Controller
{
    // Show all posts
    public function index(Request $request)
    {
        $sort = $request->get('sort');

        $posts = ForumPost::with([
            'user',
            'userVotes' => function ($query) {
                $query->where('user_id', auth()->id());
            }
        ])->withCount('comments');

        if ($sort === 'top') {
            $posts->orderByDesc('votes');
        } else {
            $posts->latest();
        }

        $posts = $posts->get();
use App\Models\ForumReport;

class ForumPostController extends Controller
{
    // Show all posts (hide hidden posts from regular users)
    public function index()
    {
        $posts = ForumPost::with('user')
            ->where('status', '!=', 'hidden')
            ->latest()
            ->get();

        return view('forum.index', compact('posts', 'sort'));
    }

    // Create page
    public function create()
    {
        return view('forum.create');
    }

    // Store post
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required'
        ]);

        ForumPost::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'content' => $request->content,
            'votes' => 0
        ]);

        return redirect()
            ->route('forum.index')
            ->with('success', 'Post created successfully. You can edit this post for up to 1 hour after creation.');
    }

    // Show single post
    public function show($id)
    {
        $post = ForumPost::with([
            'user',
            'comments.user'
        ])->findOrFail($id);

        return view('forum.show', compact('post'));
    }

    // Store comment
    public function storeComment(Request $request, $id)
    {
        $request->validate([
            'content' => 'required'
        ]);

        ForumComment::create([
            'forum_post_id' => $id,
            'user_id' => auth()->id(),
            'content' => $request->content,
            'votes' => 0
        ]);

        return redirect()->back();
    }

    // Edit page
    public function edit($id)
    {
        $post = ForumPost::findOrFail($id);

        if ($post->user_id !== auth()->id()) {
            abort(403);
        }

        if ($post->created_at->diffInHours(now()) >= 1) {
            return redirect()
                ->route('forum.index')
                ->with('success', 'Edit time expired.');
        }

        return view('forum.edit', compact('post'));
    }

    // Update post
    public function update(Request $request, $id)
    {
        $post = ForumPost::findOrFail($id);

        if ($post->user_id !== auth()->id()) {
            abort(403);
        }

        if ($post->created_at->diffInHours(now()) >= 1) {
            return redirect()
                ->route('forum.index')
                ->with('success', 'Edit time expired.');
        }

        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required'
        ]);

        $post->update([
            'title' => $request->title,
            'content' => $request->content
        ]);

        return redirect()
            ->route('forum.show', $post->id)
            ->with('success', 'Post updated successfully.');
    }

    // Delete post
    public function destroy($id)
    {
        $post = ForumPost::findOrFail($id);

        if ($post->user_id !== auth()->id()) {
            abort(403);
        }

        $post->delete();

        return redirect()
            ->route('forum.index')
            ->with('success', 'Post deleted successfully.');
    }

    // UPVOTE
    public function upvote($id)
    {
        $post = ForumPost::findOrFail($id);

        $vote = ForumPostVote::where('forum_post_id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$vote) {
            ForumPostVote::create([
                'forum_post_id' => $id,
                'user_id' => auth()->id(),
                'vote' => 1
            ]);

            $post->increment('votes');
        } elseif ($vote->vote == 1) {
            $vote->delete();

            $post->decrement('votes');
        } else {
            $vote->update([
                'vote' => 1
            ]);

            $post->increment('votes', 2);
        }

        return back();
    }

    // DOWNVOTE
    public function downvote($id)
    {
        $post = ForumPost::findOrFail($id);

        $vote = ForumPostVote::where('forum_post_id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$vote) {
            ForumPostVote::create([
                'forum_post_id' => $id,
                'user_id' => auth()->id(),
                'vote' => -1
            ]);

            $post->decrement('votes');
        } elseif ($vote->vote == -1) {
            $vote->delete();

            $post->increment('votes');
        } else {
            $vote->update([
                'vote' => -1
            ]);

            $post->decrement('votes', 2);
        }

        return back();
    }
}
    // PBI #55 — User: Report a post
    public function report(Request $request, $id)
    {
        $post = ForumPost::findOrFail($id);

        // Cannot report own post
        if ($post->user_id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'You cannot report your own post.');
        }

        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        // Prevent duplicate reports from the same user
        $alreadyReported = ForumReport::where('post_id', $id)
            ->where('user_id', auth()->id())
            ->exists();

        if ($alreadyReported) {
            return redirect()->back()
                ->with('error', 'You have already reported this post.');
        }

        ForumReport::create([
            'post_id' => $id,
            'user_id' => auth()->id(),
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        // Mark the post as reported
        if ($post->status === 'published') {
            $post->update(['status' => 'reported']);
        }

        return redirect()->back()
            ->with('success', 'Post has been reported. Thank you for helping keep the community safe.');
    }
}
