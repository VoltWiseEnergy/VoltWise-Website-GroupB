<?php

namespace Tests\Browser;

use App\Models\Device;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * PBI #7 – Consumption
 *
 * TC.Consumption.001  Empty Device Name (Negative) – name field left empty
 * TC.Consumption.002  Invalid Wattage   (Negative) – non-numeric watt value
 * TC.Consumption.003  Zero Wattage      (Negative) – wattage = 0 (min:1 rule)
 * TC.Consumption.004  Empty State       (Negative) – dashboard with zero devices
 */
class ConsumptionTest extends DuskTestCase
{
    use DatabaseMigrations;

    // ────────────────────────────────────────────────────────────
    // Helper
    // ────────────────────────────────────────────────────────────
    private function loginAs(Browser $browser, User $user): void
    {
        $browser->driver->manage()->deleteAllCookies();
        $browser->visit('/login')
                ->waitFor('#email')
                ->type('#email', $user->email)
                ->type('#password', 'password')
                ->press('#btn-login')
                ->waitForLocation('/dashboard', 10);
    }

    // ────────────────────────────────────────────────────────────
    // TC.Consumption.001 – Empty Device Name (Negative)
    //
    // Pre-condition : User is logged in. On /devices/create.
    // Expected      : Submitting with the name field blank shows a
    //                 validation error; device is NOT created.
    //
    // Note: create.blade.php has no HTML5 'required' on the name
    //       input, so the form will POST without JS manipulation.
    //       The server-side rule 'required' will reject it.
    // ────────────────────────────────────────────────────────────
    public function test_TC_Consumption_001_empty_device_name_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $this->loginAs($browser, $user);

            $browser->visit('/devices/create')
                    // Leave 'name' blank intentionally
                    ->type('input[name="wattage"]', '900')
                    ->select('select[name="category"]', 'Cooling')
                    ->click('button.btn-primary');

            // DeviceController validation: 'name' => 'required|max:255'
            // Error rendered in create.blade.php inside .error-box
            $browser->assertPathIs('/devices/create')
                    ->assertSee('The name field is required.');
        });
    }

    // ────────────────────────────────────────────────────────────
    // TC.Consumption.002 – Non-Numeric Wattage (Negative)
    //
    // Pre-condition : User is logged in. On /devices/create.
    // Expected      : A non-numeric string in the watt field fails
    //                 server-side integer validation.
    //
    // Note: The wattage input is type="number"; we change it to
    //       type="text" via JS so the browser won't block the value.
    // ────────────────────────────────────────────────────────────
    public function test_TC_Consumption_002_non_numeric_wattage_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $this->loginAs($browser, $user);

            $browser->visit('/devices/create')
                    ->type('input[name="name"]', 'Blender');

            // Change input type and inject non-numeric value via JS
            $browser->script(
                "var w = document.querySelector('input[name=\"wattage\"]');
                 w.type  = 'text';
                 w.value = 'abc';"
            );

            $browser->select('select[name="category"]', 'Kitchen')
                    ->click('button.btn-primary');

            // DeviceController validation: 'wattage' => 'required|integer|min:1'
            $browser->assertPathIs('/devices/create')
                    ->assertSee('The wattage field must be an integer.');
        });
    }

    // ────────────────────────────────────────────────────────────
    // TC.Consumption.003 – Zero Wattage (Negative)
    //
    // Pre-condition : User is logged in. On /devices/create.
    // Expected      : Wattage = 0 is rejected by the min:1 rule.
    //
    // Note: The wattage input has no HTML5 'min' attribute, so no
    //       JS removal is strictly needed—but it is kept as a guard.
    // ────────────────────────────────────────────────────────────
    public function test_TC_Consumption_003_zero_wattage_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $this->loginAs($browser, $user);

            $browser->visit('/devices/create')
                    ->type('input[name="name"]', 'Phantom Device')
                    ->type('input[name="wattage"]', '0')
                    ->select('select[name="category"]', 'Other');

            // Remove HTML5 min constraint (guard) so the form POSTs 0 to the server
            $browser->script(
                "document.querySelector('input[name=\"wattage\"]').removeAttribute('min');"
            );

            $browser->click('button.btn-primary');

            // DeviceController validation: 'wattage' => 'required|integer|min:1'
            $browser->assertPathIs('/devices/create')
                    ->assertSee('The wattage field must be at least 1.');
        });
    }

    // ────────────────────────────────────────────────────────────
    // TC.Consumption.004 – Empty State Placeholders (Negative)
    //
    // Pre-condition : User is logged in. Zero devices exist.
    // Expected      : Dashboard shows the correct empty-state text
    //                 in every widget area.
    //
    // Exact strings verified against dashboard.blade.php:
    //   line 334 – Top-5 consumers empty placeholder
    //   line 359 – Energy by category placeholder
    //   line 410 – 7-Day trend empty placeholder
    //   line 502 – All Devices table empty placeholder
    //   line 170 – totalDevices stat card shows 0
    //   line 218 – topConsumer is null → 'N/A'
    // ────────────────────────────────────────────────────────────
    public function test_TC_Consumption_004_empty_state_placeholders_shown(): void
    {
        $user = User::factory()->create();
        // No devices created intentionally

        $this->browse(function (Browser $browser) use ($user) {
            $this->loginAs($browser, $user);

            $browser->visit('/dashboard')
                    // Total Devices stat card
                    ->assertSee('0')
                    // Top Consumer stat card
                    ->assertSee('N/A')
                    // Top-5 consumers empty placeholder
                    ->assertSee('No devices added yet. Add some devices to see your consumption.')
                    // All Devices table empty placeholder
                    ->assertSee('No devices added yet. Add some devices to see your energy breakdown.')
                    // 7-Day trend chart empty placeholder
                    ->assertSee('No devices added yet. Add some devices to see your trend.')
                    // Energy by category empty placeholder
                    ->assertSee('No data available');
        });
    }
}
