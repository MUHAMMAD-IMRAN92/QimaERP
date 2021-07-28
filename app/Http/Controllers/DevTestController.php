<?php

namespace App\Http\Controllers;

use stdClass;
use App\BatchNumber;
use App\Transaction;
use App\TransactionDetail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

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


        $sum = 0;
        $details = TransactionDetail::whereIn('transaction_id', [2154, 2159, 2160, 2161, 2164])->get();
        foreach ($details as $detail) {
            $sum = $detail->constainer_weight;
        }
        return $sum;
    }
}
