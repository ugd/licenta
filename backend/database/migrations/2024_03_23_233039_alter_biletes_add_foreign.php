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
        Schema::table('biletes', function (Blueprint $table) {
            $table->foreign('eveniment_id')->references('id')->on('evenimentes');
            $table->foreign('stare_id')->references('id')->on('staris');
            $table->foreign('vendor_id')->references('id')->on('vendori_biletes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biletes', function (Blueprint $table) {
            $table->dropForeign(['eveniment_id']);
            $table->dropForeign(['stare_id']);
            $table->dropForeign(['vendor_id']);
        });
    }
};
