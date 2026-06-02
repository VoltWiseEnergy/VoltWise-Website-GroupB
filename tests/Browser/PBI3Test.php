<?php

namespace Tests\Browser\Theme;
 
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PBI3Test extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_loaded_on_app_start(): void
    {
        $user = User::factory()->create();
        
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/dashboard')
                ->script("localStorage.setItem('theme', 'dark');");

            $browser->refresh()
                ->pause(400);

            $theme = $browser->script("return document.documentElement.getAttribute('data-theme');")[0];

            $this->assertEquals('dark', $theme);
        });
    }  
}