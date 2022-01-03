<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transaction;

class LotMixingController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('details')
            ->where('is_parent', 0)
            ->where('sent_to', 24)
            ->orderBy('transaction_id', 'desc')
            ->get();
        // $allTransactions = array();

        // foreach ($transactions as $key => $tran) {
        //     // if ($tran->sent_to == 13) {
        //     $childTransaction = array();
        //     $tran->makeHidden('log');
        //     $removeLocalId = explode("-", $tran->batch_number);
        //     if ($removeLocalId[3] == '000') {
        //         $FindParentTransactions = Transaction::where('is_parent', 0)->where('batch_number', $tran->batch_number)->first();
        //         if ($FindParentTransactions) {
        //             $childTransaction = Transaction::where('is_parent', $FindParentTransactions->transaction_id)->get();
        //         }
        //     }
        //     $transaction = ['transaction' => $tran, 'child_transactions' => $childTransaction];
        //     // array_push($allTransactions, $tran);
        //     array_push($allTransactions, $transaction);
        //     // }
        //     // array_push($allTransactions, $tran);
        // }
        // return $transactions;
        return view('admin.lotMixing.index', [
            'transactions' => $transactions,
        ]);
    }
}
