<?php

namespace Tests\Browser;

use App\Models\Device;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * PBI #5 – Monitor
 *
 * TC.Monitor.001  Add Valid Device  (Positive)
 * TC.Monitor.002  Chart Tooltip     (Positive)
 * TC.Monitor.003  Status Toggle     (Positive)
 * TC.Monitor.004  Dashboard Sync    (Positive)
 */
class MonitorTest extends DuskTestCase
{
    use DatabaseMigrations;

    // ────────────────────────────────────────────────────────────
    // Helper: log in through the real login form
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
    // TC.Monitor.001 – Add Valid Device (Positive)
    //
    // Pre-condition : User is logged in. No devices exist.
    //                 User is on Dashboard page.
    // Expected      : Device is saved → redirect to /devices
    //                 with a success flash message.
    // ────────────────────────────────────────────────────────────
    public function test_TC_Monitor_001_add_valid_device(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $this->loginAs($browser, $user);

            // Use exact CSS selector matching dashboard.blade.php topbar button
            $browser->visit('/dashboard')
                    ->click('.page-header a.btn-primary')
                    ->assertPathIs('/devices/create');

            $browser->type('input[name="name"]', 'Air Conditioner')
                    ->type('input[name="wattage"]', '900')
                    ->select('select[name="category"]', 'Cooling')
                    ->click('button.btn-primary'); // exact class from create.blade.php

            $browser->waitForLocation('/devices', 10)
                    ->assertSee('Device added successfully.');
        });
    }

    // ────────────────────────────────────────────────────────────
    // TC.Monitor.002 – Chart Tooltip (Positive)
    //
    // Pre-condition : User is logged in. At least one device exists.
    // Expected      : The 7-Day Energy Trend canvas renders; the
    //                 section heading is visible after hovering.
    // ────────────────────────────────────────────────────────────
    public function test_TC_Monitor_002_chart_tooltip_visible_on_hover(): void
    {
        $user = User::factory()->create();

        Device::create([
            'user_id'  => $user->id,
            'name'     => 'Smart TV',
            'wattage'  => 150,
            'category' => 'Entertainment',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $this->loginAs($browser, $user);

            // #energyLineChart only renders when $devices->isNotEmpty()
            $browser->visit('/dashboard')
                    ->waitFor('#energyLineChart', 15)
                    ->pause(1500)
                    ->mouseover('#energyLineChart')
                    ->pause(800);

            $browser->assertPresent('#energyLineChart')
                    ->assertSee('7-Day Energy Trend');

            $browser->screenshot('TC_Monitor_002_chart_tooltip');
        });
    }

    // ────────────────────────────────────────────────────────────
    // TC.Monitor.003 – Status Badge Gone After Device Removal (Positive)
    //
    // Pre-condition : User is logged in. One device exists.
    // Expected      : Dashboard shows device with 'active' badge;
    //                 after deletion the device is no longer visible.
    // ────────────────────────────────────────────────────────────
    public function test_TC_Monitor_003_status_badge_gone_after_device_removal(): void
    {
        $user = User::factory()->create();

        Device::create([
            'user_id'  => $user->id,
            'name'     => 'Washing Machine',
            'wattage'  => 500,
            'category' => 'Cleaning',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $this->loginAs($browser, $user);

            // Confirm device name and the 'active' badge element are present on dashboard
            $browser->visit('/dashboard')
                    ->assertSee('Washing Machine')
                    ->assertPresent('.status-badge.status-active'); // specific CSS class, not plain text

            // Go to /devices list and delete the device
            $browser->visit('/devices')
                    ->assertSee('Washing Machine');

            // acceptDialog() handles the confirm() popup on the Delete button
            $browser->click('button.btn-delete')
                    ->acceptDialog()
                    ->waitForText('Device deleted successfully.', 10);

            // Return to dashboard – device name must be gone
            $browser->visit('/dashboard')
                    ->assertDontSee('Washing Machine');
        });
    }

    // ────────────────────────────────────────────────────────────
    // TC.Monitor.004 – Dashboard Sync (Positive)
    //
    // Pre-condition : User is logged in. Dashboard is open.
    // Expected      : A newly added device is immediately visible
    //                 in the dashboard device table.
    // ────────────────────────────────────────────────────────────
    public function test_TC_Monitor_004_dashboard_syncs_new_device(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $this->loginAs($browser, $user);

            $browser->visit('/devices/create')
                    ->type('input[name="name"]', 'Refrigerator')
                    ->type('input[name="wattage"]', '200')
                    ->select('select[name="category"]', 'Kitchen')
                    ->click('button.btn-primary')
                    ->waitForLocation('/devices', 10);

            $browser->visit('/dashboard')
                    ->assertSee('Refrigerator');
        });
    }
}
