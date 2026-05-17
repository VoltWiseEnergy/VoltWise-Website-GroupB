<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->decimal('daily_energy_kwh', 8, 3)->nullable();
            $table->decimal('tariff', 12, 2)->nullable();
        });
    }

    public function down()
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn(['daily_energy_kwh', 'tariff']);
        });
    }

};
