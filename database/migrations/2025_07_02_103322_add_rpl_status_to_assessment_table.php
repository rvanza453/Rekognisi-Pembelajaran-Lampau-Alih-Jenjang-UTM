<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('assessment', function (Blueprint $table) {
            // Status RPL: 'self-assessment', 'penilaian assessor', 'ditinjau admin', 'selesai', 'banding'
            $table->string('rpl_status')->default('self-assessment')->after('deadline');
        });
    }

    public function down()
    {
        Schema::table('assessment', function (Blueprint $table) {
            $table->dropColumn('rpl_status');
        });
    }
};
