<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cabang_catering', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cabang_id')->constrained('cabang')->onDelete('cascade');
            $table->foreignId('catering_id')->constrained('catering')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['cabang_id', 'catering_id']); // Mencegah duplikasi
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cabang_catering');
    }
};
