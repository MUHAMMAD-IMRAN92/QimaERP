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

class CoffeeBuyerManager extends Controller {

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

    function farmer(Request $request) {
        $farmerName = $request->farmer_name;
        $villageCode = $request->village_code;
        $farmerCode = $request->farmer_code;
        $farmerNicn = $request->farmer_nicn;
        $user_image = asset('storage/app/images/demo_user_image.png');
        $user_image_path = asset('storage/app/images/');
        $farmers = Farmer::when($farmerName, function($q) use ($farmerName) {
                    $q->where(function($q) use ($farmerName) {
                        $q->where('farmer_name', 'like', "%$farmerName%");
                    });
                })->when($villageCode, function($q) use ($villageCode) {
                    $q->where(function($q) use ($villageCode) {
                        $q->where('village_code', 'like', "%$villageCode%");
                    });
                })->when($farmerCode, function($q) use ($farmerCode) {
                    $q->where(function($q) use ($farmerCode) {
                        $q->where('farmer_code', 'like', "%$farmerCode%");
                    });
                })->with('governerate', 'region', 'village')->with(['profileImage' => function($query) use($user_image, $user_image_path) {
                        $query->select('file_id', 'system_file_name', \DB::raw("IFNULL(CONCAT('" . $user_image_path . "/',`system_file_name`),IFNULL(`system_file_name`,'" . $user_image . "')) as system_file_name"));
                    }])->with(['idcardImage' => function($query) use($user_image, $user_image_path) {
                        $query->select('file_id', 'system_file_name', \DB::raw("IFNULL(CONCAT('" . $user_image_path . "/',`system_file_name`),IFNULL(`system_file_name`,'" . $user_image . "')) as system_file_name"));
                    }])->orderBy('farmer_name')->get();
        return sendSuccess('Successfully retrieved farmers', $farmers);
    }

    function sentTransactions(Request $request) {
        //::validation
        $validator = Validator::make($request->all(), [
                    'transactions' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            return sendError($errors, 400);
        }
        $sentTransaction = json_decode($request['transactions']);
        $alreadySentCoffee = null;
        $sentCoffeeArray = null;
        if (isset($sentTransaction->transactions) && $sentTransaction->transactions) {
            $alreadyExistTransaction = Transaction::where('reference_id', $sentTransaction->transactions->reference_id)->first();
            if ($alreadyExistTransaction) {
                $alreadySentCoffee = $sentTransaction;
            } else {
                $transaction = Transaction::create([
                            'batch_number' => $sentTransaction->transactions->batch_number,
                            'is_parent' => $sentTransaction->transactions->is_parent,
                            'is_mixed' => $sentTransaction->transactions->is_mixed,
                            'created_by' => $sentTransaction->transactions->created_by,
                            'is_local' => FALSE,
                            'transaction_type' => 2,
                            'local_code' => $sentTransaction->transactions->local_code,
                            'transaction_status' => 'sent',
                            'reference_id' => $sentTransaction->transactions->reference_id,
                            'is_server_id' => 1,
                            'is_new' => $sentTransaction->transactions->is_new,
                            'sent_to' => 3
                ]);

                $transactionLog = TransactionLog::create([
                            'transaction_id' => $transaction->transaction_id,
                            'action' => 'sent',
                            'created_by' => $sentTransaction->transactions->created_by,
                            'entity_id' => $sentTransaction->transactions->center_id,
                            'local_created_at' => $sentTransaction->transactions->created_at,
                            'type' => 'center',
                ]);

                $transactionContainers = $sentTransaction->transactions_detail;
                foreach ($transactionContainers as $key => $transactionContainer) {
                    TransactionDetail::create([
                        'transaction_id' => $transaction->transaction_id,
                        'container_number' => $transactionContainer->container_number,
                        'created_by' => $sentTransaction->transactions->created_by,
                        'is_local' => FALSE,
                        'container_weight' => $transactionContainer->container_weight,
                        'weight_unit' => $transactionContainer->weight_unit,
                    ]);
                }
                $sentCoffeeArray = $transaction->transaction_id;
            }
        }
        $currentlySentCoffee = Transaction::where('transaction_id', $sentCoffeeArray)->with('transactionDetail', 'log')->first();

        if ($alreadySentCoffee) {
            $data = ['already_sent_coffee' => $alreadySentCoffee];
            return sendError('Coffee already sent', 406, $data);
        }
        $transactionsDetail = $currentlySentCoffee->transactionDetail;
        $currentlySentCoffee->center_id = $currentlySentCoffee->log->entity_id;
        $currentlySentCoffee->makeHidden('transactionDetail');
        $currentlySentCoffee->makeHidden('log');
        $sentCoffee = ['transactions' => $currentlySentCoffee, 'transactions_detail' => $transactionsDetail];
        $data = ['sent_coffee' => $sentCoffee];
        return sendSuccess('Coffee sent successfully', $data);
    }

    function centers(Request $request) {
        $search = $request->search;
        $centers = Center::when($search, function($q) use ($search) {
                    $q->where(function($q) use ($search) {
                        $q->where('center_code', 'like', "%$search%");
                    });
                })->get();
        return sendSuccess('Successfully retrieved centers', $centers);
    }

    function coffeeBuyerManagerCoffee(Request $request) {
        $allTransactions = array();
        $transactions = Transaction::where('is_parent', 0)->where('transaction_status', 'created')->doesntHave('isReference')->with('childTransation.transactionDetail', 'transactionDetail')->orderBy('transaction_id', 'desc')->get();
        foreach ($transactions as $key => $transaction) {
            $childTransactions = array();
            if ($transaction->childTransation) {
                foreach ($transaction->childTransation as $key => $childTransation) {
                    $childTransationDetail = $childTransation->transactionDetail;
                    $childTransation->makeHidden('transactionDetail');
                    $childData = ['transactions' => $childTransation, 'transactions_detail' => $childTransationDetail];
                    array_push($childTransactions, $childData);
                }
            }
            $transactionDetail = $transaction->transactionDetail;
            $transaction->makeHidden('transactionDetail');
            $transaction->makeHidden('childTransation');
            $data = ['transactions' => $transaction, 'child_transation' => $childTransactions, 'transactions_detail' => $transactionDetail];
            array_push($allTransactions, $data);
        }

        return sendSuccess('Transactions retrieved successfully', $allTransactions);
    }

    function coffeeBuyerManagerSentCoffeeTransaction(Request $request) {
        $allTransactions = array();
        $transactions = Transaction::where('created_by', $this->userId)->where('transaction_status', 'sent')->doesntHave('isReference')->with('childTransation.transactionDetail', 'transactionDetail')->orderBy('transaction_id', 'desc')->get();

        foreach ($transactions as $key => $transaction) {
            $transactionDetail = $transaction->transactionDetail;
            $transaction->makeHidden('transactionDetail');
            $transaction->makeHidden('childTransation');
            $data = ['transactions' => $transaction, 'transactions_detail' => $transactionDetail];
            array_push($allTransactions, $data);
        }
        return sendSuccess('Transactions retrieved successfully', $allTransactions);
    }

    function approvedFarmer(Request $request) {
        $validator = Validator::make($request->all(), [
                    'farmer_code' => 'required|array',
        ]);
        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            return sendError($errors, 400);
        }
        Farmer::wherein('farmer_code', $request['farmer_code'])->update(['is_status' => 1]);
        return sendSuccess('Farmer approved successfully', []);
    }

}
