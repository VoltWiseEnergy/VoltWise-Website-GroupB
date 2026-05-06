<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Device;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CreateDeviceTest extends DuskTestCase
{
    /**
     * TC.VW.001.001
     * Positive Test
     */
    public function test_user_can_create_device_with_valid_data()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)

                ->visit('/devices/create')
                ->assertSee('Add New Device')

                ->type('name', 'Air Conditioner')
                ->type('wattage', '900')
                ->select('category', 'Cooling')

                ->press('Add Device')

                ->assertPathIs('/devices')
                ->assertSee('Device added successfully.')
                ->assertSee('Air Conditioner');
        });
    }

    /**
     * TC.VW.001.002
     * Negative Test
     */
    public function test_user_cannot_create_device_with_empty_fields()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)

                ->visit('/devices/create')
                ->press('Add Device')

                ->assertPathIs('/devices/create')
                ->assertSee('The name field is required')
                ->assertSee('The wattage field is required')
                ->assertSee('The category field is required');
        });
    }
}