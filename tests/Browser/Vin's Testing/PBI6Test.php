<?php

namespace Tests\Browser\Theme;
 
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PBI6Test extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_visual_progress_bar_visible_when_budget_is_set(): void
    {
    $user = User::factory()->create(['monthly_budget' => 1000000]);

    $this->browse(function (Browser $browser) use ($user) {

        $browser->loginAs($user)
                ->visit('/dashboard')
                ->assertPresent('#budget-fill-bar')
                ->assertPresent('#budget-pct-badge');
        $width = $browser->script("return document.getElementById('budget-fill-bar').style.width;")[0];
        $this->assertStringContainsString('0', $width, 'With zero actual usage the fill bar width must be 0%.');
        $browser->assertSeeIn('#budget-pct-badge', '0%');
        });
    }
}