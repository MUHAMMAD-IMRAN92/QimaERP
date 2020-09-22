<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use App\TransactionDetail;
use App\TransactionLog;
use App\Transaction;
use App\LoginUser;
use App\User;

class ProcessingManagerController extends Controller {

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

    function getProcessingManager(Request $request) {
        $userId = $this->userId;
        $centerId = $this->user->table_id;
        $allTransactions = array();
        $transactions = Transaction::where('is_parent', 0)->where('transaction_status', 'received')->whereHas('log', function($q) use($centerId) {
                    $q->where('action', 'received')->where('type', 'center')->where('entity_id', $centerId);
                })->whereHas('transactionDetail', function($q) use($centerId) {
                    $q->where('container_status', 0);
                }, '>', 0)->with(['transactionDetail' => function($query) {
                        $query->where('container_status', 0);
                    }])->orderBy('transaction_id', 'desc')->get();

        foreach ($transactions as $key => $transaction) {
            $transactionDetail = $transaction->transactionDetail;
            $transaction->center_id = $transaction->log->entity_id;
            $transaction->is_sent = 0;
            $transaction->makeHidden('transactionDetail');
            $transaction->makeHidden('log');
            $data = ['transaction' => $transaction, 'transactionDetails' => $transactionDetail];
            array_push($allTransactions, $data);
        }
        return sendSuccess('Processor manager received coffee', $allTransactions);
    }

    function fetchProcessorRole(Request $request) {
        $userId = $this->userId;
        $centerId = $this->user->table_id;
        $role = Role::whereIn('name', ['Special Processing', 'Coffee Drying'])->select('name')->get();
        return sendSuccess('Processor manager coffee', $role);
    }

    function sentToSpecialProcessingAndCoffeeDrying(Request $request) {
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
                if ($sentTransaction->transaction->sent_to == 5) {
                    $type = 'special_processing';
                } else {
                    $type = 'coffee_drying';
                }
                $transaction = Transaction::create([
                            'batch_number' => $sentTransaction->transaction->batch_number,
                            'is_parent' => $sentTransaction->transaction->is_parent,
                            'is_mixed' => $sentTransaction->transaction->is_mixed,
                            'created_by' => $sentTransaction->transaction->created_by,
                            'is_local' => FALSE,
                            'transaction_type' => 2,
                            'local_code' => $sentTransaction->transaction->local_code,
                            'transaction_status' => 'sent',
                            'reference_id' => implode(",", $transationRef),
                            'is_server_id' => 1,
                            'is_new' => 0,
                            'sent_to' => $sentTransaction->transaction->sent_to,
                            'is_sent' => 1,
                ]);
                $transactionLog = TransactionLog::create([
                            'transaction_id' => $transaction->transaction_id,
                            'action' => 'sent',
                            'created_by' => $sentTransaction->transaction->created_by,
                            'entity_id' => $sentTransaction->transaction->center_id,
                            'local_created_at' => date("Y-m-d H:i:s", strtotime($sentTransaction->transaction->created_at)),
                            'type' => $type,
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
                        'reference_id' => $transactionContainer->reference_id,
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
        return sendSuccess('Transactions sent successfully', $data);
    }

    function getSendSpecialProcessingAndDryingCoffee(Request $request) {
        $userId = $this->userId;
        $centerId = $this->user->table_id;
        $allTransactions = array();
        $transactions = Transaction::where('created_by', $userId)->where('transaction_status', 'sent')->whereHas('log', function($q) use($centerId) {
                    $q->where('action', 'sent')->whereIn('type', ['special_processing', 'coffee_drying'])->where('entity_id', $centerId);
                })->whereHas('transactionDetail', function($q) use($centerId) {
                    $q->where('container_status', 1);
                }, '>', 0)->with(['transactionDetail' => function($query) {
                        $query->where('container_status', 1);
                    }])->orderBy('transaction_id', 'desc')->get();

        foreach ($transactions as $key => $transaction) {
            $transactionDetail = $transaction->transactionDetail;
            $transaction->center_id = $transaction->log->entity_id;
            $transaction->is_sent = 0;
            $transaction->makeHidden('transactionDetail');
            $transaction->makeHidden('log');
            $data = ['transaction' => $transaction, 'transactionDetails' => $transactionDetail];
            array_push($allTransactions, $data);
        }
        return sendSuccess('Processor manager coffee', $allTransactions);
    }

    function getSendCoffeeDrying(Request $request) {
        $userId = $this->userId;
        $centerId = $this->user->table_id;
        $allTransactions = array();
        $transactions = Transaction::where('created_by', $userId)->where('transaction_status', 'sent')->whereHas('log', function($q) use($centerId) {
                    $q->where('action', 'sent')->where('type', 'coffee_drying')->where('entity_id', $centerId);
                })->whereHas('transactionDetail', function($q) use($centerId) {
                    $q->where('container_status', 1);
                }, '>', 0)->with(['transactionDetail' => function($query) {
                        $query->where('container_status', 1);
                    }])->orderBy('transaction_id', 'desc')->get();

        foreach ($transactions as $key => $transaction) {
            $transactionDetail = $transaction->transactionDetail;
            $transaction->center_id = $transaction->log->entity_id;
            $transaction->is_sent = 0;
            $transaction->makeHidden('transactionDetail');
            $transaction->makeHidden('log');
            $data = ['transaction' => $transaction, 'transactionDetails' => $transactionDetail];
            array_push($allTransactions, $data);
        }
        return sendSuccess('Processor manager coffee', $allTransactions);
    }

}
