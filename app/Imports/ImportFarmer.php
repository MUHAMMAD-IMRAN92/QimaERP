<?php

namespace App\Imports;

use App\Farmer;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;

class ImportFarmer implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Farmer([
            'farmer_code' => $row[7],
            'farmer_name' => $row[0],
            'village_code' => $row[5],
            'picture_id' => null,
            'idcard_picture_id' => null,
            'farmer_nicn' => '000',
            'local_code' => $row[7] . '_' . Auth::user()->user_id . '-F-' . strtotime("now"),
            'is_local' => 0,
            'is_status' => 1,
            'price_per_kg' => 0,
            'created_by' => Auth::user()->user_id,
            'ph_no' => 00000,
            'reward' => 0,
            'cup_profile' => 0,
            'cupping_score' => 0,
            'farmer_info' => "",
            'no_of_trees' => 0,
            'house_hold_size' => 0,
            'farm_size' => 0,
            'altitude' => 0,
        ]);
    }
}
