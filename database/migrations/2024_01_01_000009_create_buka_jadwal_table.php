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
        Schema::create('buka_jadwal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cabang_id')->constrained('cabang')->onDelete('cascade');
            $table->string('hari', 20);
            $table->date('tanggal');
            $table->foreignId('sesi_id')->constrained('sesi')->onDelete('cascade');
            $table->foreignId('jenisacara_id')->constrained('jenis_acara')->onDelete('cascade');
            $table->enum('status_jadwal', ['available', 'booked'])->default('available');
            $table->timestamps();
        
            // Mencegah jadwal ganda pada tanggal & sesi di cabang yang sama
            $table->unique(['cabang_id', 'tanggal', 'sesi_id', 'jenisacara_id'], 'unique_jadwal_per_cabang');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buka_jadwal');
    }
};