<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PBI12_ViewProfileTest extends DuskTestCase
{
    public function test_user_can_view_profile_page()
    {
        $user = User::first();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/profile')
                    ->assertPathIs('/profile')
                    ->assertSee($user->name)
                    ->assertSee($user->email)
                    ->screenshot('PBI12-view-profile');
        });
    }
}