<?php

namespace App\Http\Controllers;

use App\TransactionDetail;
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
        // $secret = '81aGk2WUJt4Sy3tGr9gQRtDTTsg0MDxpRI1kY0Vdv3';
        // abort_unless($request->secret === $secret, 403, 'Only dev is authorized for this route V3');

        $detail = TransactionDetail::first();

        return [
            'original' => $detail,
            'replicate' => $detail->replicate()
        ];
    }
}
