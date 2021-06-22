<?php

namespace App\Http\Controllers;

use App\Transaction;
use Illuminate\Http\Request;

class UkWareHouseController extends Controller
{
    public function prices()
    {
        $transactionsWOS = Transaction::with('details')->where('is_parent', 0)->where('sent_to', 43)->get();

        $transactionsWS = Transaction::with('details')->where('is_parent', 0)->where('sent_to', 44)->get();

        return view('admin.uk_warehouse.set_prices', [
            'transactionWOS' => $transactionsWOS,
            'transactionsWS' => $transactionsWS
        ]);
    }
}
