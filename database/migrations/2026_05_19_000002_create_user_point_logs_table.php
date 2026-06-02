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
        Schema::create('user_point_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // event_type: consistent_logging | under_budget | low_usage | very_low_usage
            $table->string('event_type');
            $table->integer('points');
            $table->date('log_date');
            $table->timestamps();

            // Prevent double-awarding the same event on the same day
            $table->unique(['user_id', 'event_type', 'log_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_point_logs');
    }
};
