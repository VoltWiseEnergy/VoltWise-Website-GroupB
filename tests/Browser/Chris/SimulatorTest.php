<?php

namespace Tests\Browser;

use App\Models\Device;
use App\Models\SimulatorScenario;
use App\Models\User;
use App\Models\UsageLog;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class SimulatorTest extends DuskTestCase
{
    // =========================================================
    // TC.Sim.001 — PBI #56 Positive
    // User successfully creates a new scenario
    // =========================================================
    public function test_TC_Sim_001_user_can_create_new_scenario()
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();

            $device = Device::create([
                'user_id'  => $user->id,
                'name'     => 'AC Living Room',
                'wattage'  => 900,
                'category' => 'Cooling',
            ]);

            UsageLog::create([
                'user_id'     => $user->id,
                'device_id'   => $device->id,
                'device_name' => $device->name,
                'wattage'     => $device->wattage,
                'hours'       => 8,
                'usage_date'  => now()->toDateString(),
                'is_override' => false,
            ]);

            $browser->loginAs($user)
                    ->visit('/simulator')
                    ->pause(1000)
                    ->assertSee('Energy Cost Simulator')
                    ->type('name', 'Reduce AC by 50%')
                    ->select('device_id', $device->id)
                    ->pause(500)
                    ->assertSee('Actual data available')
                    ->type('scenario_hours', '4')
                    ->press('Calculate Simulation')
                    ->pause(1000)
                    ->assertPathIs('/simulator')
                    ->assertSee('saved successfully')
                    ->assertSee('Reduce AC by 50%');
        });
    }

    // =========================================================
    // TC.Sim.002 — PBI #56 Negative
    // User tries to submit without filling Scenario Name
    // =========================================================
    public function test_TC_Sim_002_cannot_submit_without_scenario_name()
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();

            $device = Device::create([
                'user_id'  => $user->id,
                'name'     => 'AC Living Room',
                'wattage'  => 900,
                'category' => 'Cooling',
            ]);

            UsageLog::create([
                'user_id'     => $user->id,
                'device_id'   => $device->id,
                'device_name' => $device->name,
                'wattage'     => $device->wattage,
                'hours'       => 8,
                'usage_date'  => now()->toDateString(),
                'is_override' => false,
            ]);

            $browser->loginAs($user)
                    ->visit('/simulator')
                    ->pause(1000)
                    ->select('device_id', $device->id)
                    ->type('scenario_hours', '4')
                    ->press('Calculate Simulation')
                    ->pause(1000)
                    ->assertDontSee('saved successfully');
        });
    }

    // =========================================================
    // TC.Sim.003 — PBI #57 Positive
    // System calculates kWh and cost correctly
    // =========================================================
    public function test_TC_Sim_003_system_calculates_kwh_and_cost_correctly()
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();

            $device = Device::create([
                'user_id'  => $user->id,
                'name'     => 'AC Living Room',
                'wattage'  => 900,
                'category' => 'Cooling',
            ]);

            $scenario = SimulatorScenario::create([
                'user_id'        => $user->id,
                'name'           => 'Cost Calculation Test',
                'device_id'      => $device->id,
                'device_name'    => $device->name,
                'wattage'        => 900,
                'current_hours'  => 8,
                'scenario_hours' => 4,
            ]);

            $browser->loginAs($user)
                    ->visit("/simulator/{$scenario->id}")
                    ->pause(1000)
                    ->assertSee('7.2 kWh')
                    ->assertSee('3.6 kWh');
        });
    }

    // =========================================================
    // TC.Sim.004 — PBI #57 Negative
    // User tries to input scenario hours more than 24
    // =========================================================
    public function test_TC_Sim_004_cannot_input_scenario_hours_more_than_24()
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();

            $device = Device::create([
                'user_id'  => $user->id,
                'name'     => 'AC Living Room',
                'wattage'  => 900,
                'category' => 'Cooling',
            ]);

            UsageLog::create([
                'user_id'     => $user->id,
                'device_id'   => $device->id,
                'device_name' => $device->name,
                'wattage'     => $device->wattage,
                'hours'       => 8,
                'usage_date'  => now()->toDateString(),
                'is_override' => false,
            ]);

            $browser->loginAs($user)
                    ->visit('/simulator')
                    ->pause(1000)
                    ->type('name', 'Invalid Hours Test')
                    ->select('device_id', $device->id)
                    ->type('scenario_hours', '25')
                    ->press('Calculate Simulation')
                    ->pause(1000)
                    ->assertDontSee('saved successfully');
        });
    }

    // =========================================================
    // TC.Sim.005 — PBI #58 Positive
    // Comparison page shows correct savings
    // =========================================================
    public function test_TC_Sim_005_comparison_shows_correct_savings()
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();

            $device = Device::create([
                'user_id'  => $user->id,
                'name'     => 'AC Living Room',
                'wattage'  => 900,
                'category' => 'Cooling',
            ]);

            $scenario = SimulatorScenario::create([
                'user_id'        => $user->id,
                'name'           => 'Reduce AC by 50%',
                'device_id'      => $device->id,
                'device_name'    => $device->name,
                'wattage'        => 900,
                'current_hours'  => 8,
                'scenario_hours' => 4,
                'tariff'         => 1444.70,
            ]);

            $browser->loginAs($user)
                    ->visit("/simulator/{$scenario->id}")
                    ->pause(1000)
                    ->assertSee('Actual Usage')
                    ->assertSee('Simulated Scenario')
                    ->assertSee('Consumption Comparison')
                    ->assertSee('Savings Summary')
                    ->assertSee('8 hrs')
                    ->assertSee('7.2 kWh')
                    ->assertSee('4 hrs')
                    ->assertSee('3.6 kWh')
                    ->assertSee('Save 50%');
        });
    }

    // =========================================================
    // TC.Sim.006 — PBI #58 Negative
    // User tries to access another user's comparison page
    // =========================================================
    public function test_TC_Sim_006_cannot_access_other_users_scenario()
    {
        $this->browse(function (Browser $browser) {
            $userA = User::factory()->create();
            $userB = User::factory()->create();

            $device = Device::create([
                'user_id'  => $userA->id,
                'name'     => 'AC Living Room',
                'wattage'  => 900,
                'category' => 'Cooling',
            ]);

            $scenario = SimulatorScenario::create([
                'user_id'        => $userA->id,
                'name'           => 'Private Scenario',
                'device_id'      => $device->id,
                'device_name'    => $device->name,
                'wattage'        => 900,
                'current_hours'  => 8,
                'scenario_hours' => 4,
                'tariff'         => 1444.70,
            ]);

            $browser->loginAs($userB)
                    ->visit("/simulator/{$scenario->id}")
                    ->pause(1000)
                    ->assertSee('403');
        });
    }
}