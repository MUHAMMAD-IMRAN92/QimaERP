<?php

namespace App\Http\Controllers;

use stdClass;
use App\Region;
use App\Village;
use App\BatchNumber;
use App\Transaction;
use App\TransactionDetail;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DevTestController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        // $secret = '81aGk2WUJt4Sy3tGr9gQRtDTTsg0MDxpRI1kY0Vdv4';
        // abort_unless($request->secret === $secret, 403, 'Only dev is authorized for this route V3');

        // return response()->json([
        //     'msg' => 'Hello Dev, how is your day?',
        //     'live_test' => true
        // ]);

        // $file =    Storage::disk('s3')->put('file.txt', 'Imran');

        // return $file;
        // $file = url('public/allFarmers.xlsx');
        $villages = Village::all();
        foreach ($villages as $village) {
            $regionFronVillage = Str::beforeLast($village->village_code, '-');
            $region = Region::where('region_code', $regionFronVillage)->first();
            if (!$region) {
                Region::create([
                    'region_code' =>  $regionFronVillage,
                    'region_title' => $regionFronVillage,
                    'created_by' => 4,
                    'center_id' => 1
                ]);
            }
        }
    }
}
