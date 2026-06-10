<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('forum_post_votes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('forum_post_id')
                ->constrained('forum_posts')
                ->onDelete('cascade');

            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');

            // 1 = upvote, -1 = downvote
            $table->integer('vote');

            $table->timestamps();

            // Prevent duplicate votes from the same user
            $table->unique(['forum_post_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_post_votes');
    }
};