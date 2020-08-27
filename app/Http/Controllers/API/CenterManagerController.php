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
        //return sendSuccess('Successfully retrieved farmers', $sentTransactions);
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
                        'weight' => $transactionContainer->container_weight,
                    ]);
                }
                $reciviedCoffee = $transaction->transaction_id;
            }
        }
        $currentlyReceivedCoffee = Transaction::where('transaction_id', $reciviedCoffee)->with('transactionDetail')->get();

        $data = ['received_coffee' => $currentlyReceivedCoffee, 'already_received_coffee' => $alreadyReciviedCoffee];
        if ($alreadyReciviedCoffee) {
            return sendError('Coffee already received', 406, $data);
        }
        return sendSuccess('Transactions received successfully', $data);
    }

    function centerManagerCoffee(Request $request) {
        $centerId = $this->user->table_id;
        $transactions = Transaction::where('is_parent', 0)->where('transaction_status', 'sent')->doesntHave('isReference')->whereHas('transactionLog', function($q) use($centerId) {
                    $q->where('action', 'sent')->where('type', 'center')->where('entity_id', $centerId);
                })->with('childTransation.transactionDetail', 'transactionDetail')->orderBy('transaction_id', 'desc')->get();

        return sendSuccess('Center manager received coffee', $transactions);
    }

    function centerManagerReceivedCoffee(Request $request) {
        $userId = $this->userId;
        $centerId = $this->user->table_id;
        $transactions = Transaction::where('created_by', $userId)->where('transaction_status', 'received')->doesntHave('isReference')->whereHas('transactionLog', function($q) use($centerId) {
                    $q->where('action', 'received')->where('type', 'center')->where('entity_id', $centerId);
                })->with('childTransation.transactionDetail', 'transactionDetail')->orderBy('transaction_id', 'desc')->get();

        return sendSuccess('Center manager received coffee', $transactions);
    }

}
