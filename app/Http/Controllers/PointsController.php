<?php

namespace App\Http\Controllers;

use App\Services\PointService;
use Illuminate\Support\Facades\Auth;

class PointsController extends Controller
{
    public function __construct(private PointService $pointService) {}

    /**
     * Display the full points history & level page.
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

        return view('points.index', compact(
            'totalPoints',
            'level',
            'historyByDate',
            'todayAwards'
        ));
    }
}
