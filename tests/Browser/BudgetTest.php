<?php

namespace Tests\Browser;

use App\Models\Device;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;


class BudgetTest extends DuskTestCase
{
    use DatabaseMigrations;

    // ────────────────────────────────────────────────────────────
    // Helpers
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

    /**
     * Click the budget button and wait for the modal overlay to gain class 'open'.
     * The dashboard JS does: overlay.classList.add('open') on button click.
     */
    private function openBudgetModal(Browser $browser): void
    {
        $browser->click('#open-budget-modal')
                ->waitUntil(
                    "document.getElementById('budget-modal-overlay').classList.contains('open')",
                    5
                );
    }

    // ────────────────────────────────────────────────────────────
    // TC.Budget.001 – Set Budget (Positive)
    //
    // Pre-condition : User is logged in. No budget set. Dashboard open.
    // Expected      : User opens the modal, saves 500 000, dashboard
    //                 shows the formatted budget amount.
    // ────────────────────────────────────────────────────────────
    public function test_TC_Budget_001_set_valid_budget(): void
    {
        // monthly_budget defaults to null (nullable column)
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $this->loginAs($browser, $user);

            $browser->visit('/dashboard')
                    ->assertSee('No monthly budget set yet.');

            $this->openBudgetModal($browser);

            $browser->type('#monthly_budget', '500000')
                    ->click('button.btn-modal-save')
                    ->waitForText('Rp 500.000', 10)
                    ->assertPathIs('/dashboard');
        });
    }

    // ────────────────────────────────────────────────────────────
    // TC.Budget.002 – Over Budget (Negative)
    //
    // Pre-condition : User is logged in. No budget set. Dashboard open.
    // Expected      : When monthly cost exceeds the budget the danger
    //                 indicator and warning text are visible.
    // ────────────────────────────────────────────────────────────
    public function test_TC_Budget_002_over_budget_danger_indicator_shown(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $this->loginAs($browser, $user);

            // Set an absurdly small budget (1 Rp) so any device exceeds it
            $browser->visit('/dashboard');
            $this->openBudgetModal($browser);
            $browser->type('#monthly_budget', '1000')
                    ->click('button.btn-modal-save')
                    ->waitForText('Rp 1.000', 10)
                    ->assertPathIs('/dashboard');

            // Add a 5 000 W device → monthly cost ≫ 1 Rp
            // DashboardController: daily_kwh = 5000/1000 * 1 = 5 kWh
            // monthlyCost = 5 * 30 * 1444 = 216 600 Rp → pct = 100 → 'danger'
            $browser->visit('/devices/create')
                    ->type('input[name="name"]', 'Heater')  
                    ->type('input[name="wattage"]', '5000')
                    ->select('select[name="category"]', 'Other')
                    ->click('button.btn-primary')
                    ->waitForLocation('/devices', 10);

            $browser->visit('/dashboard')
                    ->assertSee('Approaching or over budget!');

        });
    }

    // ────────────────────────────────────────────────────────────
    // TC.Budget.003 – Cost Logic (Positive)
    //
    // Pre-condition : User is logged in and there is a device.
    // Expected      : Daily cost = wattage/1000 * 1 h * 1444 Rp/kWh
    //                 A 100 W device → 0.1 kWh × 1444 = 144.4 ≈ Rp 144
    //                 (number_format rounds to 0 decimals)
    // ────────────────────────────────────────────────────────────
    public function test_TC_Budget_003_daily_cost_calculation_correct(): void
    {
        $user = User::factory()->create();

        Device::create([
            'user_id'  => $user->id,
            'name'     => 'LED Lamp',
            'wattage'  => 100,
            'category' => 'Lighting',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $this->loginAs($browser, $user);

            // Dashboard "All Devices" table shows: Rp 144 in the Daily Cost column
            // dashboard.blade.php: $cost = $device->daily_energy_kwh * 1444
            // number_format(144.4, 0, ',', '.') = '144'
            $browser->visit('/dashboard')
                    ->assertSee('LED Lamp')
                    ->assertSee('Rp 144');
        });
    }

    // ────────────────────────────────────────────────────────────
    // TC.Budget.004 – Invalid Budget / Negative Value (Negative)
    //
    // Pre-condition : User is logged in. No budget set.
    // Expected      : Server rejects negative value (rule: min:0).
    //                 Error message shown; budget remains unset.
    // ────────────────────────────────────────────────────────────
    public function test_TC_Budget_004_negative_budget_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $this->loginAs($browser, $user);

            $browser->visit('/dashboard');
            $this->openBudgetModal($browser);

            $browser->script(
                "document.getElementById('monthly_budget').removeAttribute('min');"
            );

            $browser->type('#monthly_budget', '-50000')
                    ->click('button.btn-modal-save')
                    ->waitForText('Budget cannot be negative.', 10);
        });
    }
}
