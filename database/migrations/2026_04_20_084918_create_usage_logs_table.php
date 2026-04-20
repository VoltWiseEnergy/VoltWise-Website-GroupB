<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('device_id');        // dari tabel devices William nanti
            $table->string('device_name');      // disimpan juga biar history tetap ada walau device dihapus
            $table->integer('wattage');
            $table->decimal('hours', 5, 2);     // jam pemakaian
            $table->date('usage_date');
            $table->boolean('is_override')->default(false);
            $table->timestamps();
 
            // satu device satu catatan per hari
            $table->unique(['user_id', 'device_id', 'usage_date']);
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('usage_logs');
    }
};