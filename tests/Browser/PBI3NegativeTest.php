<?php

namespace Tests\Browser\Theme;
 
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PBI3NegativeTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_negative_theme_preference_is_saved(): void
    {
        $user = User::factory()->create();
        
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/dashboard');
                
            $browser->script("localStorage.removeItem('theme');");
                
            $browser->refresh()
                ->pause(300)
                ->assertMissing('body[data-theme="dark"]');
        });
    }  
}