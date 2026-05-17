<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Device;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ViewDeviceTest extends DuskTestCase
{
    /**
     * TC.VW.002.001
     * Positive Test - View existing devices
     */
    public function test_user_can_view_saved_devices()
    {
        $user = User::factory()->create();

        Device::create([
            'user_id' => $user->id,
            'name' => 'Television',
            'wattage' => 150,
            'category' => 'Entertainment'
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)

                ->visit('/devices')

                ->assertSee('My Devices')
                ->assertSee('Television')
                ->assertSee('150 W')
                ->assertSee('Entertainment')
                ->assertSee('Edit')
                ->assertSee('Delete');
        });
    }

    /**
     * TC.VW.002.002
     * Negative Test - Empty device list
     */
    public function test_user_sees_empty_state_when_no_devices_exist()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)

                ->visit('/devices')

                ->assertSee('My Devices')
                ->assertSee('No Devices Yet')
                ->assertSee('Add your first device to start tracking energy usage.');
        });
    }
}