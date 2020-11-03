<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\TransactionDetail;
use App\TransactionLog;
use App\Transaction;
use App\LoginUser;
use App\User;

class YemenOperativeController extends Controller {

    private $userId;
    private $user;
    private $app_lang;

    public function __construct() {
        set_time_limit(0);
        $headers = getallheaders();
        $checksession = LoginUser::where('session_key', $headers['session_token'])->first();
        if (isset($headers['app_lang'])) {
            $this->app_lang = $headers['app_lang'];
        } else {
            $this->app_lang = 'en';
        }
        if ($checksession) {
            $user = User::where('user_id', $checksession->user_id)->with('roles')->first();
            if ($user) {
                $this->user = $user;
                $this->userId = $user->user_id;
            } else {
                return sendError(Config("statuscodes." . $this->app_lang . ".error_messages.SESSION_EXPIRED"), 404);
            }
        } else {
            return sendError(Config("statuscodes." . $this->app_lang . ".error_messages.SESSION_EXPIRED"), 404);
        }
    }

    function getYemenOperativeCoffee(Request $request) {
        $allTransactions = array();

        $transactions = Transaction::where('is_parent', 0)->whereHas('log', function($q) {
                    $q->where('action', 'sent')->whereIn('type', ['sent_to_yemen']);
                })->whereHas('transactionDetail', function($q) {
                    $q->where('container_status', 0);
                }, '>', 0)->with(['transactionDetail' => function($query) {
                        $query->where('container_status', 0);
                    }])->with('meta')->orderBy('transaction_id', 'desc')->get();

        foreach ($transactions as $key => $transaction) {
            $childTransaction = array();
            $transactionDetail = $transaction->transactionDetail;
            $transaction->center_id = $transaction->log->entity_id;
            $transaction->center_name = $transaction->log->center_name;
            $transactionMata = $transaction->meta;
            $transaction->makeHidden('transactionDetail');
            $transaction->makeHidden('log');
            $transaction->makeHidden('meta');
            $removeLocalId = explode("-", $transaction->batch_number);
            if ($removeLocalId[3] == '000') {
                $FindParentTransactions = Transaction::where('is_parent', 0)->where('batch_number', $transaction->batch_number)->first();
                if ($FindParentTransactions) {
                    $childTransaction = Transaction::where('is_parent', $FindParentTransactions->transaction_id)->get();

                    foreach ($childTransaction as $key => $childTransactio) {
                        $childTransactio->is_parent = $transaction->transaction_id;
                    }
                }
            }
            $data = ['transaction' => $transaction, 'transactionDetails' => $transactionDetail, 'transactionMeta' => $transactionMata, 'child_transactions' => $childTransaction];
            array_push($allTransactions, $data);
        }
        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.RECV_COFFEE_MESSAGE"), $allTransactions);
    }

    function receivedSpecialProcessingCoffee(Request $request) {
        $validator = Validator::make($request->all(), [
                    'transactions' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            return sendError($errors, 400);
        }
        $userId = $this->userId;
        $receivedCofffee = array();
        $receivedTransactions = json_decode($request['transactions']);
        DB::beginTransaction();
        try {
            foreach ($receivedTransactions as $key => $receivedTransaction) {
                
            }
            DB::commit();
        } catch (PDOException $e) {
//   DB::rollback();

            return Response::json(array('status' => 'error', 'message' => 'Something was wrong', 'data' => []), 499);
        }
        $allTransactions = array();

        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.RECV_COFFEE_MESSAGE"), $allTransactions);
    }

}
