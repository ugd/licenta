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
        Schema::table('invitatiis', function (Blueprint $table) {
            $table->foreign('stare_id')->references('id')->on('staris');
            $table->foreign('eveniment_id')->references('id')->on('evenimentes');
            $table->foreign('invite_type_id')->references('id')->on('invite_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invitatiis', function (Blueprint $table) {
            $table->dropForeign(['stare_id']);
            $table->dropForeign(['eveniment_id']);
            $table->dropForeign(['invite_type_id']);
        });
    }
};
