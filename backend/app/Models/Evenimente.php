<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evenimente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nume_eveniment',
        'locatie',
        'data_inceput',
        'data_sfarsit',
        'location_lat',
        'location_long',
        'invitation_background',
        'apple_wallet_background',
        'apple_wallet_thumbnail',
        'apple_wallet_logo',
        'apple_wallet_icon',
        'main_act',
        'event_day_only',
        'performers',
        'event_edition',
        'event_location',
        'venue_name',
        'venue_entrance',
        'venue_room',
        'uuid',
    ];

    protected $casts = [
        'performers' => 'array',
    ];

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'permisiuni_eveniments',
            'eveniment_id',
            'utilizator_id'
        );
    }
}
