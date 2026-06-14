<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\ForumPost;
use App\Models\ForumPostVote;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VotePostTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TC.VW.009.001
     * Like & Dislike Posts (Positive)
     * User upvotes a post successfully.
     */
    public function test_tc_vw_009_001_user_can_upvote_a_post()
    {
        $user = User::factory()->create();

        $post = ForumPost::create([
            'user_id' => User::factory()->create()->id,
            'title' => 'Vote Test Post',
            'content' => 'Testing upvotes.',
            'votes' => 0,
            'status' => 'published',
        ]);

        $response = $this->actingAs($user)
            ->post(route('forum.upvote', $post->id));

        $response->assertRedirect();

        $this->assertDatabaseHas('forum_post_votes', [
            'forum_post_id' => $post->id,
            'user_id' => $user->id,
            'vote' => 1,
        ]);

        $this->assertEquals(
            1,
            $post->fresh()->votes
        );

        $this->assertTrue(
            ForumPostVote::where('forum_post_id', $post->id)
                ->where('user_id', $user->id)
                ->where('vote', 1)
                ->exists()
        );
    }

    /**
     * TC.VW.009.002
     * Like & Dislike Posts (Negative - Toggle Removal)
     * User clicks the same vote again.
     */
    public function test_tc_vw_009_002_clicking_same_upvote_removes_vote()
    {
        $user = User::factory()->create();

        $post = ForumPost::create([
            'user_id' => User::factory()->create()->id,
            'title' => 'Toggle Vote Test',
            'content' => 'Testing vote removal.',
            'votes' => 1,
            'status' => 'published',
        ]);

        ForumPostVote::create([
            'forum_post_id' => $post->id,
            'user_id' => $user->id,
            'vote' => 1,
        ]);

        $response = $this->actingAs($user)
            ->post(route('forum.upvote', $post->id));

        $response->assertRedirect();

        $this->assertDatabaseMissing('forum_post_votes', [
            'forum_post_id' => $post->id,
            'user_id' => $user->id,
            'vote' => 1,
        ]);

        $this->assertEquals(
            0,
            $post->fresh()->votes
        );
    }
}