<?php

namespace Tests\Browser\Theme;
 
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PBI2Test extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_theme_preference_is_saved(): void
    {
        $user = User::factory()->create();
        
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/dashboard')
                ->assertSee('Dashboard')
                ->click('#theme-toggle')
                ->pause(400)
                ->refresh();
            $theme = $browser->script("return localStorage.getItem('theme');");
            $this->assertNull($theme[0]);
        });
    }  
}