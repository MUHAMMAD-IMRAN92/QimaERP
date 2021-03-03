<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Transaction;
use Illuminate\Http\Request;

class MillOperativeController extends Controller
{
    public function coffee()
    {
        $transactions = Transaction::where('ready_to_milled', true)
            ->where('sent_to', 14)
            ->with(['transactionDetail', 'childTransation', 'meta', 'log'])
            ->get();

        return response()->json([
            'transactions' => $transactions
        ]);
    }
}
