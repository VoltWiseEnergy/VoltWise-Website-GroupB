<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Device;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class DeleteDeviceTest extends DuskTestCase
{
    /**
     * TC.VW.004.001
     * Positive Test:
     * User successfully deletes a device
     */
    public function test_user_can_delete_device()
    {
        $user = User::factory()->create();

        $device = Device::create([
            'user_id' => $user->id,
            'name' => 'Television',
            'wattage' => 200,
            'category' => 'Entertainment'
        ]);

        $this->browse(function (Browser $browser) use ($user, $device) {
            $browser->loginAs($user)
                ->visit('/devices')

                ->assertSee('Television')

                ->press('Delete')
                ->acceptDialog()

                ->pause(1000)

                ->assertPathIs('/devices')
                ->assertDontSee('Television');
        });
    }

    /**
     * TC.VW.004.002
     * Negative Test:
     * User cancels deletion
     */
    public function test_user_can_cancel_device_deletion()
    {
        $user = User::factory()->create();

        $device = Device::create([
            'user_id' => $user->id,
            'name' => 'Laptop',
            'wattage' => 150,
            'category' => 'Entertainment'
        ]);

        $this->browse(function (Browser $browser) use ($user, $device) {
            $browser->loginAs($user)
                ->visit('/devices')

                ->assertSee('Laptop')

                ->press('Delete')
                ->dismissDialog()

                ->pause(1000)

                ->assertPathIs('/devices')
                ->assertSee('Laptop');
        });
    }
}