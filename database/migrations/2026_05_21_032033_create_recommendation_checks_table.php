<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recommendation_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('recommendation_key'); // unique key per recommendation
            $table->boolean('is_checked')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'recommendation_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recommendation_checks');
    }
};