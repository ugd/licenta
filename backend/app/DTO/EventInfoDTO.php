<?php

namespace App\DTO;

class EventInfoDTO
{
    public float $lat;

    public float $long;

    public string $guest_name;

    public string $event_name;

    public string $event_day_only;

    public string $event_date;

    public string $event_end_date;

    public string $venue_name;

    public string $venue_entrance;

    public string $venue_room;

    public string $event_location;

    public string $event_edition;

    public string $ticket_number;

    public string $invite_type;

    public array $performers;

    public string $main_act;

    public string $apple_wallet_background;

    public string $apple_wallet_thumbnail;

    public string $apple_wallet_logo;

    public string $apple_wallet_icon;

    public ?string $uuid;

    public function __construct(array $data)
    {
        $this->lat = $data['lat'];
        $this->long = $data['long'];
        $this->guest_name = $data['guest_name'];
        $this->event_name = $data['event_name'];
        $this->event_day_only = $data['event_day_only'];
        $this->event_date = $data['event_date'];
        $this->event_end_date = $data['event_end_date'];
        $this->venue_name = $data['venue_name'];
        $this->venue_entrance = $data['venue_entrance'];
        $this->venue_room = $data['venue_room'];
        $this->event_location = $data['event_location'];
        $this->event_edition = $data['event_edition'];
        $this->ticket_number = $data['ticket_number'];
        $this->invite_type = $data['invite_type'];
        $this->performers = $data['performers'] ?? [];
        $this->main_act = $data['main_act'];
        $this->apple_wallet_background = $data['apple_wallet_background'];
        $this->apple_wallet_thumbnail = $data['apple_wallet_thumbnail'];
        $this->apple_wallet_logo = $data['apple_wallet_logo'];
        $this->apple_wallet_icon = $data['apple_wallet_icon'];
        $this->uuid = $data['uuid'] ?? null;
    }
}
