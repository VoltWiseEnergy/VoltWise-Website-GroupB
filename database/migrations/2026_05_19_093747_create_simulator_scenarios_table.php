<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('simulator_scenarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');                  // nama skenario, misal "Kurangi AC 50%"
            $table->string('device_id');             // dari tabel devices William
            $table->string('device_name');           // disimpan biar tidak hilang kalau device dihapus
            $table->integer('wattage');
            $table->decimal('current_hours', 5, 2);  // jam aktual dari usage_logs
            $table->decimal('scenario_hours', 5, 2); // jam hipotesis yang diinput user
            $table->decimal('tariff', 10, 2)->default(1444); // Rp/kWh, default PLN
            $table->timestamps();
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('simulator_scenarios');
    }
};