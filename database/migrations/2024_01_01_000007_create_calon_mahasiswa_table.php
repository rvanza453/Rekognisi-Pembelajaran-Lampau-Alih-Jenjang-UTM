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
        Schema::create('calon_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('no_whatsapp')->nullable();
            $table->enum('kelamin',['laki-laki','perempuan'])->nullable();
            $table->string('alamat')->nullable();
            $table->string('kota')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('kode_pos')->nullable();
            $table->string('foto_profile')->nullable();
            $table->timestamps();

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('jurusan_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('jurusan_id')->references('id')->on('jurusan')->onDelete('cascade');
            
            
        });
    }

    
    public function down(): void
    {
        schema::dropIfExists('calon_mahasiswa');
    }
};
