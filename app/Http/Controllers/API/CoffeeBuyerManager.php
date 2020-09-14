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
                })->with(['profileImage' => function($query) use($user_image, $user_image_path) {
                        $query->select('file_id', 'user_file_name', \DB::raw("IFNULL(CONCAT('" . $user_image_path . "/',`user_file_name`),IFNULL(`user_file_name`,'" . $user_image . "')) as user_file_name"));
                    }])->with(['idcardImage' => function($query) use($user_image, $user_image_path) {
                        $query->select('file_id', 'user_file_name', \DB::raw("IFNULL(CONCAT('" . $user_image_path . "/',`user_file_name`),IFNULL(`user_file_name`,'" . $user_image . "')) as user_file_name"));
                    }])->orderBy('farmer_name')->get();

        foreach ($farmers as $key => $farmer) {
            $farmer->farmer_id_card_picture = '';
            $farmer->farmer_picture = '';
            if (isset($farmer->idcardImage) && isset($farmer->idcardImage->user_file_name)) {
                $farmer->farmer_id_card_picture = $farmer->idcardImage->user_file_name;
            }
            if (isset($farmer->profileImage) && isset($farmer->profileImage->user_file_name)) {
                $farmer->farmer_picture = $farmer->profileImage->user_file_name;
            }
            $farmer->farmer_village = $farmer->village_code;
            $farmer->farmer_id_card_no = $farmer->farmer_nicn;
            $farmer->makeHidden('idcardImage');
            $farmer->makeHidden('profileImage');
            $farmer->makeHidden('village_code');
            $farmer->makeHidden('farmer_nicn');
            $farmer->makeHidden('idcard_picture_id');
            $farmer->makeHidden('picture_id');
        }
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
        $sentTransactions = json_decode($request['transactions']);

        $alreadySentCoffee = array();
        $sentCoffeeArray = array();
        foreach ($sentTransactions as $key => $sentTransaction) {

            if (isset($sentTransaction->transactions) && $sentTransaction->transactions) {
                $alreadyExistTransaction = Transaction::where('reference_id', $sentTransaction->transactions->reference_id)->first();
                if ($alreadyExistTransaction) {
                    $sentTransaction->transactions->already_sent = true;
                    array_push($alreadySentCoffee, $sentTransaction);
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
                                'sent_to' => 3,
                                'is_sent' => 0,
                    ]);

                    $transactionLog = TransactionLog::create([
                                'transaction_id' => $transaction->transaction_id,
                                'action' => 'sent',
                                'created_by' => $sentTransaction->transactions->created_by,
                                'entity_id' => $sentTransaction->transactions->center_id,
                                'local_created_at' => date("Y-m-d H:i:s", strtotime($sentTransaction->transactions->created_at)),
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
                            'center_id' => $transactionContainer->center_id,
                            'reference_id' => 0,
                        ]);
                    }
                    array_push($sentCoffeeArray, $transaction->transaction_id);
                }
            }
        }
        $currentlySentCoffees = Transaction::whereIn('transaction_id', $sentCoffeeArray)->with('transactionDetail', 'log')->get();
        $dataArray = array();
        foreach ($currentlySentCoffees as $key => $currentlySentCoffee) {
            $transactionsDetail = $currentlySentCoffee->transactionDetail;
            $currentlySentCoffee->center_id = $currentlySentCoffee->log->entity_id;
            $currentlySentCoffee->makeHidden('transactionDetail');
            $currentlySentCoffee->makeHidden('log');
            $currentlySentCoffee->already_sent = FALSE;
            $sentCoffee = ['transactions' => $currentlySentCoffee, 'transactions_detail' => $transactionsDetail];
            array_push($dataArray, $sentCoffee);
        }
        $data = array_merge($dataArray, $alreadySentCoffee);
        if (count($alreadySentCoffee) > 0) {
            return sendSuccess('Some transactions have already been sent.', $data);
        }
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
        $transactions = Transaction::where('created_by', $this->userId)->where('is_parent', 0)->where('transaction_status', 'sent')->whereHas('transactionDetail', function($q) {
                    $q->where('container_status', 0);
                }, '>', 0)->with(['transactionDetail' => function($query) {
                        $query->where('container_status', 0);
                    }])->with('log')->orderBy('transaction_id', 'desc')->get();
        foreach ($transactions as $key => $transaction) {
            $transactionDetail = $transaction->transactionDetail;
            $transaction->makeHidden('transactionDetail');
            $transaction->makeHidden('childTransation');
            $data = ['transactions' => $transaction, 'transactions_detail' => $transactionDetail];
            array_push($allTransactions, $data);
        }
        return sendSuccess('Transactions retrieved successfully!', $allTransactions);
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
