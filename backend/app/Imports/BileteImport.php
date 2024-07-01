<?php

namespace App\Imports;

use App\Models\Bilete;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithUpserts;

class BileteImport implements ToModel, WithHeadingRow, WithHeadings, WithUpserts
{
    private $evenimentId;
    private $vendorId;

    public function __construct($evenimentId = null, $vendorId = null)
    {
        $this->evenimentId = $evenimentId;
        $this->vendorId = $vendorId;
    }

    /**
     * @return Bilete|null
     */
    public function model(array $row)
    {

        $data = [
            'nume_prenume' => $row['nume_client'] ?? null,
            'email' => $row['email'] ?? null,
            'telefon' => $row['telefon'] ?? null,
            'cod_bilet' => $row['cod_bare'] ?? null,
            'stare_id' => 2,
            'eveniment_id' => $this->evenimentId,
            'vendor_id' => $this->vendorId,
        ];

        return new Bilete($data);
    }

    public function headings(): array
    {
        return [
            'nr_crt',
            'id_comanda',
            'data_comanda',
            'nume_client',
            'tarif',
            'loc',
            'pret_bilet',
            'cod_bare',
            'serie',
            'validat',
            'status_cmd',
            'status_blt',
            'telefon',
            'email'
        ];
    }

    public function uniqueBy()
    {
        return 'cod_bilet';
    }

    public function upsert($row, array $data)
    {
        return Bilete::updateOrCreate(
            ['cod_bilet' => $row['cod_bare']],
            $data
        );
    }
}
