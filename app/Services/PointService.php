<?php

namespace App\Services;

use App\Models\User;
use App\Models\UsageLog;
use App\Models\UserPointLog;
use Illuminate\Support\Carbon;

class PointService
{
    /**
     * Point values per event type.
     */
    const POINTS = [
        'consistent_logging' => 5,
        'under_budget'       => 50,
        'low_usage'          => 10,
        'very_low_usage'     => 20,
    ];

    /**
     * Award daily points to the user (idempotent — safe to call multiple times per day).
     * Returns an array of newly awarded events (empty if nothing new was awarded today).
     */
    public function awardDailyPoints(User $user): array
    {
        $today    = Carbon::today()->toDateString();
        $awarded  = [];

        // ── 1. Consistent Logging: logged at least 1 device today ────────────
        $hasLogToday = UsageLog::where('user_id', $user->id)
            ->where('usage_date', $today)
            ->exists();

        if ($hasLogToday) {
            if ($this->tryAward($user, 'consistent_logging', $today)) {
                $awarded['consistent_logging'] = self::POINTS['consistent_logging'];
            }
        }

        // ── 2. Under Budget: monthly cost < monthly budget ────────────────────
        if ($user->monthly_budget && $user->monthly_budget > 0) {
            $monthlyCost = $user->devices()->sum('monthly_cost') ?? 0;
            if ($monthlyCost < $user->monthly_budget) {
                if ($this->tryAward($user, 'under_budget', $today)) {
                    $awarded['under_budget'] = self::POINTS['under_budget'];
                }
            }
        }

        // ── 3. Low Usage / Very Low Usage: compare today vs 7-day avg ────────
        $todayKwh = $this->getTodayKwh($user->id, $today);

        if ($todayKwh > 0) {
            $avgKwh = $this->getSevenDayAvgKwh($user->id, $today);

            if ($avgKwh > 0) {
                if ($todayKwh < ($avgKwh * 0.5)) {
                    // Very low usage (< 50% of average) — give the bigger bonus only
                    if ($this->tryAward($user, 'very_low_usage', $today)) {
                        $awarded['very_low_usage'] = self::POINTS['very_low_usage'];
                    }
                } elseif ($todayKwh < $avgKwh) {
                    // Low usage (< average)
                    if ($this->tryAward($user, 'low_usage', $today)) {
                        $awarded['low_usage'] = self::POINTS['low_usage'];
                    }
                }
            }
        }

        return $awarded;
    }

    /**
     * Get the list of events awarded today for display purposes.
     */
    public function getTodayAwards(User $user): array
    {
        $today = Carbon::today()->toDateString();

        return UserPointLog::where('user_id', $user->id)
            ->where('log_date', $today)
            ->get()
            ->mapWithKeys(fn($log) => [$log->event_type => $log->points])
            ->toArray();
    }

    /**
     * Get paginated point log history for the user.
     */
    public function getHistory(User $user, int $days = 30)
    {
        return UserPointLog::where('user_id', $user->id)
            ->where('log_date', '>=', Carbon::today()->subDays($days - 1))
            ->orderBy('log_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * Determine the level name and progress based on total points.
     */
    public function getLevel(int $points): array
    {
        $levels = [
            ['name' => 'Bronze',   'min' => 0,   'max' => 99,  'emoji' => '🥉', 'color' => '#cd7f32'],
            ['name' => 'Silver',   'min' => 100, 'max' => 299, 'emoji' => '🥈', 'color' => '#94a3b8'],
            ['name' => 'Gold',     'min' => 300, 'max' => 699, 'emoji' => '🥇', 'color' => '#f59e0b'],
            ['name' => 'Platinum', 'min' => 700, 'max' => null,'emoji' => '💎', 'color' => '#8b5cf6'],
        ];

        foreach ($levels as $i => $level) {
            if ($level['max'] === null || $points <= $level['max']) {
                $nextLevel  = $levels[$i + 1] ?? null;
                $rangeSize  = $level['max'] !== null ? ($level['max'] - $level['min'] + 1) : 1;
                $progress   = $level['max'] !== null
                    ? min(round((($points - $level['min']) / $rangeSize) * 100), 100)
                    : 100;

                return [
                    'current'   => $level,
                    'next'      => $nextLevel,
                    'progress'  => $progress,
                    'points_to_next' => $nextLevel ? max(0, $nextLevel['min'] - $points) : 0,
                ];
            }
        }

        return [
            'current'  => end($levels),
            'next'     => null,
            'progress' => 100,
            'points_to_next' => 0,
        ];
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Try to insert a point log entry. Returns true if newly inserted (award given),
     * false if it already existed (duplicate, no double-award).
     */
    private function tryAward(User $user, string $eventType, string $date): bool
    {
        $exists = UserPointLog::where('user_id', $user->id)
            ->where('event_type', $eventType)
            ->where('log_date', $date)
            ->exists();

        if ($exists) {
            return false;
        }

        UserPointLog::create([
            'user_id'    => $user->id,
            'event_type' => $eventType,
            'points'     => self::POINTS[$eventType],
            'log_date'   => $date,
        ]);

        // Increment user's running total
        $user->increment('points', self::POINTS[$eventType]);

        return true;
    }

    private function getTodayKwh(int $userId, string $today): float
    {
        $logs = UsageLog::where('user_id', $userId)
            ->where('usage_date', $today)
            ->get();

        return $logs->sum(fn($log) => ($log->wattage * $log->hours) / 1000);
    }

    private function getSevenDayAvgKwh(int $userId, string $today): float
    {
        $start = Carbon::parse($today)->subDays(7)->toDateString();

        $logs = UsageLog::where('user_id', $userId)
            ->whereBetween('usage_date', [$start, $today])
            ->get()
            ->groupBy('usage_date');

        if ($logs->isEmpty()) {
            return 0;
        }

        $dailyTotals = $logs->map(fn($dayLogs) =>
            $dayLogs->sum(fn($log) => ($log->wattage * $log->hours) / 1000)
        );

        return $dailyTotals->avg() ?? 0;
    }
}
