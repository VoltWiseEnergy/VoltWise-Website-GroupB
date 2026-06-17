<?php

use Laravel\Dusk\Browser;
use App\Models\User;
use Tests\DuskTestCase;


uses(DuskTestCase::class);

test('TC.Distribution.001 - Add First Valid Device', function () {
    $this->browse(function (Browser $browser) {
        $user = User::factory()->create();

        $browser->loginAs($user)
            ->visit('/devices')
            ->clickLink('Add Device')
            ->type('name', 'Fridge')
            ->type('wattage', '1000')
            ->select('category', 'Kitchen')
            ->press('Add Device')
            ->visit('/analytics')
            ->assertSee('Category Distribution')
            ->assertSee('Fridge');
    });
});

test('TC.Distribution.002 - Add second Valid Device', function () {
    $this->browse(function (Browser $browser) {
        $user = User::factory()->create();

        $browser->loginAs($user)
            ->visit('/devices')
            ->clickLink('Add Device')
            ->type('name', 'Fridge')
            ->select('category', 'Kitchen')
            ->type('wattage', '1000')
            ->press('Add Device');

        $browser->visit('/devices')
            ->clickLink('Add Device')
            ->type('name', 'AC')
            ->type('wattage', '500')
            ->select('category', 'Cooling')
            ->press('Add Device')
            ->visit('/analytics')
            ->assertSee('Category Distribution')
            ->assertSee('AC'); 
    });
});

test('TC.Distribution.003 - Edit First Valid Device', function () {
    $this->browse(function (Browser $browser) {
        $user = User::factory()->create();

        $browser->loginAs($user)
            ->visit('/devices')
            ->clickLink('Add Device')
            ->type('name', 'Fridge')
            ->select('category', 'Kitchen')
            ->type('wattage', '1000')
            ->press('Add Device');

        $browser->visit('/devices')
            ->clickLink('Edit')
            ->pause(500)
            ->type('wattage', '500')
            ->press('Update Device')
            ->visit('/analytics')
            ->assertSee('Category Distribution')
            ->assertSee('Fridge');
    });
});

test('TC.Distribution.004 - Delete all Device', function () {
    $this->browse(function (Browser $browser) {
        $user = User::factory()->create();

        $browser->loginAs($user)
            ->visit('/devices')
            ->clickLink('Add Device')
            ->type('name', 'Fridge')
            ->select('category', 'Kitchen')
            ->type('wattage', '1000')
            ->press('Add Device');

        $browser->visit('/devices')
            ->waitForText('Delete')
            ->press('Delete')
            ->acceptDialog()
            ->visit('/analytics')
            ->assertSee('No category data. Add devices to see distribution.');
    });
});