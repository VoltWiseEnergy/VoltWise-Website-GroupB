<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            // Unique machine-readable key, e.g. 'week_streak'
            $table->string('key')->unique();
            $table->string('name');
            $table->string('description');
            $table->string('emoji');
            // Category: streak | savings | usage | milestone
            $table->string('category');
            // Hex color used for the badge card accent, e.g. '#10b981'
            $table->string('color', 20);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('badges');
    }
};
