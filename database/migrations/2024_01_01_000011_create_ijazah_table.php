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
        Schema::create('ijazah', function (Blueprint $table){
            $table->id();
            $table->string('institusi_pendidikan')->nullable();
            $table->enum('jenjang',['SMA','S1'])->nullable();
            $table->string('provinsi')->nullable();
            $table->string('kota')->nullable();
            $table->string('negara')->nullable();
            $table->string('fakultas')->nullable();
            $table->string('jurusan')->nullable();
            $table->string('ipk_nilai')->nullable();
            $table->year('tahun_lulus')->nullable();
            $table->string('file')->nullable();
            $table->timestamps();

            $table->unsignedBigInteger('calon_mahasiswa_id');
            $table->foreign('calon_mahasiswa_id')->references('id')->on('calon_mahasiswa')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ijazah');
    }
};
