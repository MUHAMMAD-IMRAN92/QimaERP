<?php

namespace App\Http\Controllers;

use App\BatchNumber;
use App\Transaction;
use ProductNameSeeder;
use Illuminate\Support\Str;
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
        $secret = '81aGk2WUJt4Sy3tGr9gQRtDTTsg0MDxpRI1kY0Vdv1';
        abort_unless($request->secret === $secret, 403, 'Only dev is authorized for this route.');

        $transactions = Transaction::with('details.metas')->limit(3)->get();

        return response()->json([
            'transactions' => $transactions
        ]);
    }
}
