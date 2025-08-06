<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use function Laravel\Prompts\table;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admin', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('no_hp')->nullable();
            $table->string('alamat')->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('jurusan_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('jurusan_id')->references('id')->on('jurusan')->onDelete('cascade');

        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('admin');
    }
};
