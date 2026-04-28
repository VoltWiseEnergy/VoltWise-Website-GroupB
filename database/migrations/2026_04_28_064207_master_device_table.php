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
        Schema::create('master_devices', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Device template name');
            $table->string('category')->comment('Device category e.g. Lighting, Cooling');
            $table->decimal('wattage', 10, 2)->comment('Power consumption in watts');
            $table->text('description')->nullable()->comment('Optional device description');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_devices');
    }
};