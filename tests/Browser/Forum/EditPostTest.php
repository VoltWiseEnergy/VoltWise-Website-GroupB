<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\ForumPost;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class EditPostTest extends DuskTestCase
{
    /**
     * TC.VW.007.001
     * Positive Test:
     * User edits own post within 1 hour
     */
    public function test_user_can_edit_own_post_within_one_hour()
    {
        $user = User::factory()->create();

        $post = ForumPost::create([
            'user_id' => $user->id,
            'title' => 'Original Title',
            'content' => 'Original Content',
            'votes' => 0,
            'status' => 'published',
            'created_at' => now(),
        ]);

        $this->browse(function (Browser $browser) use ($user, $post) {
            $browser->loginAs($user)
                ->visit("/forum/{$post->id}")

                ->assertSee('Original Title')

                ->click('@post-menu-button')

                ->pause(500)

                ->clickLink('Edit Post')

                ->assertPathIs("/forum/{$post->id}/edit")

                ->clear('title')
                ->type('title', 'Updated Title')

                ->clear('content')
                ->type('content', 'Updated Content')

                ->press('Save Changes')

                ->waitForLocation("/forum/{$post->id}")

                ->assertPathIs("/forum/{$post->id}")
                ->assertSee('Post updated successfully.')
                ->assertSee('Updated Title')
                ->assertSee('Updated Content');
        });
    }

    /**
     * TC.VW.007.002
     * Negative Test:
     * User attempts to edit after 1 hour
     */
    public function test_user_cannot_edit_post_after_one_hour()
    {
        $user = User::factory()->create();

        $post = ForumPost::create([
            'user_id' => $user->id,
            'title' => 'Expired Post',
            'content' => 'This post can no longer be edited.',
            'votes' => 0,
            'status' => 'published',
        ]);

        $post->created_at = now()->subHours(2);
        $post->save();

        $post->refresh();

        $this->browse(function (Browser $browser) use ($user, $post) {
            $browser->loginAs($user)
                ->visit("/forum/{$post->id}")

                ->assertSee('Expired Post')

                ->click('@post-menu-button')

                ->pause(500)

                ->assertSee('Edit Post (Expired)')

                ->click('@expired-edit-button')
                ->acceptDialog()

                ->assertPathIs("/forum/{$post->id}")
                ->assertSee('Expired Post');
        });
    }
}