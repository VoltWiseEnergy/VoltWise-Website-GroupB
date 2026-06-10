<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PBI15_ChangePasswordTest extends DuskTestCase
{
    public function test_user_can_change_password()
    {
        $user = User::first();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/profile')
                    ->assertPathIs('/profile')
                    ->type('current_password', 'password123')
                    ->type('password', 'newpassword123')
                    ->type('password_confirmation', 'newpassword123')
                    ->press('Save Changes')
                    ->assertSee('Profile updated successfully!')
                    ->screenshot('PBI15-change-password');
        });
    }
}