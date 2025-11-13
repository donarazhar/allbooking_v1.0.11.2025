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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('email')->unique();
            $table->string('password');
            $table->string('no_hp', 20)->unique();
            $table->text('alamat')->nullable();
            // Tambah kolom baru setelah kolom alamat
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan'])->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->string('nik', 16)->nullable();
            // Kolom wilayah
            $table->string('provinsi_id', 2)->nullable();
            $table->string('provinsi_nama')->nullable();
            $table->string('kabupaten_id', 4)->nullable();
            $table->string('kabupaten_nama')->nullable();
            $table->string('kecamatan_id', 7)->nullable();
            $table->string('kecamatan_nama')->nullable();
            $table->string('kelurahan_id', 10)->nullable();
            $table->string('kelurahan_nama')->nullable();
            $table->string('kode_pos', 5)->nullable();
            $table->string('foto')->nullable();
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('cabang_id')->constrained('cabang')->onDelete('cascade');
            $table->enum('status_users', ['active', 'inactive'])->default('active');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
