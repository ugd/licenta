<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bilete extends Model
{
    use HasFactory;

    protected $fillable = [
        'nume_prenume',
        'telefon',
        'email',
        'cod_bilet',
        'stare_id',
        'eveniment_id',
        'vendor_id',
    ];

    public function vendor_object()
    {
        return $this->belongsTo(VendoriBilete::class, 'vendor_id', 'id');
    }
}
