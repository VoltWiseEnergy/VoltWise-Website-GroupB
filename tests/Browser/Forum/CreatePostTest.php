<?php

namespace Tests\Browser\William;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CreatePostTest extends DuskTestCase
{
    /**
     * TC.VW.005.001
     * Positive Test
     */
    public function test_user_can_create_post_with_valid_data()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)

                ->visit('/forum/create')
                ->assertSee('Create Forum Post')

                ->type('title', 'Saving Electricity Tips')
                ->type('content', 'Turn off unused appliances to reduce energy consumption.')

                ->press('Post Discussion')
                ->waitForLocation('/forum')

                ->assertPathIs('/forum')
                ->assertSee('Post created successfully.')
                ->assertSee('Saving Electricity Tips');
        });
    }

    /**
     * TC.VW.005.002
     * Negative Test
     */
    public function test_user_cannot_create_post_with_empty_fields()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)

                ->visit('/forum/create')
                ->assertSee('Create Forum Post')

                ->press('Post Discussion')

                ->assertPathIs('/forum/create')
                ->assertSee('The title field is required')
                ->assertSee('The content field is required');
        });
    }
}