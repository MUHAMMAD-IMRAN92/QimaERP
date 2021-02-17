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

        $columns = [
            'farmer_name',
            'village_code',
            'picture_id',
            'idcard_picture_id',
            'is_status',
            'created_by',
            'is_local',
            'local_code',
            'farmer_nicn',
            'center_id',
            'deleted_at'
        ];

        $farmers = Farmer::where('farmer_id', '<=', 93)->get($columns);

        $farmers->each(function ($farmer) {
            $max_id = Farmer::max('farmer_id');
            $farmer->farmer_code = $farmer->village_code . '-' . sprintf("%03d", $max_id + 1);

            Farmer::create($farmer->toArray());
        });

        return [
            'deleted' => Farmer::where('farmer_id', '<=', 93)->forceDelete(),
            'count' => $farmers->count(),
            'max_id' => Farmer::max('farmer_id'),
            'farmers' => $farmers
        ];
    }
}
