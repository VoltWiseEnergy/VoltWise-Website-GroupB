<?php

namespace Tests\Browser\Theme;
 
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PBI5Test extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_dashboard_shows_used_vs_budget_when_budget_is_set(): void
    {
    $user = User::factory()->create(['monthly_budget' => 750000]);

    $this->browse(function (Browser $browser) use ($user) {

        $browser->loginAs($user)
                ->visit('/dashboard')
                ->assertPresent('#budget-tracker-card')
                ->assertSeeIn('.budget-used', 'Rp 0')
                ->assertSeeIn('.budget-total', 'Rp 750.000')
                ->assertPresent('#budget-pct-badge')
                ->assertSeeIn('#budget-pct-badge', '0%');
        });
    }
}