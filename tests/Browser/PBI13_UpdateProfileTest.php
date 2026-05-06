<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PBI13_UpdateProfileTest extends DuskTestCase
{
    public function test_user_can_update_profile()
    {
        $user = User::first();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/profile')
                    ->assertPathIs('/profile')
                    ->type('name', 'Alan Updated')
                    ->type('email', 'alanupdated@example.com')
                    ->press('Save')
                    ->assertSee('Alan Updated')
                    ->screenshot('PBI13-update-profile');
        });
    }
}