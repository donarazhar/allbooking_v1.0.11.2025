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
            $table->string('hari', 20);
            $table->date('tanggal');
            $table->foreignId('sesi_id')->constrained('sesi')->onDelete('cascade');
            $table->foreignId('jenisacara_id')->constrained('jenis_acara')->onDelete('cascade');
            $table->enum('status_jadwal', ['available', 'booked'])->default('available');
            $table->timestamps();
            
            // Unique constraint untuk mencegah duplikasi jadwal
            $table->unique(['tanggal', 'sesi_id', 'jenisacara_id']);
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