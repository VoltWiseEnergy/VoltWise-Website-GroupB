<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('forum_posts', function (Blueprint $table) {
            $table->enum('status', ['published', 'reported', 'hidden'])->default('published')->after('votes');
            $table->boolean('is_verified')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('forum_posts', function (Blueprint $table) {
            $table->dropColumn(['status', 'is_verified']);
        });
    }
};
