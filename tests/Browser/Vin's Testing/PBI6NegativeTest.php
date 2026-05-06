<?php

namespace Tests\Browser\Theme;
 
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PBI6NegativeTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_fill_bar_has_danger_class_at_zero_usage(): void
    {
    $user = User::factory()->create(['monthly_budget' => 1000000]);

    $this->browse(function (Browser $browser) use ($user) {

        $browser->loginAs($user)
                ->visit('/dashboard')
                ->assertPresent('#budget-fill-bar');
        $classes = $browser->attribute('#budget-fill-bar', 'class');
        $this->assertStringContainsString('danger', (string)$classes);
        });
    }
}