<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LeaderboardController extends Controller
{
    /** Point thresholds for level display */
    const LEVELS = [
        ['name' => 'Bronze',   'emoji' => '🥉', 'min' => 0,    'color' => '#cd7f32'],
        ['name' => 'Silver',   'emoji' => '🥈', 'min' => 100,  'color' => '#94a3b8'],
        ['name' => 'Gold',     'emoji' => '🥇', 'min' => 500,  'color' => '#f59e0b'],
        ['name' => 'Platinum', 'emoji' => '💎', 'min' => 1500, 'color' => '#8b5cf6'],
    ];

    public function index()
    {
        $me = Auth::user();

        // Fetch all users ranked by points, eager-load badge count
        $ranked = User::withCount('badges')
            ->orderByDesc('points')
            ->get()
            ->map(function ($user, $index) use ($me) {
                $level = $this->resolveLevel($user->points ?? 0);
                return [
                    'rank'         => $index + 1,
                    'id'           => $user->id,
                    'name'         => $user->name,
                    'initials'     => strtoupper(substr($user->name, 0, 2)),
                    'points'       => $user->points ?? 0,
                    'badges_count' => $user->badges_count,
                    'level'        => $level,
                    'is_me'        => $user->id === $me->id,
                ];
            });

        // Current user's rank
        $myRank = $ranked->firstWhere('is_me', true)['rank'] ?? null;

        // Top 3 podium + full list
        $podium   = $ranked->take(3);
        $restList = $ranked->slice(3)->values();

        return view('leaderboard.index', compact('ranked', 'podium', 'restList', 'myRank'));
    }

    private function resolveLevel(int $pts): array
    {
        $level = self::LEVELS[0];
        foreach (self::LEVELS as $l) {
            if ($pts >= $l['min']) $level = $l;
        }
        return $level;
    }
}
