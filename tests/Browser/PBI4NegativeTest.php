<?php

namespace Tests\Browser\Theme;
 
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PBI4NegativeTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_zero_budget_should_show_validation_error(): void
    {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {

        $browser->loginAs($user)
                ->visit('/dashboard')
                ->click('#open-budget-modal')->pause(400)
                ->clear('#monthly_budget')
                ->type('#monthly_budget', '0')
                ->click('.btn-modal-save')
                ->pause(800)
                ->assertSee('Budget cannot be negative.');
        });
    }
}