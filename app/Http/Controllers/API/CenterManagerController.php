<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\TransactionDetail;
use App\TransactionLog;
use App\Transaction;
use App\LoginUser;
use App\Center;
use App\Farmer;
use App\User;

class CenterManagerController extends Controller {

    private $userId;
    private $user;

    public function __construct() {
        set_time_limit(0);
        $headers = getallheaders();
        $checksession = LoginUser::where('session_key', $headers['session_token'])->first();

        if ($checksession) {
            $user = User::where('user_id', $checksession->user_id)->with('roles')->first();
            if ($user) {
                $this->user = $user;
                $this->userId = $user->user_id;
            } else {
                return sendError('Session Expired', 404);
            }
        } else {
            return sendError('Session Expired', 404);
        }
    }

    function receivedTransactions(Request $request) {
        //::validation
        $validator = Validator::make($request->all(), [
                    'transactions' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            return sendError($errors, 400);
        }
        $sentTransaction = json_decode($request['transactions']);
        $alreadyReciviedCoffee = null;
        $reciviedCoffee = null;
        if (isset($sentTransaction->transaction) && $sentTransaction->transaction) {
            $alreadyExistTransaction = Transaction::where('reference_id', $sentTransaction->transaction->reference_id)->first();
            if ($alreadyExistTransaction) {
                $alreadyReciviedCoffee = $sentTransaction;
            } else {
                $transaction = Transaction::create([
                            'batch_number' => $sentTransaction->transaction->batch_number,
                            'is_parent' => $sentTransaction->transaction->is_parent,
                            'is_mixed' => $sentTransaction->transaction->is_mixed,
                            'created_by' => $sentTransaction->transaction->created_by,
                            'is_local' => FALSE,
                            'transaction_type' => $sentTransaction->transaction->transaction_type,
                            'local_code' => $sentTransaction->transaction->local_code,
                            'transaction_status' => 'received',
                            'reference_id' => $sentTransaction->transaction->reference_id,
                            'is_server_id' => 1,
                            'is_new' => $sentTransaction->transaction->is_new,
                            'sent_to' => $sentTransaction->transaction->sent_to,
                            'is_sent' => 1,
                            'sent_to' => 3,
                ]);

                $transactionLog = TransactionLog::create([
                            'transaction_id' => $transaction->transaction_id,
                            'action' => 'received',
                            'created_by' => $sentTransaction->transaction->created_by,
                            'entity_id' => $sentTransaction->transaction->center_id,
                            'local_created_at' => $sentTransaction->transaction->created_at,
                            'type' => 'center',
                ]);

                $transactionContainers = $sentTransaction->transactionDetails;
                foreach ($transactionContainers as $key => $transactionContainer) {
                    TransactionDetail::create([
                        'transaction_id' => $transaction->transaction_id,
                        'container_number' => $transactionContainer->container_number,
                        'created_by' => $transactionContainer->created_by,
                        'is_local' => FALSE,
                        'container_weight' => $transactionContainer->container_weight,
                        'weight_unit' => $transactionContainer->weight_unit,
                    ]);
                }
                $reciviedCoffee = $transaction->transaction_id;
            }
        }
        $currentlyReceivedCoffee = Transaction::where('transaction_id', $reciviedCoffee)->with('transactionDetail')->first();
        if ($alreadyReciviedCoffee) {
            $data = ['already_received_coffee' => $alreadyReciviedCoffee];

            return sendError('Coffee already received', 406, $data);
        }
        $transactionDeatil = $currentlyReceivedCoffee->transactionDetail;
        $currentlyReceivedCoffee->makeHidden('transactionDetail');
        $recCoffee = ['transaction' => $currentlyReceivedCoffee, 'transactionDetails' => $transactionDeatil];
        $data = ['received_coffee' => $recCoffee];

        return sendSuccess('Transactions received successfully', $data);
    }

    function centerManagerCoffee(Request $request) {
        $centerId = $this->user->table_id;
        $allTransactions = array();
        $transactions = Transaction::where('is_parent', 0)->where('transaction_status', 'sent')->doesntHave('isReference')->whereHas('transactionLog', function($q) use($centerId) {
                    $q->where('action', 'sent')->where('type', 'center')->where('entity_id', $centerId);
                })->with('childTransation.transactionDetail', 'transactionDetail')->orderBy('transaction_id', 'desc')->get();
        foreach ($transactions as $key => $transaction) {
            $transactionDetail = $transaction->transactionDetail;
            $transaction->makeHidden('transactionDetail');
            $transaction->makeHidden('childTransation');
            $data = ['transaction' => $transaction, 'transactionDetails' => $transactionDetail];
            array_push($allTransactions, $data);
        }
        return sendSuccess('Center manager received coffee', $allTransactions);
    }

    function centerManagerReceivedCoffee(Request $request) {
        $userId = $this->userId;
        $centerId = $this->user->table_id;
        $allTransactions = array();
        $transactions = Transaction::where('created_by', $userId)->where('transaction_status', 'received')->doesntHave('isReference')->whereHas('transactionLog', function($q) use($centerId) {
                    $q->where('action', 'received')->where('type', 'center')->where('entity_id', $centerId);
                })->with('childTransation.transactionDetail', 'transactionDetail')->orderBy('transaction_id', 'desc')->get();

        foreach ($transactions as $key => $transaction) {
            $transactionDetail = $transaction->transactionDetail;
            $transaction->makeHidden('transactionDetail');
            $transaction->makeHidden('childTransation');
            $data = ['transaction' => $transaction, 'transactionDetails' => $transactionDetail];
            array_push($allTransactions, $data);
        }
        return sendSuccess('Center manager received coffee', $allTransactions);
    }

}
