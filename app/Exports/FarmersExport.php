<?php

namespace App\Exports;

use App\Farmer;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
class FarmersExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    use Exportable;
    public function collection()
    {
        return Farmer::all(['farmer_name', 'farmer_code', 'village_code']);
    }
    public function headings(): array
    {
        return [
            'Farmer Name',
            'Farmer Code',
            'Farmer Village Code',
            ];
    }
}
