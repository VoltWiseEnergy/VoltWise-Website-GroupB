<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\ForumPost;
use App\Models\ForumComment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewDiscussionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TC.VW.006.001
     * View Discussion – Positive
     */
    public function test_tc_vw_006_001_user_views_existing_discussion_posts()
    {
        $user = User::factory()->create();

        $post = ForumPost::create([
            'user_id' => $user->id,
            'title' => 'Energy Saving Tips',
            'content' => 'Share your best energy-saving tips.',
            'votes' => 0,
            'status' => 'published',
        ]);

        ForumComment::create([
            'forum_post_id' => $post->id,
            'user_id' => $user->id,
            'content' => 'Use LED bulbs.',
            'votes' => 0,
        ]);

        // Step 1: Navigate to Forum page
        $forumResponse = $this->actingAs($user)
            ->get(route('forum.index'));

        $forumResponse->assertStatus(200);
        $forumResponse->assertSee('Energy Saving Tips');

        // Step 2 & 3: Open discussion and view comments section
        $discussionResponse = $this->actingAs($user)
            ->get(route('forum.show', $post->id));

        $discussionResponse->assertStatus(200);

        $discussionResponse->assertSee('Energy Saving Tips');
        $discussionResponse->assertSee('Share your best energy-saving tips.');
        $discussionResponse->assertSee('Comments');
        $discussionResponse->assertSee('Use LED bulbs.');
    }

    /**
     * TC.VW.006.002
     * View Discussion – Negative (Empty State)
     */
    public function test_tc_vw_006_002_user_accesses_empty_forum()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('forum.index'));

        $response->assertStatus(200);

        $response->assertSee('No forum posts yet.');
    }
}