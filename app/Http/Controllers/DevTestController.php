<?php

namespace App\Http\Controllers;

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
        // $secret = '81aGk2WUJt4Sy3tGr9gQRtDTTsg0MDxpRI1kY0Vdv1';
        // abort_unless($request->secret === $secret, 403, 'Only dev is authorized for this route.');

        $localCodes = '1_1-T-1618215338641,9_10-T-1618295238485,11_10-T-1618295274642';

        return explode(',', $localCodes);

        return response()->json([
            'message' => 'Hello Dev',
            'data' => []
        ]);
    }
}
