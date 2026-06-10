<?php

use App\Models\User;
use Laravel\Dusk\Browser;


// PBI #1 — ADD MASTER DEVICE


test('admin can add a new master device', function () {
    $admin = User::where('email', 'admin@voltwise.test')->first();

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
                ->visit('/admin/master-devices/create')
                ->type('name', 'Philips LED 10W')
                ->select('category', 'Lighting')
                ->type('wattage', '10')
                ->type('description', 'Energy-saving LED bulb')
                ->press('Add Device')
                ->waitForLocation('/admin/master-devices')
                ->assertPathIs('/admin/master-devices')
                ->assertSee('Philips LED 10W');
    });
});

test('admin can add device without description', function () {
    $admin = User::where('email', 'admin@voltwise.test')->first();

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
                ->visit('/admin/master-devices/create')
                ->type('name', 'Sharp AC 1.5PK')
                ->select('category', 'Cooling')
                ->type('wattage', '1350')
                ->press('Add Device')
                ->waitForLocation('/admin/master-devices')
                ->assertPathIs('/admin/master-devices')
                ->assertSee('Sharp AC 1.5PK');
    });
});

test('add fails when name is empty', function () {
    $admin = User::where('email', 'admin@voltwise.test')->first();

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
                ->visit('/admin/master-devices/create')
                ->select('category', 'Lighting')
                ->type('wattage', '10')
                ->press('Add Device')
                ->assertPathIs('/admin/master-devices/create');
    });
});

test('add fails when wattage is empty', function () {
    $admin = User::where('email', 'admin@voltwise.test')->first();

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
                ->visit('/admin/master-devices/create')
                ->type('name', 'No Wattage Device')
                ->select('category', 'Office')
                ->press('Add Device')
                ->assertPathIs('/admin/master-devices/create');
    });
});

test('cancel on add returns to index without saving', function () {
    $admin = User::where('email', 'admin@voltwise.test')->first();

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
                ->visit('/admin/master-devices/create')
                ->type('name', 'Abandoned Device')
                ->clickLink('Cancel')
                ->assertPathIs('/admin/master-devices')
                ->assertDontSee('Abandoned Device');
    });
});


// PBI #2 — VIEW MASTER DEVICE LIST


test('admin can view master device list', function () {
    $admin = User::where('email', 'admin@voltwise.test')->first();

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
                ->visit('/admin/master-devices')
                ->assertSee('Master Device Library')
                ->assertSee('Add Device');
    });
});

test('added device appears in the list', function () {
    $admin = User::where('email', 'admin@voltwise.test')->first();

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
                ->visit('/admin/master-devices/create')
                ->type('name', 'Samsung TV 43in')
                ->select('category', 'Entertainment')
                ->type('wattage', '120')
                ->press('Add Device')
                ->waitForLocation('/admin/master-devices')
                ->assertSee('Samsung TV 43in')
                ->assertSee('120')
                ->assertSee('Entertainment');
    });
});

test('guest cannot access master device list', function () {
    $this->browse(function (Browser $browser) {
        // logout 
        $browser->logout() 
                ->visit('/admin/master-devices')
                ->assertPathIs('/login');
    });
});


test('regular user cannot access master device list', function () {
    $user = User::factory()->create(['role' => 'user']);

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
                ->visit('/admin/master-devices')
                ->assertSee('403');
    });
});


// PBI #3 — UPDATE MASTER DEVICE


test('admin can update a master device', function () {
    $admin = User::where('email', 'admin@voltwise.test')->first();

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
                ->visit('/admin/master-devices/create')
                ->type('name', 'Old Fan')
                ->select('category', 'Cooling')
                ->type('wattage', '50')
                ->press('Add Device')
                ->waitForLocation('/admin/master-devices')
                ->clickLink('Edit')
                ->clear('name')
                ->type('name', 'Updated Fan 35W')
                ->clear('wattage')
                ->type('wattage', '35')
                ->press('Save Changes')
                ->waitForLocation('/admin/master-devices')
                ->assertSee('Updated Fan 35W')
                ->assertDontSee('Old Fan');
    });
});

test('edit form shows prefilled values', function () {
    $admin = User::where('email', 'admin@voltwise.test')->first();

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
                ->visit('/admin/master-devices/create')
                ->type('name', 'Prefill Test Device')
                ->select('category', 'Kitchen')
                ->type('wattage', '800')
                ->press('Add Device')
                ->waitForLocation('/admin/master-devices')
                ->clickLink('Edit')
                ->assertInputValue('name', 'Prefill Test Device')
                ->assertSelected('category', 'Kitchen')
                ->assertInputValue('wattage', '800.00');
    });
});

test('update fails when name is cleared', function () {
    $admin = User::where('email', 'admin@voltwise.test')->first();

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
                ->visit('/admin/master-devices/create')
                ->type('name', 'Device Before Clear')
                ->select('category', 'Laundry')
                ->type('wattage', '500')
                ->press('Add Device')
                ->waitForLocation('/admin/master-devices')
                ->clickLink('Edit')
                ->clear('name')
                ->press('Save Changes')
                ->assertPathContains('/edit');
    });
});

test('cancel on edit returns to index without changes', function () {
    $admin = User::where('email', 'admin@voltwise.test')->first();

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
                ->visit('/admin/master-devices/create')
                ->type('name', 'Untouched Device')
                ->select('category', 'Office')
                ->type('wattage', '45')
                ->press('Add Device')
                ->waitForLocation('/admin/master-devices')
                ->clickLink('Edit')
                ->clear('name')
                ->type('name', 'Changed Name')
                ->clickLink('Cancel')
                ->assertPathIs('/admin/master-devices')
                ->assertSee('Untouched Device')
                ->assertDontSee('Changed Name');
    });
});