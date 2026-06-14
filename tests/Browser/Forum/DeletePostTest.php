<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\ForumPost;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class DeletePostTest extends DuskTestCase
{
    /**
     * TC.VW.008.001
     * Positive Test:
     * User successfully deletes their own post
     */
    public function test_user_can_delete_forum_post()
    {
        $user = User::factory()->create();

        $post = ForumPost::create([
            'user_id' => $user->id,
            'title' => 'Delete Me',
            'content' => 'This post should be deleted.',
            'votes' => 0,
            'status' => 'published',
        ]);

        $this->browse(function (Browser $browser) use ($user, $post) {

            $browser->loginAs($user)
                ->visit(route('forum.show', $post->id))

                ->assertSee('Delete Me')

                ->click('@post-menu-button')

                ->press('@delete-post-button')
                ->acceptDialog()

                ->waitForLocation('/forum')

                ->assertPathIs('/forum')
                ->assertSee('Post deleted successfully.')
                ->assertDontSee('Delete Me');
        });
    }

    /**
     * TC.VW.008.002
     * Negative Test:
     * User cancels deletion
     */
    public function test_user_can_cancel_forum_post_deletion()
    {
        $user = User::factory()->create();

        $post = ForumPost::create([
            'user_id' => $user->id,
            'title' => 'Keep Me',
            'content' => 'This post should remain.',
            'votes' => 0,
            'status' => 'published',
        ]);

        $this->browse(function (Browser $browser) use ($user, $post) {

            $browser->loginAs($user)
                ->visit(route('forum.show', $post->id))

                ->assertSee('Keep Me')

                ->click('@post-menu-button')

                ->press('@delete-post-button')
                ->dismissDialog()

                ->pause(1000)

                ->assertPathIs('/forum/' . $post->id)
                ->assertSee('Keep Me');
        });
    }
}