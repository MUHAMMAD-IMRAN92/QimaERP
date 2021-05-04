<?php

namespace App\Http\Controllers;

use App\Transaction;
use Illuminate\Http\Request;

class ExportMixingController extends Controller
{
    public function get()
    {
        $transactions = Transaction::with('details')->where('sent_to', 24)
            ->orderBy('transaction_id', 'desc')
            ->get();

        return view('admin.export.mixing.index' , [
            'transactions' => $transactions ,
        ]);
    }
    public function post(Request $request)
    {
        return $request->all();
    }
}
