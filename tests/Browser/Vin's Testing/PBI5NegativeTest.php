<?php

namespace Tests\Browser\Theme;
 
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PBI5NegativeTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_budget_comparison_visible_without_budget_set(): void
    {
    $user = User::factory()->create(['monthly_budget' => null]);

    $this->browse(function (Browser $browser) use ($user) {

        $browser->loginAs($user)
                ->visit('/dashboard')
                ->assertMissing('.budget-used');
        });
    }
}