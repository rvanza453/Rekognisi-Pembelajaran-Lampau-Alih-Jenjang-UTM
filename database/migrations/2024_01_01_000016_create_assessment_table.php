<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('assessment', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('calon_mahasiswa_id');
            $table->unsignedBigInteger('jurusan_id');
            $table->unsignedBigInteger('assessor_id_1');
            $table->unsignedBigInteger('assessor_id_2');
            $table->unsignedBigInteger('assessor_id_3');
            $table->foreign('calon_mahasiswa_id')->references('id')->on('calon_mahasiswa')->onDelete('cascade');
            $table->foreign('jurusan_id')->references('id')->on('jurusan')->onDelete('cascade');
            $table->foreign('assessor_id_1')->references('id')->on('assessor')->onDelete('cascade');
            $table->foreign('assessor_id_2')->references('id')->on('assessor')->onDelete('cascade');
            $table->foreign('assessor_id_3')->references('id')->on('assessor')->onDelete('cascade');

            $table->timestamps();
        }); 
    }

    
    public function down(): void
    {
        Schema::dropIfExists('assessment');
    }
};
