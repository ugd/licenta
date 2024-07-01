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
            $table->timestamp('scan_time')->nullable();
        });
        Schema::table('intraris', function (Blueprint $table) {
            $table->timestamp('scan_time')->nullable();
        });
        Schema::table('biletes', function (Blueprint $table) {
            $table->timestamp('scan_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invitatiis', function (Blueprint $table) {
            $table->dropColumn('scan_time');
        });
        Schema::table('intraris', function (Blueprint $table) {
            $table->dropColumn('scan_time');
        });
        Schema::table('biletes', function (Blueprint $table) {
            $table->dropColumn('scan_time');
        });
    }
};
