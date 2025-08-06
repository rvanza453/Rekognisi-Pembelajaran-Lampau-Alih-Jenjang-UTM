<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up()
    {
        Schema::create('matkul_score', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('matkul_id');
            $table->unsignedBigInteger('assessor_id');
            $table->unsignedBigInteger('calon_mahasiswa_id');
            $table->string('status', 20); 
            $table->integer('nilai')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->integer('nilai_akhir')->nullable();
            $table->boolean('is_banding')->default(false);
            $table->text('banding_keterangan')->nullable();
            $table->enum('banding_status', ['pending', 'ditolak', 'diterima'])->default('pending');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('matkul_id')->references('id')->on('matkul')->onDelete('cascade');
            $table->foreign('assessor_id')->references('id')->on('assessor')->onDelete('cascade');
            $table->foreign('calon_mahasiswa_id')->references('id')->on('calon_mahasiswa')->onDelete('cascade');

        });
    }


    public function down()
    {
        Schema::dropIfExists('matkul_score');
    }
};