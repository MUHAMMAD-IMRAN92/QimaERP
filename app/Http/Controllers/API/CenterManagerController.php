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
        $sentTransactions = json_decode($request['transactions']);
        $alreadyReciviedCoffee = array();
        $reciviedCoffee = array();
        foreach ($sentTransactions as $key => $sentTransaction) {
            $transationRef = array();
            foreach ($sentTransaction->transactionDetails as $key => $transactionDetailsvalue) {
                if (!in_array($transactionDetailsvalue->reference_id, $transationRef)) {
                    array_push($transationRef, $transactionDetailsvalue->reference_id);
                }
            }
            if (isset($sentTransaction->transaction) && $sentTransaction->transaction) {
                $transaction = Transaction::create([
                            'batch_number' => $sentTransaction->transaction->batch_number,
                            'is_parent' => $sentTransaction->transaction->is_parent,
                            'is_mixed' => $sentTransaction->transaction->is_mixed,
                            'created_by' => $sentTransaction->transaction->created_by,
                            'is_local' => FALSE,
                            'transaction_type' => $sentTransaction->transaction->transaction_type,
                            'local_code' => $sentTransaction->transaction->local_code,
                            'transaction_status' => 'received',
                            'reference_id' => implode(",", $transationRef),
                            'is_server_id' => 1,
                            'is_new' => 0,
                            'sent_to' => 4,
                            'is_sent' => 1,
                ]);
                $transactionLog = TransactionLog::create([
                            'transaction_id' => $transaction->transaction_id,
                            'action' => 'received',
                            'created_by' => $sentTransaction->transaction->created_by,
                            'entity_id' => $sentTransaction->transaction->center_id,
                            'local_created_at' => date("Y-m-d H:i:s", strtotime($sentTransaction->transaction->created_at)),
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
                        'center_id' => $transactionContainer->center_id,
                    ]);

                    TransactionDetail::where('transaction_id', $transactionContainer->reference_id)->where('container_number', $transactionContainer->container_number)->update(['container_status' => 1]);
                }
                array_push($reciviedCoffee, $transaction->transaction_id);
            }
        }
        $currentlyReceivedCoffees = Transaction::whereIn('transaction_id', $reciviedCoffee)->with('transactionDetail')->get();
        $dataArray = array();
        foreach ($currentlyReceivedCoffees as $key => $currentlyReceivedCoffee) {
            $transactionDeatil = $currentlyReceivedCoffee->transactionDetail;
            $currentlyReceivedCoffee->makeHidden('transactionDetail');
            $currentlyReceivedCoffee->already_received = FALSE;
            $recCoffee = ['transaction' => $currentlyReceivedCoffee, 'transactionDetails' => $transactionDeatil];
            array_push($dataArray, $recCoffee);
        }
        $data = array_merge($dataArray, $alreadyReciviedCoffee);
        if (count($alreadyReciviedCoffee) > 0) {
            return sendSuccess('Some transactions have already been recivied.', $data);
        }
        return sendSuccess('Transactions received successfully', $data);
    }

    function centerManagerCoffee(Request $request) {
        $centerId = $this->user->table_id;
        $allTransactions = array();
        $transactions = Transaction::where('is_parent', 0)->where('transaction_status', 'sent')->whereHas('transactionLog', function($q) use($centerId) {
                    $q->where('action', 'sent')->where('type', 'center')->where('entity_id', $centerId);
                })->whereHas('transactionDetail', function($q) use($centerId) {
                    $q->where('container_status', 0);
                }, '>', 0)->with(['transactionDetail' => function($query) {
                        $query->where('container_status', 0);
                    }])->orderBy('transaction_id', 'desc')->get();
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
        $transactions = Transaction::where('created_by', $userId)->where('transaction_status', 'received')->whereHas('transactionLog', function($q) use($centerId) {
                    $q->where('action', 'received')->where('type', 'center')->where('entity_id', $centerId);
                })->with('transactionDetail')->orderBy('transaction_id', 'desc')->get();

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
