<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Transaction;
use Illuminate\Http\Request;

class YOLocalMarketController extends Controller
{
    public function getCoffee()
    {
        $transactions = Transaction::where('sent_to' , 20)->with('details.metas')->get();
        return $transactions;
    }

    public function sendCoffee(Request $request)
    {
        return response()->json([
            'request' => $request->all()
        ]);
    }
}
