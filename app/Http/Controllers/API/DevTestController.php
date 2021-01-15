<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Transaction;
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
        $secret = 'base64:7owkmMQygcmYBMFNGmdMW2wcnAyqbeFikRhBt2/lXbc=';
        abort_unless($request->secret === $secret, 401, 'Not Authorized for this request');

        $trancsactions = Transaction::orderBy('updated_at', 'desc')->get();

        return response()->json([
            'transactions' => $trancsactions
        ]);
    }
}
