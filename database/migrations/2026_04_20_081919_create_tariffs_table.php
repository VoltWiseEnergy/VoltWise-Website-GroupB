<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tariffs', function (Blueprint $table) {
            $table->id();
            $table->decimal('rate_per_kwh', 10, 2); // harga listrik
            $table->date('effective_date');         // tanggal berlaku
            $table->timestamps();                   // created_at & updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tariffs');
    }
};