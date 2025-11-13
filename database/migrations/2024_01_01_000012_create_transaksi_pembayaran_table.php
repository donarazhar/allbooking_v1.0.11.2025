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
        Schema::create('transaksi_pembayaran', function (Blueprint $table) {
            $table->id();
            $table->date('tgl_pembayaran');
            $table->foreignId('booking_id')->constrained('transaksi_booking')->onDelete('cascade');
            $table->enum('jenis_bayar', ['DP', 'Termin 1', 'Termin 2', 'Termin 3', 'Pelunasan']);
            $table->string('bukti_bayar');
            $table->decimal('nominal', 15, 2);
            $table->foreignId('cabang_id')->constrained('cabang')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_pembayaran');
    }
};