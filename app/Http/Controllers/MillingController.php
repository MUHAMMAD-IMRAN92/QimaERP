<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transaction;

class MillingController extends Controller {

    public function index() {
    
        $allTransactions = array();

        $transactions = Transaction::where('is_parent', 0)->whereHas('log', function($q) {
                    $q->where('action', 'sent')->whereIn('type', ['sent_to_yemen']);
                })->whereHas('transactionDetail', function($q) {
                    $q->where('container_status', 0);
                }, '>', 0)->with(['transactionDetail' => function($query) {
                        $query->where('container_status', 0);
                    }])->orderBy('transaction_id', 'desc')->get();

        foreach ($transactions as $key => $transaction) {
            $childTransaction = array();
            $transactionDetail = $transaction->transactionDetail;
            $transaction->makeHidden('transactionDetail');
            $transaction->makeHidden('log');
            $removeLocalId = explode("-", $transaction->batch_number);
            if ($removeLocalId[3] == '000') {
                $FindParentTransactions = Transaction::where('is_parent', 0)->where('batch_number', $transaction->batch_number)->first();
                if ($FindParentTransactions) {
                    $childTransaction = Transaction::where('is_parent', $FindParentTransactions->transaction_id)->get();
                }
            }
            $data = ['transaction' => $transaction, 'transactionDetails' => $transactionDetail, 'child_transactions' => $childTransaction];
            array_push($allTransactions, $data);
        }
                return sendSuccess(Config("statuscodes.en.success_messages.RECV_COFFEE_MESSAGE"), $allTransactions);

        var_dump($allTransactions);exit;
        return view('admin.milling.index');
    }

}
