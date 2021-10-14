<?php

namespace App\Imports;

use App\Farmer;
use App\Village;
use Maatwebsite\Excel\Concerns\ToModel;

class FarmersImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {

        $vilalge =  Village::where('village_code', $row['2'])->first();
        if (!$vilalge) {
            Village::create([
                'village_code' => $row['2'],
                'village_title' => $row['2'],
                'created_by' => 4,
                'is_local' => 0,
                'village_title_ar' => $row['2'],
            ]);
        }

        return new Farmer([
            'farmer_code'     => $row['0'],
            'farmer_name'    => $row['1'],
            'village_code'    => $row['2'],
            // 'picture_id'    => '123456789',
            // 'idcard_picture_id'    => '123456789',
            'is_status'    => $row['5'],
            'price_per_kg'    => $row['6'],
            'created_by'    => $row['7'],
            'is_local'    => $row['8'],
            'season_no'    => $row['9'],
            'local_code'    => $row['10'],
            'farmer_nicn'    => $row['11'],
            'center_id'    => $row['12'],
        ]);
    }
}
