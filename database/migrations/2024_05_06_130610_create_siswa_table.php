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
        Schema::create('siswa', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('peserta_didik_id');
            $table->string('sekolah_id');
            $table->string('nama');
            $table->string('jenis_kelamin');
            $table->string('nisn');
            $table->string('nik');
            $table->string('no_kk');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->string('agama_id');
            $table->string('agama');
            $table->string('kewarganegaraan');
            $table->string('alamat_jalan');
            $table->string('rt');
            $table->string('rw');
            $table->string('nama_dusun');
            $table->string('desa_kelurahan');
            $table->string('kode_wilayah');
            $table->string('kode_kecamatan');
            $table->string('kecamatan');
            $table->string('kode_kabupaten');
            $table->string('kabupaten');
            $table->string('kode_provinsi');
            $table->string('provinsi');
            $table->string('kode_pos');
            $table->string('lintang');
            $table->string('bujur');
            $table->string('nik_ayah');
            $table->string('nama_ayah');
            $table->string('tahun_lahir_ayah');
            $table->string('pekerjaan_id_ayah');
            $table->string('pekerjaan_ayah');
            $table->string('penghasilan_id_ayah');
            $table->string('penghasilan_ayah');
            $table->string('jenjang_pendidikan_ayah');
            $table->string('jenjang_pendidikan_ayah_keterangan');
            $table->string('nik_ibu');
            $table->string('nama_ibu_kandung');
            $table->string('tahun_lahir_ibu');
            $table->string('pekerjaan_id_ibu');
            $table->string('pekerjaan_ibu');
            $table->string('penghasilan_id_ibu');
            $table->string('penghasilan_ibu');
            $table->string('jenjang_pendidikan_ibu');
            $table->string('jenjang_pendidikan_ibu_keterangan');
            $table->string('nik_wali');
            $table->string('nama_wali');
            $table->string('tahun_lahir_wali');
            $table->string('pekerjaan_id_wali');
            $table->string('pekerjaan_wali');
            $table->string('penghasilan_id_wali');
            $table->string('penghasilan_wali');
            $table->string('jenjang_pendidikan_wali');
            $table->string('jenjang_pendidikan_wali_keterangan');
            $table->string('nomor_telepon_rumah');
            $table->string('nomor_telepon_seluler');
            $table->string('layak_PIP');
            $table->string('no_KIP');
            $table->string('nm_KIP');
            $table->text('raw_json');
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
        Schema::dropIfExists('siswa');
    }
};
