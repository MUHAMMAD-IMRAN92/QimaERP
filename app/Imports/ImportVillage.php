<?php

namespace App\Imports;

use App\Village;
use Maatwebsite\Excel\Concerns\ToModel;

class ImportVillage implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Village([
            'village_code' => $row[4],
            'village_title' => $row[0],
            'village_title_ar' => $row[1]
        ]);
    }
}
