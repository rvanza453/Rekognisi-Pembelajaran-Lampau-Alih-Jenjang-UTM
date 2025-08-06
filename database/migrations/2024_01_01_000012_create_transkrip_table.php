<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transkrip', function (Blueprint $table) {
            $table->id();
            $table->string('file');
            $table->unsignedBigInteger('calon_mahasiswa_id');
            $table->json('mata_kuliah_transkrip');
            $table->foreign('calon_mahasiswa_id')->references('id')->on('calon_mahasiswa')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transkrip');
    }
}; 