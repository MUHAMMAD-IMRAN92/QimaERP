<?php

namespace App\Http\Controllers;

use App\Farmer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $secret = '81aGk2WUJt4Sy3tGr9gQRtDTTsg0MDxpRI1kY0Vd';
        abort_unless($request->secret === $secret, 403, 'Only dev is authorized for this route.');

        $village_code = $request->village_code;

        $farmers = Farmer::where('village_code', $village_code)->get();

        $farmers->each(function ($farmer) {
            $farmer->farmer_code = $farmer->village_code . '-' . sprintf("%03d", $farmer->farmer_id);

            $farmer->save();
        });

        return Farmer::where('village_code', $village_code)->get();
    }
}
