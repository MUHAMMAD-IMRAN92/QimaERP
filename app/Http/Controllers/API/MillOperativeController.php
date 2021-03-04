<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Transaction;
use Illuminate\Http\Request;

class MillOperativeController extends Controller
{
    public function coffee()
    {
        $transactions = Transaction::where('is_parent', 0)
            ->where('ready_to_milled', true)
            ->with(['transactionDetail', 'childTransation', 'meta', 'log'])
            ->get();

        return response()->json([
            'transactions' => $transactions
        ]);
    }
}
