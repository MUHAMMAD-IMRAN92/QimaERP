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
use App\CenterUser;

class CoffeeDryingController extends Controller {

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
                return sendError('Session Expired', 404);
            }
        } else {
            return sendError('Session Expired', 404);
        }
    }

    function getCoffeeDryingPendingCoffee(Request $request) {
        $userId = $this->userId;
        $centerId = 0;
        $userCenter = CenterUser::where('user_id', $this->userId)->first();
        if ($userCenter) {
            $centerId = $userCenter->center_id;
        }
        $allTransactions = array();
        $transactions = Transaction::where('is_parent', 0)->whereHas('log', function($q) use($centerId) {
                            $q->where('action', 'sent')->where('type', 'coffee_drying')->where('entity_id', $centerId);
                        })
                        //->doesntHave('isReference')
                        ->with(['transactionDetail' => function($query) {
                                $query->where('container_status', 0);
                            }])->orderBy('transaction_id', 'desc')->get();

        foreach ($transactions as $key => $transaction) {
            $transactionDetail = $transaction->transactionDetail;
            $transaction->center_id = $transaction->log->entity_id;
            $transaction->center_name = $transaction->log->center_name;
            $transaction->is_sent = 0;
            $transaction->makeHidden('transactionDetail');
            $transaction->makeHidden('log');
            $data = ['transaction' => $transaction, 'transactionDetails' => $transactionDetail];
            array_push($allTransactions, $data);
        }
        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.RECV_COFFEE_MESSAGE"), $allTransactions);
    }

    function receivedCoffeeDryingCoffee(Request $request) {
        $validator = Validator::make($request->all(), [
                    'transactions' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            return sendError($errors, 400);
        }
        $receivedTransactions = json_decode($request['transactions']);
       
        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.RECV_COFFEE_MESSAGE"), $allTransactions);
    }

}
