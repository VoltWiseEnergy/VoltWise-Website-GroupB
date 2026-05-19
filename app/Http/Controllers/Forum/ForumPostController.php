<?php

namespace App\Http\Controllers\Forum;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ForumPost;

class ForumPostController extends Controller
{
    public function index()
    {
        $posts = ForumPost::with('user')
            ->latest()
            ->get();

        return view('forum.index', compact('posts'));
    }

    public function create()
    {
        return view('forum.create');
    }

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
}