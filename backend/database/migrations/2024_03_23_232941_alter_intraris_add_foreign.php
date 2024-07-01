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
        Schema::table('intraris', function (Blueprint $table) {
            $table->foreign('eveniment_id')->references('id')->on('evenimentes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('intraris', function (Blueprint $table) {
            $table->dropForeign(['eveniment_id']);
        });
    }
};
