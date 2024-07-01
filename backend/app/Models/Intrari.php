<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Intrari extends Model
{
    use HasFactory;

    public $timestamps = true;

    public $fillable = [
        'eveniment_id',
    ];
}
