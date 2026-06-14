<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Device;

class UsageTest extends DuskTestCase
{
    public function test_open_tracker_page()
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();

            $browser->loginAs($user)
                ->visit('/usage/tracker')
                ->pause(1000)
                ->assertSee('Daily Tracker');
        });
    }

    public function test_open_tracker_invalid()
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();

            $browser->loginAs($user)
                ->visit('/usage/invalid')
                ->assertDontSee('Daily Tracker');
        });
    }

    public function test_tracker_has_content()
    {
        $this->browse(function (Browser $browser) {

            $user = User::factory()->create();

            Device::create([
                'user_id' => $user->id,
                'name' => 'Air con',
                'category' => 'Kitchen',
                'wattage' => 900
            ]);

            $browser->loginAs($user)
                ->visit('/usage/tracker')
                ->pause(1000)
                ->assertSee('Air con');
        });
    }

    public function test_tracker_no_random_text()
    {
        $this->browse(function (Browser $browser) {

            $user = User::factory()->create();

            Device::create([
                'user_id' => $user->id,
                'name' => 'Air con',
                'category' => 'Kitchen',
                'wattage' => 900
            ]);

            $browser->loginAs($user)
                ->visit('/usage/tracker')
                ->assertDontSee('XYZ123ABC');
        });
    }

    public function test_tracker_has_button()
    {
        $this->browse(function (Browser $browser) {

            $user = User::factory()->create();

            Device::create([
                'user_id' => $user->id,
                'name' => 'Air con',
                'category' => 'Kitchen',
                'wattage' => 900
            ]);

            $browser->loginAs($user)
                ->visit('/usage/tracker')
                ->pause(1000)
                ->assertSee('Save');
        });
    }

    public function test_click_save_button()
    {
        $this->browse(function (Browser $browser) {

            $user = User::factory()->create();

            Device::create([
                'user_id' => $user->id,
                'name' => 'Air con',
                'category' => 'Kitchen',
                'wattage' => 900
            ]);

            $browser->loginAs($user)
                ->visit('/usage/tracker')
                ->pause(1000)
                ->press('Save')
                ->pause(1000)
                ->assertPathIs('/usage/tracker');
        });
    }
}