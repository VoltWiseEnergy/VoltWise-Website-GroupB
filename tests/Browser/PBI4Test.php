<?php

namespace Tests\Browser\Theme;
 
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PBI4Test extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_user_can_set_monthly_usage_limit(): void
    {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {

        $browser->loginAs($user)
                ->visit('/dashboard')
                ->assertPresent('#budget-tracker-card')
                ->assertSeeIn('#open-budget-modal', 'Set Budget')
                ->click('#open-budget-modal', 'Set Budget')
                ->assertVisible('#budget-modal-overlay')
                ->clear('#monthly_budget')
                ->type('#monthly_budget', '500000')
                ->click('.btn-modal-save')->waitForLocation('/dashboard')
                ->assertSee('Rp 500.000');
        });
    }
}