<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('tanggal_booking');
            $table->foreignId('buka_jadwal_id')->constrained('buka_jadwal')->onDelete('cascade');
            $table->foreignId('catering_id')->nullable()->constrained('catering')->onDelete('set null');
            $table->date('tgl_expired_booking')->nullable();
            $table->enum('status_bookings', ['active', 'inactive'])->default('active');
            $table->string('keterangan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
