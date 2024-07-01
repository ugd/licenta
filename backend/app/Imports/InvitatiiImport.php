<?php

namespace App\Imports;

use App\Models\Invitatii;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InvitatiiImport implements ToModel, WithHeadingRow, WithHeadings
{
    private $evenimentId;

    public function __construct($evenimentId = null)
    {
        $this->evenimentId = $evenimentId;
    }

    /**
     * @return Invitatii|null
     */
    public function model(array $row)
    {

        $code = strtoupper(Str::random(6));

        while (Invitatii::where('cod_invitatie', $code)->exists()) {
            $code = strtoupper(Str::random(6));
        }

        return new Invitatii([
            'nume' => $row['nume'] ?? $row['name'] ?? null,
            'prenume' => $row['prenume'] ?? $row['firstname'] ?? null,
            'adresa_email' => $row['email'] ?? $row['adresa_email'] ?? null,
            'telefon' => $row['telefon'] ?? $row['phone'] ?? null,
            'cod_invitatie' => $code,
            'stare_id' => 1,
            'invite_type_id' => 1,
            'eveniment_id' => $this->evenimentId,
        ]);
    }

    public function headings(): array
    {
        return [
            'nume',
            'prenume',
            'email',
            'telefon',
            'eveniment_id',
        ];
    }
}
