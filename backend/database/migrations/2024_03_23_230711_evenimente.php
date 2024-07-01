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
        Schema::create('evenimentes', function (Blueprint $table) {
            $table->id();
            $table->string('nume_eveniment');
            $table->string('uuid')->nullable();
            $table->string('locatie');
            $table->string('data_inceput');
            $table->string('data_sfarsit');
            $table->string('location_lat')->nullable();
            $table->string('location_long')->nullable();
            $table->string('invitation_background')->nullable();
            $table->string('apple_wallet_background')->nullable();
            $table->string('apple_wallet_thumbnail')->nullable();
            $table->string('apple_wallet_logo')->nullable();
            $table->string('apple_wallet_icon')->nullable();
            $table->string('main_act')->nullable();
            $table->string('event_day_only')->nullable();
            $table->json('performers')->nullable();
            $table->string('event_edition')->nullable();
            $table->string('event_location')->nullable();
            $table->string('venue_entrance')->nullable();
            $table->string('venue_room')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evenimentes');
    }
};
