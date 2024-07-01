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
        Schema::create('permisiuni_eveniments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('eveniment_id');
            $table->unsignedBigInteger('utilizator_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permisiuni_eveniments');
    }
};
