<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class ReminderTest extends DuskTestCase
{
    /** @test */
   public function test_add_reminder()
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();

            $browser->loginAs($user)
                ->visit('/devices/create')
                ->type('name', 'Test Device')
                ->type('wattage', '1000')
                ->select('category', 'Other')
                ->press('Add Device')
                ->press('manage-reminder-btn')
                ->select('device_id', $deviceId = $browser->element('select[name="device_id"] option:nth-child(2)')->getAttribute('value'))
                ->type('reminder_time', '2024-12-31T23:59')
                ->type('reminder_message', 'This is a test reminder.')
                ->press('save-reminder-btn');
        });
    }

    public function test_edit_reminder()
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();

             $browser->loginAs($user)
                ->visit('/devices/create')
                ->type('name', 'Test Device 2')
                ->type('wattage', '1000')
                ->select('category', 'Other')
                ->press('Add Device')
                ->press('manage-reminder-btn')
                ->select('device_id', $deviceId = $browser->element('select[name="device_id"] option:nth-child(2)')->getAttribute('value'))
                ->type('reminder_time', '2024-12-31T23:59')
                ->type('reminder_message', 'This is a test reminder.')
                ->press('save-reminder-btn')
                ->press('manage-reminder-btn')
                ->clickLink('Edit')
                ->type('reminder_time', '2025-01-01T12:00')
                ->type('reminder_message', 'This is an edited test reminder.')
                ->press('save-reminder-edit');
        });
    }

    public function test_negative_add()
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();

            $browser->loginAs($user)
                ->visit('/devices/create')
                ->type('name', 'Test Device 3')
                ->type('wattage', '1000')
                ->select('category', 'Other')
                ->press('Add Device')
                ->press('manage-reminder-btn')
                ->press('save-reminder-btn');

            // Grab the browser's native validation message
            $message = $browser->script(
                "return document.querySelector('select[name=\"device_id\"]').validationMessage;"
            );

            // Assert the message contains the expected text
            $this->assertStringContainsString('Please select', $message[0]);
        });
    }
}
