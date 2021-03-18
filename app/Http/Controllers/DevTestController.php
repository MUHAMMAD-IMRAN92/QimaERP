<?php

namespace App\Http\Controllers;

use App\Farmer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use ProductNameSeeder;

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

        $productSeeder = new ProductNameSeeder();

        $ran = $productSeeder->run();

        return [
            'message' => 'This is for alee',
            'ran' => $ran
        ];
    }
}
