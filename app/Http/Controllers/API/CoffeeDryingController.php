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

class CoffeeDryingController extends Controller {

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

    function getCoffeeDryingPendingCoffee(Request $request) {
        $userId = $this->userId;
        $centerId = $this->user->table_id;
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
        return sendSuccess('Coffee drying manager received coffee', $allTransactions);
    }

}
