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
        Schema::create('biletes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('eveniment_id');
            $table->unsignedBigInteger('stare_id');
            $table->string('nume_prenume');
            $table->string('email')->nullable();
            $table->string('telefon')->nullable();
            $table->string('cod_bilet')->unique('cod_bilet');
            $table->unsignedBigInteger('vendor_id')->default(7);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biletes');
    }
};
