<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\User;
use App\Models\UserBadge;
use App\Models\UserPointLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BadgeService
{
    /**
     * Check all badge conditions for the user and award any newly-earned badges.
     * Returns an array of Badge models that were just awarded (empty if none).
     */
    public function checkAndAwardBadges(User $user): array
    {
        $allBadges   = Badge::all()->keyBy('key');
        $newlyEarned = [];

        // ── Streak badges ──────────────────────────────────────────────────
        $streak = $this->getConsistentLoggingStreak($user->id);

        if ($streak >= 1) {
            if ($b = $this->tryAwardBadge($user, $allBadges->get('first_log'))) {
                $newlyEarned[] = $b;
            }
        }
        if ($streak >= 7) {
            if ($b = $this->tryAwardBadge($user, $allBadges->get('week_streak'))) {
                $newlyEarned[] = $b;
            }
        }
        if ($streak >= 30) {
            if ($b = $this->tryAwardBadge($user, $allBadges->get('month_streak'))) {
                $newlyEarned[] = $b;
            }
        }

        // ── Savings badges (total under_budget events) ─────────────────────
        $underBudgetCount = UserPointLog::where('user_id', $user->id)
            ->where('event_type', 'under_budget')
            ->count();

        if ($underBudgetCount >= 3) {
            if ($b = $this->tryAwardBadge($user, $allBadges->get('budget_starter'))) {
                $newlyEarned[] = $b;
            }
        }
        if ($underBudgetCount >= 7) {
            if ($b = $this->tryAwardBadge($user, $allBadges->get('budget_keeper'))) {
                $newlyEarned[] = $b;
            }
        }
        if ($underBudgetCount >= 30) {
            if ($b = $this->tryAwardBadge($user, $allBadges->get('budget_master'))) {
                $newlyEarned[] = $b;
            }
        }

        // ── Usage badges ───────────────────────────────────────────────────
        $lowUsageCount = UserPointLog::where('user_id', $user->id)
            ->where('event_type', 'low_usage')
            ->count();

        $veryLowUsageCount = UserPointLog::where('user_id', $user->id)
            ->where('event_type', 'very_low_usage')
            ->count();

        if ($lowUsageCount >= 5) {
            if ($b = $this->tryAwardBadge($user, $allBadges->get('saver_5'))) {
                $newlyEarned[] = $b;
            }
        }
        if ($veryLowUsageCount >= 5) {
            if ($b = $this->tryAwardBadge($user, $allBadges->get('eco_5'))) {
                $newlyEarned[] = $b;
            }
        }
        if ($veryLowUsageCount >= 15) {
            if ($b = $this->tryAwardBadge($user, $allBadges->get('eco_15'))) {
                $newlyEarned[] = $b;
            }
        }

        // ── Milestone badges (total points) ────────────────────────────────
        $points = (int) $user->points;

        if ($points >= 100) {
            if ($b = $this->tryAwardBadge($user, $allBadges->get('pts_100'))) {
                $newlyEarned[] = $b;
            }
        }
        if ($points >= 300) {
            if ($b = $this->tryAwardBadge($user, $allBadges->get('pts_300'))) {
                $newlyEarned[] = $b;
            }
        }
        if ($points >= 700) {
            if ($b = $this->tryAwardBadge($user, $allBadges->get('pts_700'))) {
                $newlyEarned[] = $b;
            }
        }

        return $newlyEarned;
    }

    /**
     * Return the current consecutive-day streak for consistent_logging events.
     * Counts backwards from today: how many days in a row has the user logged?
     */
    public function getConsistentLoggingStreak(int $userId): int
    {
        // Fetch all distinct dates the user has a consistent_logging event, newest first
        $dates = UserPointLog::where('user_id', $userId)
            ->where('event_type', 'consistent_logging')
            ->select(DB::raw('DATE(log_date) as d'))
            ->distinct()
            ->orderByDesc('d')
            ->pluck('d')
            ->map(fn($d) => Carbon::parse($d)->startOfDay());

        if ($dates->isEmpty()) {
            return 0;
        }

        // Streak must include today OR yesterday (allow for the check running before today's award)
        $today     = Carbon::today()->startOfDay();
        $yesterday = Carbon::yesterday()->startOfDay();

        if (!$dates->first()->eq($today) && !$dates->first()->eq($yesterday)) {
            return 0;
        }

        $streak    = 1;
        $previous  = $dates->first();

        foreach ($dates->slice(1) as $date) {
            if ($previous->diffInDays($date) === 1) {
                $streak++;
                $previous = $date;
            } else {
                break;
            }
        }

        return $streak;
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    /**
     * Award a badge to a user. Idempotent — returns the Badge if newly awarded,
     * null if the user already has it (or if badge definition is missing).
     */
    private function tryAwardBadge(User $user, ?Badge $badge): ?Badge
    {
        if (!$badge) {
            return null;
        }

        $exists = UserBadge::where('user_id', $user->id)
            ->where('badge_id', $badge->id)
            ->exists();

        if ($exists) {
            return null;
        }

        UserBadge::create([
            'user_id'   => $user->id,
            'badge_id'  => $badge->id,
            'earned_at' => now(),
        ]);

        return $badge;
    }
}
