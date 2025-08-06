<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matkul', function (Blueprint $table) {
            $table->id();
            $table->string('nama_matkul');
            $table->text('sinonim')->nullable();
            $table->unsignedBigInteger('jurusan_id');
            
            // Add foreign key with cascade
            $table->foreign('jurusan_id')
                  ->references('id')
                  ->on('jurusan')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('matkul');
        Schema::enableForeignKeyConstraints();
    }
}; 