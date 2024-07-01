<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InviteType extends Model
{
    use HasFactory;

    protected $fillable = ['invite_type'];

    public $timestamps = true;
}
