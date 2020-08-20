<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Farmer;
use App\BatchNumber;
use App\Transaction;
use App\TransactionDetail;
use App\TransactionLog;

class CenterManagerController extends Controller {

    function receivedTransactions(Request $request) {
        //::validation
        $validator = Validator::make($request->all(), [
                    'transactions' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            return sendError($errors, 400);
        }
        $sentTransactions = json_decode($request['transactions']);
        foreach ($sentTransactions as $key => $sentTransaction) {
            $alreadyExistTransaction = Transaction::where('id', $sentTransaction->server_transaction_id)->first();

            $transaction = Transaction::create([
                        'batch_number' => $alreadyExistTransaction->batch_number,
                        'is_parent' => $sentTransaction->is_parent,
                        'is_mixed' => $sentTransaction->is_mixed,
                        'created_by' => $sentTransaction->created_by,
                        'is_local' => FALSE,
                        'transaction_type' => $sentTransaction->transaction_type,
                        'local_code' => $sentTransaction->local_code,
                        'transaction_status' => $sentTransaction->transaction_status,
                        'reference_id' => $alreadyExistTransaction->server_transaction_id,
            ]);

            $transactionLog = TransactionLog::create([
                        'transaction_id' => $transaction->server_transaction_id,
                        'action' => $sentTransaction->transaction_log->action,
                        'created_by' => $sentTransaction->transaction_log->created_by,
                        'sent_to' => $sentTransaction->transaction_log->sent_to,
                        'local_created_at' => $sentTransaction->transaction_log->local_created_at,
            ]);

            $transactionContainers = $sentTransaction->transactions_detail;
            foreach ($transactionContainers as $key => $transactionContainer) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->server_transaction_id,
                    'container_number' => $transactionContainer->container_number,
                    'created_by' => $sentTransaction->transaction_log->created_by,
                    'is_local' => FALSE,
                    'weight' => $transactionContainer->container_weight,
                ]);
            }
        }
        return sendSuccess('Transactions received successfully', []);
    }

}
