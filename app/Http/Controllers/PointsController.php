<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Services\PointService;
use Illuminate\Support\Facades\Auth;

class PointsController extends Controller
{
    public function __construct(private PointService $pointService) {}

    /**
     * Display the full points history, level, and badges page.
     * GET /points
     */
    public function index()
    {
        $user        = Auth::user();
        $totalPoints = $user->points ?? 0;
        $level       = $this->pointService->getLevel($totalPoints);
        $history     = $this->pointService->getHistory($user, 30);
        $todayAwards = $this->pointService->getTodayAwards($user);

        // Group history by date for display
        $historyByDate = $history->groupBy(fn($log) => $log->log_date->toDateString());

        // All badge definitions (for locked/unlocked display)
        $allBadges    = Badge::all();
        // Badges the user has actually earned (keyed by badge_id for fast lookup)
        $earnedBadges = $user->badges()->withPivot('earned_at')->get()->keyBy('id');

        return view('points.index', compact(
            'totalPoints',
            'level',
            'historyByDate',
            'todayAwards',
            'allBadges',
            'earnedBadges'
        ));
    }
}
