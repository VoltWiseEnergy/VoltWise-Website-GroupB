<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');                         // e.g. "AC Sharp 1 PK"
            $table->string('category');                     // e.g. "Air Conditioner"
            $table->string('brand')->nullable();
            $table->decimal('power_watt', 10, 2);           // watt
            $table->decimal('usage_hours_per_day', 5, 2);   // hours/day
            $table->integer('usage_days_per_month')->default(30);
            $table->string('energy_label')->nullable();     // A, B, C, D, E (efficiency rating)
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
