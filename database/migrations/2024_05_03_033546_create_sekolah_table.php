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
        Schema::create('sekolah', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('sekolah_id');
            $table->string('nama');
            $table->string('npsn');
            $table->string('nss')->nullable();
            $table->string('bentuk_pendidikan_id');
            $table->string('bentuk_pendidikan');
            $table->string('status_sekolah_id')->nullable();
            $table->string('status_sekolah')->nullable();
            $table->string('alamat_jalan')->nullable();
            $table->string('rt')->nullable();
            $table->string('rw')->nullable();
            $table->string('nama_dusun')->nullable();
            $table->string('kode_wilayah')->nullable();
            $table->string('kode_desa_kelurahan')->nullable();
            $table->string('desa_kelurahan')->nullable();
            $table->string('kode_kecamatan');
            $table->string('kecamatan');
            $table->string('kode_kabupaten')->nullable();
            $table->string('kabupaten')->nullable();
            $table->string('kode_provinsi')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('kode_pos')->nullable();
            $table->string('lintang')->nullable();
            $table->string('bujur')->nullable();
            $table->string('nomor_telepon')->nullable();
            $table->string('nomor_fax')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->text('raw_json')->nullable();
            $table->unsignedInteger('jml')->nullable();
            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sekolah');
    }
};
