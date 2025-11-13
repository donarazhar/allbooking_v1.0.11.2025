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
        Schema::create('transaksi_booking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cabang_id')->constrained('cabang')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('bukajadwal_id')->constrained('buka_jadwal')->onDelete('cascade');
            $table->foreignId('catering_id')->nullable()->constrained('catering')->onDelete('set null');
            $table->date('tgl_booking');
            $table->dateTime('tgl_expired_booking')->nullable();
            $table->enum('status_booking', ['active', 'inactive', 'expired', 'cancelled'])->default('active');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_booking');
    }
};