<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->boolean('reminder_enabled')->default(false)->after('tariff');
            $table->time('reminder_time')->nullable()->after('reminder_enabled');
            $table->string('reminder_message')->nullable()->after('reminder_time');
        });
    }

    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn(['reminder_enabled', 'reminder_time', 'reminder_message']);
        });
    }
};
