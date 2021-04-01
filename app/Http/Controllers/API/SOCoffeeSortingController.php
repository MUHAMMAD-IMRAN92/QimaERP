<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Transaction;
use Illuminate\Http\Request;

class SOCoffeeSortingController extends Controller
{
    public function getCoffee()
    {
       $transactions = Transaction::where('sent_to' , 21)->with('details.metas')->get();
       return $transactions;
    }

    public function sendCoffee(Request $request)
    {
        return response()->json([
            'request' => $request->all()
        ]);
    }
}
