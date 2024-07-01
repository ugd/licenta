<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stari extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = ['id', 'nume_stare', 'created_at', 'updated_at'];
}
