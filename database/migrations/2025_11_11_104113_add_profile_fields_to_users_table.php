<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambah kolom baru setelah kolom alamat
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan'])->nullable()->after('alamat');
            $table->date('tgl_lahir')->nullable()->after('jenis_kelamin');
            $table->string('nik', 16)->nullable()->after('tgl_lahir');
            
            // Kolom wilayah - FIXED SIZE
            $table->string('provinsi_id', 2)->nullable()->after('nik');
            $table->string('provinsi_nama')->nullable()->after('provinsi_id');
            $table->string('kabupaten_id', 4)->nullable()->after('provinsi_nama');
            $table->string('kabupaten_nama')->nullable()->after('kabupaten_id');
            $table->string('kecamatan_id', 7)->nullable()->after('kabupaten_nama'); // ✅ FIXED: 6 → 7
            $table->string('kecamatan_nama')->nullable()->after('kecamatan_id');
            $table->string('kelurahan_id', 10)->nullable()->after('kecamatan_nama');
            $table->string('kelurahan_nama')->nullable()->after('kelurahan_id');
            $table->string('kode_pos', 5)->nullable()->after('kelurahan_nama');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'jenis_kelamin',
                'tgl_lahir',
                'nik',
                'provinsi_id',
                'provinsi_nama',
                'kabupaten_id',
                'kabupaten_nama',
                'kecamatan_id',
                'kecamatan_nama',
                'kelurahan_id',
                'kelurahan_nama',
                'kode_pos'
            ]);
        });
    }
};