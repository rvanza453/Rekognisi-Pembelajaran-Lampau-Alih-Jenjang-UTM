<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cpmk', function (Blueprint $table) {
            $table->id();
            $table->string('penjelasan');
            $table->unsignedBigInteger('matkul_id');
            
            // Add foreign key with cascade
            $table->foreign('matkul_id')
                  ->references('id')
                  ->on('matkul')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('cpmk');
        Schema::enableForeignKeyConstraints();
    }
};