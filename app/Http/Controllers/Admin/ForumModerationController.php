<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForumPost;
use App\Models\ForumReport;
use Illuminate\Http\Request;

class ForumModerationController extends Controller
{
    
    //PBI #52/#53 — Admin: list all posts with moderation tools.
    //GET /admin/forum
    
    public function index(Request $request)
    {
        $filter = $request->input('filter', 'all');

        $query = ForumPost::with('user')->withCount('reports');

        if ($filter === 'reported') {
            $query->where('status', 'reported')
                  ->orWhereHas('reports', fn($q) => $q->where('status', 'pending'));
        } elseif ($filter === 'verified') {
            $query->where('is_verified', true);
        } elseif ($filter === 'hidden') {
            $query->where('status', 'hidden');
        }

        $posts = $query->orderByDesc('created_at')->paginate(15);

        return view('admin.forum.index', compact('posts', 'filter'));
    }

    
    //PBI #52 — Admin: delete/hide inappropriate post.
    //DELETE /admin/forum/{post}
    
    public function destroy(ForumPost $post)
    {
        $post->update(['status' => 'hidden']);

        // Mark all pending reports for this post as reviewed
        $post->reports()->where('status', 'pending')->update(['status' => 'reviewed']);

        return redirect()->route('admin.forum.index')
            ->with('success', 'Post has been hidden from the forum.');
    }

    
    //PBI #53 — Admin: toggle verified/unverified status.
    //POST /admin/forum/{post}/verify
    
    public function toggleVerified(ForumPost $post)
    {
        $post->update(['is_verified' => !$post->is_verified]);

        $status = $post->is_verified ? 'verified' : 'unverified';

        return redirect()->back()
            ->with('success', "Post marked as {$status}.");
    }

    
     //PBI #55 — Admin: view all reports.
     //GET /admin/forum/reports
     
    public function reports()
    {
        $reports = ForumReport::with(['post.user', 'user'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.forum.reports', compact('reports'));
    }


// PBI #55 — Admin: review (action or dismiss) a report.
// POST /admin/forum/reports/{report}/review

    public function reviewReport(Request $request, ForumReport $report)
    {
        $request->validate([
            'action' => 'required|in:dismiss,hide_post',
        ]);

        if ($request->action === 'hide_post') {
            $report->post->update(['status' => 'hidden']);
            $report->update(['status' => 'reviewed']);
            ForumReport::where('post_id', $report->post_id)
                ->where('status', 'pending')
                ->update(['status' => 'reviewed']);
        } else {
            $report->update(['status' => 'dismissed']);
        }

        return redirect()->route('admin.forum.reports')
            ->with('success', 'Report has been processed.');
    }
}
