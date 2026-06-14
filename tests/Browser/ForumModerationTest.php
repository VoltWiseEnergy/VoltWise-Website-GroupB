<?php

use App\Models\User;
use App\Models\ForumPost;
use Laravel\Dusk\Browser;

// =====================================================================
// PBI #52 — FORUM MODERATION (View List)
// =====================================================================

test('admin can view the forum moderation dashboard', function () {
    $admin = User::where('email', 'admin@voltwise.test')->first() ?? User::factory()->create(['role' => 'admin']);

    $this->browse(function (Browser $browser) use ($admin) {
        $browser->loginAs($admin)
                ->visit('/admin/forum')
                ->assertSee('Forum Moderation')
                ->assertSee('Manage forum posts, verify information, and review reports')
                ->assertSee('All Posts')
                ->assertSee('Reported')
                ->assertSee('Verified');
    });
});

test('regular user cannot access forum moderation', function () {
    $user = User::factory()->create(['role' => 'user']);

    $this->browse(function (Browser $browser) use ($user) {
        $browser->loginAs($user)
                ->visit('/admin/forum')
                ->assertSee('Unauthorized'); // EnsureRole middleware throws 'Unauthorized. You do not have the required role.'
    });
});

// =====================================================================
// PBI #53 — FORUM MODERATION (Hide Post)
// =====================================================================

test('admin can hide a forum post', function () {
    $admin = User::where('email', 'admin@voltwise.test')->first() ?? User::factory()->create(['role' => 'admin']);
    $user  = User::factory()->create();
    
    // Create a dummy post to hide
    $post = ForumPost::create([
        'user_id' => $user->id,
        'title'   => 'Inappropriate Post Title ' . time(),
        'content' => 'This is a bad post.',
        'status'  => 'published'
    ]);

    $this->browse(function (Browser $browser) use ($admin, $post) {
        $browser->loginAs($admin)
                ->visit('/admin/forum')
                ->assertSee($post->title)
                ->press('Hide')
                ->acceptDialog() // Accepts the confirm('Hide this post?') dialog
                ->waitForLocation('/admin/forum')
                ->assertSee('hidden'); // It will show the 'hidden' badge
    });
});

test('cancel on hide confirmation does not hide post', function () {
    $admin = User::where('email', 'admin@voltwise.test')->first() ?? User::factory()->create(['role' => 'admin']);
    $user  = User::factory()->create();
    
    $post = ForumPost::create([
        'user_id' => $user->id,
        'title'   => 'Safe Post Title ' . time(),
        'content' => 'This is a good post.',
        'status'  => 'published'
    ]);

    $this->browse(function (Browser $browser) use ($admin, $post) {
        $browser->loginAs($admin)
                ->visit('/admin/forum')
                ->assertSee($post->title)
                ->press('Hide')
                ->dismissDialog() // Clicks Cancel on the confirm() dialog
                ->assertPathIs('/admin/forum')
                ->assertSee('published'); // Status remains published
    });
});

// =====================================================================
// PBI #55 — REPORT POST
// =====================================================================

test('user can report a forum post', function () {
    $user1 = User::factory()->create(); // Author
    $user2 = User::factory()->create(); // Reporter

    $post = ForumPost::create([
        'user_id' => $user1->id,
        'title'   => 'Spam Post Title ' . time(),
        'content' => 'Buy cheap electronics here!',
        'status'  => 'published'
    ]);

    $this->browse(function (Browser $browser) use ($user2, $post) {
        $browser->loginAs($user2)
                ->visit('/forum/' . $post->id)
                ->assertSee($post->title)
                ->press('🚩 Report')
                ->pause(500) // Wait for modal to become visible
                ->select('reason', 'Spam or misleading')
                ->press('Submit Report')
                ->waitForLocation('/forum/' . $post->id)
                ->assertSee('Post has been reported'); // Matches controller success message
    });
});

test('author cannot report their own post', function () {
    $author = User::factory()->create();

    $post = ForumPost::create([
        'user_id' => $author->id,
        'title'   => 'My Personal Post ' . time(),
        'content' => 'I cannot report this.',
        'status'  => 'published'
    ]);

    $this->browse(function (Browser $browser) use ($author, $post) {
        $browser->loginAs($author)
                ->visit('/forum/' . $post->id)
                ->assertDontSee('🚩 Report');
    });
});
