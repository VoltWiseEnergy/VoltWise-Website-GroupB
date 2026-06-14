<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\ForumPost;
use App\Models\ForumComment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReplyPostTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TC.VW.011.001
     * Reply to Posts – Positive
     *
     * Scenario:
     * User submits a valid reply.
     */
    public function test_tc_vw_011_001_user_submits_valid_reply()
    {
        $user = User::factory()->create();

        $post = ForumPost::create([
            'user_id' => $user->id,
            'title' => 'Energy Saving Tips',
            'content' => 'Share your ideas here.',
            'votes' => 0,
            'status' => 'published',
        ]);

        $response = $this->actingAs($user)
            ->post(route('forum.comment.store', $post->id), [
                'content' => 'I recommend using LED bulbs.',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('forum_comments', [
            'forum_post_id' => $post->id,
            'user_id' => $user->id,
            'content' => 'I recommend using LED bulbs.',
            'votes' => 0,
        ]);

        $this->assertEquals(
            1,
            ForumComment::where('forum_post_id', $post->id)->count()
        );
    }

    /**
     * TC.VW.011.002
     * Reply to Posts – Negative (Empty State)
     *
     * Scenario:
     * User submits an empty reply.
     */
    public function test_tc_vw_011_002_user_submits_empty_reply()
    {
        $user = User::factory()->create();

        $post = ForumPost::create([
            'user_id' => $user->id,
            'title' => 'Energy Saving Tips',
            'content' => 'Share your ideas here.',
            'votes' => 0,
            'status' => 'published',
        ]);

        $response = $this->actingAs($user)
            ->from(route('forum.show', $post->id))
            ->post(route('forum.comment.store', $post->id), [
                'content' => '',
            ]);

        $response->assertRedirect(route('forum.show', $post->id));

        $response->assertSessionHasErrors([
            'content',
        ]);

        $this->assertDatabaseMissing('forum_comments', [
            'forum_post_id' => $post->id,
            'user_id' => $user->id,
        ]);

        $this->assertEquals(
            0,
            ForumComment::where('forum_post_id', $post->id)->count()
        );
    }
}