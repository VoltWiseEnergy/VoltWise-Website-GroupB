<?php

namespace App\Http\Controllers\Forum;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ForumPost;
use App\Models\ForumComment;

class ForumPostController extends Controller
{
    // Show all posts (forum homepage)
    public function index()
    {
        $posts = ForumPost::with('user')
            ->latest()
            ->get();

        return view('forum.index', compact('posts'));
    }

    // Show create post form
    public function create()
    {
        return view('forum.create');
    }

    // Store new post
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
            ->with('success', 'Post created successfully.');
    }

    // Show single post (Reddit-style detail page)
    public function show($id)
    {
        $post = ForumPost::with(['user', 'comments.user'])
            ->findOrFail($id);

        return view('forum.show', compact('post'));
    }

    // Store comment under a post
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
}