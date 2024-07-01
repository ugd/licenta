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
        Schema::create('invitatiis', function (Blueprint $table) {
            $table->id();
            $table->string('adresa_email');
            $table->string('nume');
            $table->string('prenume');
            $table->string('telefon')->nullable();
            $table->string('cod_invitatie');
            $table->bigInteger('stare_id')->unsigned()->default(1);
            $table->bigInteger('eveniment_id')->unsigned();
            $table->bigInteger('invite_type_id')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invitatiis');
    }
};
