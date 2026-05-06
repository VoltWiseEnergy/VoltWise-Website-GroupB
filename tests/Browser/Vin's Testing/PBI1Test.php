<?php

namespace Tests\Browser\Theme;
 
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PBI1Test extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_toggle_dark_mode(): void
    {
        $user = User::factory()->create();
        
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/dashboard')
                ->assertSee('Dashboard')
                ->click('#theme-toggle')
                ->pause(400);
        });
    }
}
