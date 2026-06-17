<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class RecommendationTest extends DuskTestCase
{
    /**
     * PBI #42 - Analyze Consumption Pattern
     */
    public function testAnalyzeConsumptionPattern()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(\App\Models\User::first())
                    ->visit('/recommendations')
                    ->assertPathIs('/recommendations')
                    ->waitForText('Smart Recommendations', 5)
                    ->screenshot('PBI42-1_navigate_recommendations')

                    ->waitForText('Total Usage (30d)', 5)
                    ->assertSee('Daily Average')
                    ->assertSee('Top Consumer')
                    ->assertSee('Issues Found')
                    ->screenshot('PBI42-2_consumption_data_loaded')

                    ->waitFor('.energy-score-circle', 5)
                    ->assertPresent('.energy-score-number')
                    ->assertPresent('.energy-score-label')
                    ->screenshot('PBI42-3_energy_score_displayed');
        });
    }

    /**
     * PBI #43 - Generate Recommendations
     */
    public function testGenerateRecommendations()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(\App\Models\User::first())
                    ->visit('/recommendations')
                    ->assertPathIs('/recommendations')
                    ->waitForText('Smart Recommendations', 5)
                    ->screenshot('PBI43-1_navigate_recommendations')

                    ->waitFor('.rec-card', 5)
                    ->assertPresent('.rec-card')
                    ->screenshot('PBI43-2_recommendations_generated')

                    ->assertSee('Personalized Recommendations')
                    ->assertPresent('.rec-priority-badge')
                    ->screenshot('PBI43-3_recommendation_items_exist');
        });
    }

    /**
     * PBI #44 - Display Suggestions
     */
    public function testDisplaySuggestions()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(\App\Models\User::first())
                    ->visit('/recommendations')
                    ->assertPathIs('/recommendations')
                    ->waitForText('Smart Recommendations', 5)
                    ->screenshot('PBI44-1_navigate_recommendations')

                    ->waitFor('.rec-list', 5)
                    ->assertPresent('.rec-card')
                    ->assertSee('Personalized Recommendations')
                    ->screenshot('PBI44-2_recommendations_displayed')

                    ->click('.rec-checkbox')
                    ->pause(1000)
                    ->screenshot('PBI44-3_checklist_checked')

                    ->assertSee('What-if Simulator')
                    ->assertPresent('#sim-device')
                    ->screenshot('PBI44-4_whatif_simulator_visible')

                    ->select('#sim-device', $this->getFirstDeviceWattage())
                    ->pause(500)
                    ->tap(function ($b) {
                        $b->script("document.getElementById('sim-slider').value = 2; updateSimulator();");
                    })
                    ->pause(500)
                    ->assertVisible('#sim-results')
                    ->screenshot('PBI44-5_simulator_result');
        });
    }

    private function getFirstDeviceWattage()
    {
        $device = \App\Models\Device::where('user_id', \App\Models\User::first()->id)->first();
        return $device ? (string) $device->wattage : '';
    }
}