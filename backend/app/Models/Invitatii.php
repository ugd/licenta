<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitatii extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'nume',
        'prenume',
        'adresa_email',
        'telefon',
        'eveniment_id',
        'invite_type_id',
        'cod_invitatie',
        'stare_id',
    ];

    public function eveniment()
    {
        return $this->belongsTo(Evenimente::class, 'eveniment_id', 'id');
    }

    public function inviteType()
    {
        return $this->belongsTo(InviteType::class, 'invite_type_id', 'id');
    }
}
