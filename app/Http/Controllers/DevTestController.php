<?php

namespace App\Http\Controllers;

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
        $secret = '81aGk2WUJt4Sy3tGr9gQRtDTTsg0MDxpRI1kY0Vdv1';
        abort_unless($request->secret === $secret, 403, 'Only dev is authorized for this route.');

        $transactions = Transaction::with('details.metas')->limit(3)->get();

        return response()->json([
            'transactions' => $transactions
        ]);
    }
}
