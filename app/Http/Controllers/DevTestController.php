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
        // $secret = '81aGk2WUJt4Sy3tGr9gQRtDTTsg0MDxpRI1kY0Vdv3';
        // abort_unless($request->secret === $secret, 403, 'Only dev is authorized for this route V3');

        // return response()->json([
        //     'message' => 'Hello Dev! How is your day?',
        //     'data' => []
        // ]);

        $batches = [
            'LHR-JRT-004',
            'LHR-JRT-009',
            'LHR-JRT-0012',
            'LHR-JRT-003',
        ];

        $max = 0;
        $maxBatch = null;

        for ($i = 1; $i < count($batches); $i++) {
            if (explode('-', $batches[$i])[2] > $max) {
                $max = explode('-', $batches[$i])[2];
                $maxBatch = $batches[$i];
            }
        }

        dd($max, $maxBatch);
    }
}
