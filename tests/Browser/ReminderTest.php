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

    public function test_delete_reminder()
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();

             $browser->loginAs($user)
                ->visit('/devices/create')
                ->type('name', 'Test Device 5')
                ->type('wattage', '1000')
                ->select('category', 'Other')
                ->press('Add Device')
                ->press('manage-reminder-btn')
                ->select('device_id', $deviceId = $browser->element('select[name="device_id"] option:nth-child(2)')->getAttribute('value'))
                ->type('reminder_time', '2024-12-31T23:59')
                ->type('reminder_message', 'This is a test reminder.')
                ->press('save-reminder-btn')
                ->press('manage-reminder-btn')
                ->press('delete-reminder') 
                ->assertDialogOpened('Delete this reminder?') 
                ->acceptDialog();
        });
    }

    public function test_alert_reminder()
    {
    $this->browse(function (Browser $browser) {
        $user = User::factory()->create();

        $browser->loginAs($user)
            ->visit('/devices/create')
            ->type('name', 'Test Device 6')
            ->type('wattage', '1000')
            ->select('category', 'Other')
            ->press('Add Device')
            ->press('manage-reminder-btn')
            ->select('device_id', $deviceId = $browser->element('select[name="device_id"] option:nth-child(2)')->getAttribute('value'))
            ->type('reminder_time', date('H:i'))
            ->type('reminder_message', 'This is a test reminder.')
            ->press('save-reminder-btn');

        $browser->script("checkAndNotifyReminders();");

        $browser->waitForText('This is a test reminder.', 5)
                ->assertSee('This is a test reminder.');
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
            $message = $browser->script(
                "return document.querySelector('select[name=\"device_id\"]').validationMessage;"
            );

            $this->assertStringContainsString('Please select', $message[0]);
        });
    }
    
    public function test_negative_edit()
    {
    $this->browse(function (Browser $browser) {
        $user = User::factory()->create();

        $browser->loginAs($user)
            ->visit('/devices/create')
            ->type('name', 'Test Device 4')
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
            ->type('reminder_time', '')   // leave empty
            ->type('reminder_message', '') // leave empty
            ->press('save-reminder-edit');

        // Grab the browser’s native validation message for the required field
        $message = $browser->script(
            "return document.querySelector('input[name=\"reminder_time\"]').validationMessage;"
        );

        $this->assertStringContainsString('Please fill out', $message[0]);
    });
    }
    
}
