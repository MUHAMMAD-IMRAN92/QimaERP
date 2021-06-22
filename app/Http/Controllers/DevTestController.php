<?php

namespace App\Http\Controllers;

use App\BatchNumber;
use App\TransactionDetail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use stdClass;

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
        $secret = '81aGk2WUJt4Sy3tGr9gQRtDTTsg0MDxpRI1kY0Vdv4';
        abort_unless($request->secret === $secret, 403, 'Only dev is authorized for this route V3');

        return response()->json([
            'msg' => 'Hello Dev, how is your day?',
            'live_test' => true
        ]);
    }
}
