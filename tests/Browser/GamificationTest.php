<?php

namespace Tests\Browser;

use App\Models\Badge;
use App\Models\User;
use App\Models\UserBadge;
use App\Services\BadgeService;
use App\Services\PointService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class GamificationTest extends DuskTestCase
{
    private array $testUserIds = [];

    protected function tearDown(): void
    {
        if (!empty($this->testUserIds)) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('user_badges')->whereIn('user_id', $this->testUserIds)->delete();
            DB::table('user_point_logs')->whereIn('user_id', $this->testUserIds)->delete();
            DB::table('usage_logs')->whereIn('user_id', $this->testUserIds)->delete();
            DB::table('devices')->whereIn('user_id', $this->testUserIds)->delete();
            DB::table('users')->whereIn('id', $this->testUserIds)->delete();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        DB::table('badges')->where('key', 'first_log')->delete();
        $this->testUserIds = [];

        parent::tearDown();
    }

    // =========================================================
    // HELPERS
    // =========================================================

    private function makeUser(float $budget = 500000, int $points = 0): User
    {
        $user = User::factory()->create([
            'monthly_budget' => $budget,
            'points'         => $points,
        ]);
        $this->testUserIds[] = $user->id;
        return $user;
    }

    private function makeTrackedUser(array $attributes = []): User
    {
        $user = User::factory()->create($attributes);
        $this->testUserIds[] = $user->id;
        return $user;
    }

    private function makeDevice(User $user, float $monthlyCost = 50000): int
    {
        return DB::table('devices')->insertGetId([
            'user_id'              => $user->id,
            'name'                 => 'Kipas Angin Test',
            'category'             => 'Electronics',
            'wattage'              => 100,
            'usage_hours_per_day'  => 8,
            'usage_days_per_month' => 30,
            'monthly_cost'         => $monthlyCost,
            'created_at'           => now(),
            'updated_at'           => now(),
        ]);
    }

    private function makeUsageLog(User $user, int $deviceId, string $date, int $wattage = 100, float $hours = 2.0): void
    {
        DB::table('usage_logs')->insert([
            'user_id'     => $user->id,
            'device_id'   => (string) $deviceId,
            'device_name' => 'Kipas Angin Test',
            'wattage'     => $wattage,
            'hours'       => $hours,
            'usage_date'  => $date,
            'is_override' => 0,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    private function makeBadge(string $key, string $category = 'streak'): Badge
    {
        return Badge::firstOrCreate(
            ['key' => $key],
            [
                'name'        => ucfirst(str_replace('_', ' ', $key)),
                'description' => "Badge untuk $key",
                'emoji'       => '🏅',
                'category'    => $category,
                'color'       => '#10b981',
            ]
        );
    }

    private function makePointLog(User $user, string $eventType, int $points, string $date): void
    {
        DB::table('user_point_logs')->insert([
            'user_id'    => $user->id,
            'event_type' => $eventType,
            'points'     => $points,
            'log_date'   => $date,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $user->increment('points', $points);
    }

    // =========================================================
    // PBI-39 : EARN POINTS
    // =========================================================

    // TC-39-01 [POSITIVE]
    public function testTC3901EarnPointsConsistentLoggingAndUnderBudget()
    {
        $user     = $this->makeUser(budget: 500000);
        $deviceId = $this->makeDevice($user, monthlyCost: 50000);
        $today    = Carbon::today()->toDateString();

        $this->makeUsageLog($user, $deviceId, $today);

        $pointService = app(PointService::class);
        $pointService->awardDailyPoints($user);
        $user->refresh();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/points')
                    ->pause(1000)
                    ->assertSee('My Points')
                    ->assertSee((string) $user->points)
                    ->assertSee('Logged Usage')
                    ->assertSee('Stayed Under Budget');
        });
    }

    // TC-39-02 [NEGATIVE]
    public function testTC3902PointsNotAwardedTwiceForSameEventSameDay()
    {
        $user     = $this->makeUser(budget: 500000, points: 55);
        $deviceId = $this->makeDevice($user, monthlyCost: 50000);
        $today    = Carbon::today()->toDateString();

        $this->makePointLog($user, 'consistent_logging', 5, $today);
        $this->makePointLog($user, 'under_budget', 50, $today);
        $this->makeUsageLog($user, $deviceId, $today);

        $user->refresh();
        $pointsBefore = $user->points;

        $pointService = app(PointService::class);
        $awarded = $pointService->awardDailyPoints($user);
        $user->refresh();

        $this->assertEmpty($awarded);
        $this->assertEquals($pointsBefore, $user->points);

        $this->browse(function (Browser $browser) use ($user, $pointsBefore) {
            $browser->loginAs($user)
                    ->visit('/points')
                    ->pause(1000)
                    ->assertSee('My Points')
                    ->assertSee((string) $pointsBefore);
        });
    }

    // =========================================================
    // PBI-40 : UNLOCK BADGES / ACHIEVEMENTS
    // =========================================================

    // TC-40-01 [POSITIVE]
    public function testTC4001BadgeFirstLogAwardedOnFirstUsageLog()
    {
        $user     = $this->makeUser(budget: 500000);
        $deviceId = $this->makeDevice($user, monthlyCost: 50000);
        $badge    = $this->makeBadge('first_log', 'streak');
        $today    = Carbon::today()->toDateString();

        $this->makeUsageLog($user, $deviceId, $today);

        $pointService = app(PointService::class);
        $pointService->awardDailyPoints($user);
        $user->refresh();

        $this->assertDatabaseHas('user_badges', [
            'user_id'  => $user->id,
            'badge_id' => $badge->id,
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/points')
                    ->pause(1000)
                    ->assertSee('My Points')
                    ->assertSee('My Badges')
                    ->assertSee('First log');
        });
    }

    // TC-40-02 [NEGATIVE]
    public function testTC4002BadgeNotAwardedTwiceWhenAlreadyOwned()
    {
        $user  = $this->makeUser(budget: 500000);
        $badge = $this->makeBadge('first_log', 'streak');

        UserBadge::create([
            'user_id'   => $user->id,
            'badge_id'  => $badge->id,
            'earned_at' => now()->subDay(),
        ]);

        $today = Carbon::today()->toDateString();
        $this->makePointLog($user, 'consistent_logging', 5, $today);

        $badgeService = app(BadgeService::class);
        $newlyEarned  = $badgeService->checkAndAwardBadges($user);

        $newKeys = array_map(fn($b) => $b->key, $newlyEarned);
        $this->assertNotContains('first_log', $newKeys);

        $count = UserBadge::where('user_id', $user->id)
            ->where('badge_id', $badge->id)
            ->count();
        $this->assertEquals(1, $count);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/points')
                    ->pause(1000)
                    ->assertSee('My Badges');

            $badgeCount = substr_count($browser->text('.badges-grid'), 'First log');
            $this->assertEquals(1, $badgeCount);
        });
    }

    // =========================================================
    // PBI-41 : DISPLAY LEADERBOARD
    // =========================================================

    // TC-41-01 [POSITIVE]
    public function testTC4101LeaderboardShowsUsersOrderedByPointsAndMarksCurrentUser()
    {
        $userA = $this->makeTrackedUser(['name' => 'AlphaUser', 'points' => 600]);
        $userB = $this->makeTrackedUser(['name' => 'BetaUser',  'points' => 100]);
        $userC = $this->makeTrackedUser(['name' => 'GammaUser', 'points' => 0]);

        $this->browse(function (Browser $browser) use ($userA, $userB, $userC) {
            $browser->loginAs($userB)
                    ->visit('/leaderboard')
                    ->pause(1000)
                    ->assertSee('Leaderboard')
                    ->assertSee('AlphaUser')
                    ->assertSee('BetaUser')
                    ->assertSee('GammaUser')
                    ->assertSee('You')
                    ->assertSee('Gold')
                    ->assertSee('Silver')
                    ->assertSee('600')
                    ->assertSee('100');

            $html     = $browser->driver->getPageSource();
            $posAlpha = strpos($html, 'AlphaUser');
            $posGamma = strpos($html, 'GammaUser');

            $this->assertLessThan($posGamma, $posAlpha);
        });
    }

    // TC-41-02 [NEGATIVE]
    public function testTC4102UnauthenticatedUserRedirectedFromLeaderboard()
    {
        $this->browse(function (Browser $browser) {
            $browser->logout()
                    ->visit('/leaderboard')
                    ->pause(1000)
                    ->assertPathIs('/login')
                    ->assertSee('Welcome back')
                    ->assertSee('Sign In');
        });
    }
}
