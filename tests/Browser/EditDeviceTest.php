<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Device;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class EditDeviceTest extends DuskTestCase
{
    /**
     * TC.VW.003.001
     * Positive Test:
     * User successfully edits existing device
     */
    public function test_user_can_edit_device_with_valid_data()
    {
        $user = User::factory()->create();

        $device = Device::create([
            'user_id' => $user->id,
            'name' => 'Fan',
            'wattage' => 75,
            'category' => 'Cooling'
        ]);

        $this->browse(function (Browser $browser) use ($user, $device) {
            $browser->loginAs($user)
                ->visit("/devices/{$device->id}/edit")

                ->assertSee('Edit Device')

                ->clear('name')
                ->type('name', 'Air Conditioner')

                ->clear('wattage')
                ->type('wattage', '900')

                ->select('category', 'Cooling')

                ->press('Update Device')

                ->assertPathIs('/devices')
                ->assertSee('Device updated successfully')
                ->assertSee('Air Conditioner');
        });
    }

    /**
     * TC.VW.003.002
     * Negative Test:
     * User tries editing device with empty fields
     */
    public function test_user_cannot_edit_device_with_empty_fields()
    {
        $user = User::factory()->create();

        $device = Device::create([
            'user_id' => $user->id,
            'name' => 'TV',
            'wattage' => 200,
            'category' => 'Entertainment'
        ]);

        $this->browse(function (Browser $browser) use ($user, $device) {
            $browser->loginAs($user)
                ->visit("/devices/{$device->id}/edit")

                ->assertSee('Edit Device')

                ->clear('name')
                ->clear('wattage')
                ->select('category', '')

                ->script("
                    document.querySelector('input[name=name]').removeAttribute('required');
                    document.querySelector('input[name=wattage]').removeAttribute('required');
                    document.querySelector('select[name=category]').removeAttribute('required');
                ");

            $browser
                ->press('Update Device')

                ->assertPathIs("/devices/{$device->id}/edit")
                ->assertSee('The name field is required')
                ->assertSee('The wattage field is required')
                ->assertSee('The category field is required');
        });
    }
}