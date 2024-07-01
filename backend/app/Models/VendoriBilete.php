<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendoriBilete extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'nume_vendor',
    ];

    public function bilete()
    {
        return $this->hasMany(Bilete::class);
    }
}
