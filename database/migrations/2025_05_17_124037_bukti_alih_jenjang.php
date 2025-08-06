<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('bukti_alih_jenjang', function (Blueprint $table) {
            $table->id('nomor_dokumen');
            $table->string('jenis_dokumen');
            $table->string('file');
            $table->unsignedBigInteger('calon_mahasiswa_id');
            $table->foreign('calon_mahasiswa_id')->references('id')->on('calon_mahasiswa')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bukti_alih_jenjang');
    }
};
