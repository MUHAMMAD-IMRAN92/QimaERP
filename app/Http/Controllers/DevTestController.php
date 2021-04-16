<?php

namespace App\Http\Controllers;

use App\Region;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

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
        // $secret = '81aGk2WUJt4Sy3tGr9gQRtDTTsg0MDxpRI1kY0Vdv1';
        // abort_unless($request->secret === $secret, 403, 'Only dev is authorized for this route.');

        $govs = '2,3,4';

        $trnsactions = Transaction::where('transaction_id', explode(',', $govs)[0])->first();

        // $trnsactions = Transaction::where(function($query) use($govs) {
        //     foreach(explode(',', $govs) as $gov){
        //         $query->orWhere('batch_number', 'like', "$gov%");
        //     }
        // })->get()->toArray();
        
        return response()->json([
            'data' => $trnsactions
        ]);
    }
}
