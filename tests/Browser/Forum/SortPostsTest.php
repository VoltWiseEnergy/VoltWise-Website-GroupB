<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\ForumPost;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SortPostsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TC.VW.010.001
     * Sort Posts by Likes – Positive
     */
    public function test_tc_vw_010_001_user_sorts_posts_by_most_liked()
    {
        $user = User::factory()->create();

        $lowVotePost = ForumPost::create([
            'user_id' => $user->id,
            'title' => 'Low Vote Post',
            'content' => 'Content',
            'votes' => 2,
            'status' => 'published',
        ]);

        $highVotePost = ForumPost::create([
            'user_id' => $user->id,
            'title' => 'High Vote Post',
            'content' => 'Content',
            'votes' => 10,
            'status' => 'published',
        ]);

        $response = $this->actingAs($user)
            ->get(route('forum.index', ['sort' => 'top']));

        $response->assertStatus(200);

        $posts = $response->viewData('posts');

        $this->assertEquals(
            $highVotePost->id,
            $posts->first()->id
        );

        $this->assertEquals(
            10,
            $posts->first()->votes
        );
    }

    /**
     * TC.VW.010.002
     * Sort Posts by Likes – Negative (Default Sort)
     */
    public function test_tc_vw_010_002_user_switches_back_to_newest_posts()
    {
        $user = User::factory()->create();

        $olderPost = ForumPost::create([
            'user_id' => $user->id,
            'title' => 'Older Post',
            'content' => 'Content',
            'votes' => 50,
            'status' => 'published',
        ]);

        $olderPost->created_at = now()->subDays(2);
        $olderPost->updated_at = now()->subDays(2);
        $olderPost->save();

        $newerPost = ForumPost::create([
            'user_id' => $user->id,
            'title' => 'Newer Post',
            'content' => 'Content',
            'votes' => 1,
            'status' => 'published',
        ]);

        $response = $this->actingAs($user)
            ->get(route('forum.index'));

        $response->assertStatus(200);

        $posts = $response->viewData('posts');

        $this->assertEquals(
            $newerPost->id,
            $posts->first()->id
        );

        $this->assertEquals(
            'Newer Post',
            $posts->first()->title
        );
    }
}