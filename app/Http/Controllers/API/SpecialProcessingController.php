<?php

namespace App\Http\Controllers\API;

<<<<<<< HEAD
use App\User;
use App\Yeast;
use PDOException;
use App\LoginUser;
use Carbon\Carbon;
=======
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use App\TransactionDetail;
use App\TransactionLog;
use App\CoffeeProcess;
use App\Transaction;
use App\LoginUser;
use App\User;
>>>>>>> parent of 53486db... fix the formatting
use App\CenterUser;
use App\MetaTransation;
<<<<<<< HEAD
use App\TransactionLog;
use App\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
=======
use App\Yeast;
use DB;
>>>>>>> parent of 53486db... fix the formatting

class SpecialProcessingController extends Controller {

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

    function getSpeicalProcessingManagerPendingCoffee(Request $request) {
        $userId = $this->userId;
        $centerId = 0;
        $userCenter = CenterUser::where('user_id', $this->userId)->first();
        if ($userCenter) {
            $centerId = $userCenter->center_id;
        }
        $allTransactions = array();
        $transactions = Transaction::where('is_parent', 0)->whereHas('log', function($q) use($centerId) {
                    $q->where('action', 'sent')->whereIn('type', ['special_processing', 'coffee_drying'])->where('entity_id', $centerId);
                })->whereHas('transactionDetail', function($q) use($centerId) {
                    $q->where('container_status', 0);
                }, '>', 0)->with(['transactionDetail' => function($query) {
                        $query->where('container_status', 0);
                    }])->with('meta')->orderBy('transaction_id', 'desc')->get();

        foreach ($transactions as $key => $transaction) {
            $transactionDetail = $transaction->transactionDetail;
            $transaction->center_id = $transaction->log->entity_id;
            $transaction->center_name = $transaction->log->center_name;
            $transactionMata = $transaction->meta;
            $transaction->is_sent = 0;
            if ($transaction->sent_to == 8) {
                $checkStatus = Transaction::where('reference_id', $transaction->transaction_id)->where('sent_to', 9)->first();
                if ($checkStatus) {
                    $transaction->is_sent = 1;
                }
            }
            $transaction->makeHidden('transactionDetail');
            $transaction->makeHidden('log');
            $transaction->makeHidden('meta');
            $data = ['transaction' => $transaction, 'transactionDetails' => $transactionDetail, 'transactionMeta' => $transactionMata];
            array_push($allTransactions, $data);
        }
        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.RECV_COFFEE_MESSAGE"), $allTransactions);
    }

    function processList(Request $request) {
        $process = CoffeeProcess::all();

        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.PROCESS_LIST"), $process);
    }

    function yeastList(Request $request) {
        $yest = Yeast::all();
        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.YEAST_LIST"), $yest);
    }

    function receivedSpecialProcessingCoffee(Request $request) {
        $validator = Validator::make($request->all(), [
                    'transactions' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            return sendError($errors, 400);
        }

        // return response()->json(json_decode($request->transactions));

        $userId = $this->userId;
        $receivedCofffee = array();
        $receivedTransactions = json_decode($request['transactions']);
        DB::beginTransaction();
        try {
            foreach ($receivedTransactions as $key => $receivedTransaction) {
                if (isset($receivedTransaction->transaction) && $receivedTransaction->transaction) {
                    if ($receivedTransaction->transaction->sent_to == 7) {
                        if ($receivedTransaction->transaction->is_local == FALSE && $receivedTransaction->transaction->update_meta == TRUE) {
                            $updateTransaction = Transaction::where('transaction_id', $receivedTransaction->transaction->transaction_id)->first();
                            $updateTransaction->sent_to = $receivedTransaction->transaction->sent_to;
                            $updateTransaction->save();
                            TransactionDetail::where('transaction_id', $receivedTransaction->transaction->transaction_id)->delete();
                            MetaTransation::where('transaction_id', $receivedTransaction->transaction->transaction_id)->delete();


                            $transactionContainers = $receivedTransaction->transactionDetails;
                            foreach ($transactionContainers as $key => $transactionContainer) {
                                TransactionDetail::create([
                                    'transaction_id' => $receivedTransaction->transaction->transaction_id,
                                    'container_number' => $transactionContainer->container_number,
                                    'created_by' => $receivedTransaction->transaction->created_by,
                                    'is_local' => FALSE,
                                    'container_weight' => $transactionContainer->container_weight,
                                    'weight_unit' => $transactionContainer->weight_unit,
                                    'center_id' => $transactionContainer->center_id,
                                    'reference_id' => $transactionContainer->reference_id,
                                ]);
                            }

                            $transactionMeta = $receivedTransaction->transactionMeta;
                            foreach ($transactionMeta as $key => $transactionMe) {
                                if (strstr($transactionMe->key, 'BS') || strstr($transactionMe->key, 'DT') || strstr($transactionMe->key, 'SC') || strstr($transactionMe->key, 'DM') || strstr($transactionMe->key, 'DS') || strstr($transactionMe->key, 'GS') || strstr($transactionMe->key, 'ES') || strstr($transactionMe->key, 'PS') || strstr($transactionMe->key, 'SS') || strstr($transactionMe->key, 'LS') || strstr($transactionMe->key, 'HS') || strstr($transactionMe->key, 'QS') || strstr($transactionMe->key, 'KS') || strstr($transactionMe->key, 'VB') || strstr($transactionMe->key, 'PB') || strstr($transactionMe->key, 'VP') || strstr($transactionMe->key, 'PP') || strstr($transactionMe->key, 'SM')) {
                                    $basketArray = explode("_", $transactionMe->key);
                                    $basket = $basketArray[0];
                                    $weight = $basketArray[1];
                                    $transationsExplodeId = $basketArray[2];
                                    TransactionDetail::where('transaction_id', $transationsExplodeId)->where('container_number', $basket)->update(['container_status' => 1]);
                                }
                                MetaTransation::create([
                                    'transaction_id' => $receivedTransaction->transaction->transaction_id,
                                    'key' => $transactionMe->key,
                                    'value' => $transactionMe->value,
                                    'local_created_at' => Carbon::parse($transactionMe->local_created_at)->toDateTimeString()
                                ]);
                            }
                        } else {

                            $transaction = Transaction::create([
                                        'batch_number' => $receivedTransaction->transaction->batch_number,
                                        'is_parent' => $receivedTransaction->transaction->is_parent,
                                        'is_mixed' => $receivedTransaction->transaction->is_mixed,
                                        'created_by' => $receivedTransaction->transaction->created_by,
                                        'is_local' => FALSE,
                                        'transaction_type' => 1,
                                        'local_code' => $receivedTransaction->transaction->local_code,
                                        'transaction_status' => 'received',
                                        'reference_id' => $receivedTransaction->transaction->reference_id,
                                        'is_server_id' => 1,
                                        'is_new' => 0,
                                        'sent_to' => $receivedTransaction->transaction->sent_to,
                                        'is_sent' => 1,
                                        'is_in_process' => $receivedTransaction->transaction->is_in_process,
                                        'session_no' => $receivedTransaction->transaction->session_no,
                                        'local_created_at' => date("Y-m-d H:i:s", strtotime($receivedTransaction->transaction->created_at)),
                            ]);

                            $transactionLog = TransactionLog::create([
                                        'transaction_id' => $transaction->transaction_id,
                                        'action' => 'sent',
                                        'created_by' => $receivedTransaction->transaction->created_by,
                                        'entity_id' => $receivedTransaction->transaction->center_id,
                                        'center_name' => '',
                                        'local_created_at' => date("Y-m-d H:i:s", strtotime($receivedTransaction->transaction->created_at)),
                                        'type' => 'special_processing',
                            ]);

                            $transactionContainers = $receivedTransaction->transactionDetails;
                            foreach ($transactionContainers as $key => $transactionContainer) {
                                TransactionDetail::create([
                                    'transaction_id' => $transaction->transaction_id,
                                    'container_number' => $transactionContainer->container_number,
                                    'created_by' => $receivedTransaction->transaction->created_by,
                                    'is_local' => FALSE,
                                    'container_weight' => $transactionContainer->container_weight,
                                    'weight_unit' => $transactionContainer->weight_unit,
                                    'center_id' => $transactionContainer->center_id,
                                    'reference_id' => $transactionContainer->reference_id,
                                ]);
                            }

                            $transactionMeta = $receivedTransaction->transactionMeta;
                            foreach ($transactionMeta as $key => $transactionMe) {
                                if (strstr($transactionMe->key, 'BS') || strstr($transactionMe->key, 'DT') || strstr($transactionMe->key, 'SC') || strstr($transactionMe->key, 'DM') || strstr($transactionMe->key, 'DS') || strstr($transactionMe->key, 'GS') || strstr($transactionMe->key, 'ES') || strstr($transactionMe->key, 'PS') || strstr($transactionMe->key, 'SS') || strstr($transactionMe->key, 'LS') || strstr($transactionMe->key, 'HS') || strstr($transactionMe->key, 'QS') || strstr($transactionMe->key, 'KS') || strstr($transactionMe->key, 'VB') || strstr($transactionMe->key, 'PB') || strstr($transactionMe->key, 'VP') || strstr($transactionMe->key, 'PP') || strstr($transactionMe->key, 'SM')) {
                                    $basketArray = explode("_", $transactionMe->key);
                                    $basket = $basketArray[0];
                                    $weight = $basketArray[1];
                                    $transationsExplodeId = $basketArray[2];
                                    TransactionDetail::where('transaction_id', $transationsExplodeId)->where('container_number', $basket)->update(['container_status' => 1]);
                                }
                                MetaTransation::create([
                                    'transaction_id' => $transaction->transaction_id,
                                    'key' => $transactionMe->key,
                                    'value' => $transactionMe->value,
                                    'local_created_at' => Carbon::parse($transactionMe->local_created_at)->toDateTimeString()
                                ]);
                            }
                        }
                    }

                    if ($receivedTransaction->transaction->sent_to == 8) {
                        if ($receivedTransaction->transaction->is_local == FALSE && $receivedTransaction->transaction->update_meta == TRUE) {
                            $updateTransaction = Transaction::where('transaction_id', $receivedTransaction->transaction->transaction_id)->first();
                            $updateTransaction->sent_to = $receivedTransaction->transaction->sent_to;
                            $updateTransaction->save();
                            TransactionDetail::where('transaction_id', $receivedTransaction->transaction->transaction_id)->delete();
                            MetaTransation::where('transaction_id', $receivedTransaction->transaction->transaction_id)->delete();


                            $transactionContainers = $receivedTransaction->transactionDetails;
                            foreach ($transactionContainers as $key => $transactionContainer) {
                                TransactionDetail::create([
                                    'transaction_id' => $receivedTransaction->transaction->transaction_id,
                                    'container_number' => $transactionContainer->container_number,
                                    'created_by' => $receivedTransaction->transaction->created_by,
                                    'is_local' => FALSE,
                                    'container_weight' => $transactionContainer->container_weight,
                                    'weight_unit' => $transactionContainer->weight_unit,
                                    'center_id' => $transactionContainer->center_id,
                                    'reference_id' => $transactionContainer->reference_id,
                                ]);
                            }

                            $transactionMeta = $receivedTransaction->transactionMeta;
                            foreach ($transactionMeta as $key => $transactionMe) {
                                if (strstr($transactionMe->key, 'BS') || strstr($transactionMe->key, 'DT') || strstr($transactionMe->key, 'SC') || strstr($transactionMe->key, 'DM') || strstr($transactionMe->key, 'DS') || strstr($transactionMe->key, 'GS') || strstr($transactionMe->key, 'ES') || strstr($transactionMe->key, 'PS') || strstr($transactionMe->key, 'SS') || strstr($transactionMe->key, 'LS') || strstr($transactionMe->key, 'HS') || strstr($transactionMe->key, 'QS') || strstr($transactionMe->key, 'KS') || strstr($transactionMe->key, 'VB') || strstr($transactionMe->key, 'PB') || strstr($transactionMe->key, 'VP') || strstr($transactionMe->key, 'PP') || strstr($transactionMe->key, 'SM')) {
                                    $basketArray = explode("_", $transactionMe->key);
                                    $basket = $basketArray[0];
                                    $weight = $basketArray[1];
                                    $transationsExplodeId = $basketArray[2];
                                    TransactionDetail::where('transaction_id', $transationsExplodeId)->where('container_number', $basket)->update(['container_status' => 1]);
                                }
                                MetaTransation::create([
                                    'transaction_id' => $receivedTransaction->transaction->transaction_id,
                                    'key' => $transactionMe->key,
                                    'value' => $transactionMe->value,
                                    'local_created_at' => Carbon::parse($transactionMe->local_created_at)->toDateTimeString()
                                ]);
                            }
                        } else {
                            $transaction = Transaction::create([
                                        'batch_number' => $receivedTransaction->transaction->batch_number,
                                        'is_parent' => $receivedTransaction->transaction->is_parent,
                                        'is_mixed' => $receivedTransaction->transaction->is_mixed,
                                        'created_by' => $receivedTransaction->transaction->created_by,
                                        'is_local' => FALSE,
                                        'transaction_type' => 1,
                                        'local_code' => $receivedTransaction->transaction->local_code,
                                        'transaction_status' => 'received',
                                        'reference_id' => $receivedTransaction->transaction->reference_id,
                                        'is_server_id' => 1,
                                        'is_new' => 0,
                                        'sent_to' => $receivedTransaction->transaction->sent_to,
                                        'is_sent' => 1,
                                        'is_in_process' => $receivedTransaction->transaction->is_in_process,
                                        'session_no' => $receivedTransaction->transaction->session_no,
                                        'local_created_at' => date("Y-m-d H:i:s", strtotime($receivedTransaction->transaction->created_at)),
                            ]);

                            $transactionLog = TransactionLog::create([
                                        'transaction_id' => $transaction->transaction_id,
                                        'action' => 'sent',
                                        'created_by' => $receivedTransaction->transaction->created_by,
                                        'entity_id' => $receivedTransaction->transaction->center_id,
                                        'center_name' => '',
                                        'local_created_at' => date("Y-m-d H:i:s", strtotime($receivedTransaction->transaction->created_at)),
                                        'type' => 'special_processing',
                            ]);

                            $transactionContainers = $receivedTransaction->transactionDetails;
                            foreach ($transactionContainers as $key => $transactionContainer) {
                                TransactionDetail::create([
                                    'transaction_id' => $transaction->transaction_id,
                                    'container_number' => $transactionContainer->container_number,
                                    'created_by' => $receivedTransaction->transaction->created_by,
                                    'is_local' => FALSE,
                                    'container_weight' => $transactionContainer->container_weight,
                                    'weight_unit' => $transactionContainer->weight_unit,
                                    'center_id' => $transactionContainer->center_id,
                                    'reference_id' => $transactionContainer->reference_id,
                                ]);
                            }

                            $transactionMeta = $receivedTransaction->transactionMeta;
                            foreach ($transactionMeta as $key => $transactionMe) {
                                if (strstr($transactionMe->key, 'BS') || strstr($transactionMe->key, 'DT') || strstr($transactionMe->key, 'SC') || strstr($transactionMe->key, 'DM') || strstr($transactionMe->key, 'DS') || strstr($transactionMe->key, 'GS') || strstr($transactionMe->key, 'ES') || strstr($transactionMe->key, 'PS') || strstr($transactionMe->key, 'SS') || strstr($transactionMe->key, 'LS') || strstr($transactionMe->key, 'HS') || strstr($transactionMe->key, 'QS') || strstr($transactionMe->key, 'KS') || strstr($transactionMe->key, 'VB') || strstr($transactionMe->key, 'PB') || strstr($transactionMe->key, 'VP') || strstr($transactionMe->key, 'PP') || strstr($transactionMe->key, 'SM')) {
                                    $basketArray = explode("_", $transactionMe->key);
                                    $basket = $basketArray[0];
                                    $weight = $basketArray[1];
                                    $transationsExplodeId = $basketArray[2];
                                    TransactionDetail::where('transaction_id', $transationsExplodeId)->where('container_number', $basket)->update(['container_status' => 1]);
                                }
                                MetaTransation::create([
                                    'transaction_id' => $transaction->transaction_id,
                                    'key' => $transactionMe->key,
                                    'value' => $transactionMe->value,
                                    'local_created_at' => Carbon::parse($transactionMe->local_created_at)->toDateTimeString()
                                ]);
                            }
                        }
                    }

                    if ($receivedTransaction->transaction->sent_to == 9) {
                        if ($receivedTransaction->transaction->is_local == FALSE && $receivedTransaction->transaction->update_meta == TRUE) {
                            $updateTransaction = Transaction::where('transaction_id', $receivedTransaction->transaction->transaction_id)->first();
                            $updateTransaction->sent_to = $receivedTransaction->transaction->sent_to;
                            $updateTransaction->save();
                            TransactionDetail::where('transaction_id', $receivedTransaction->transaction->transaction_id)->delete();
                            MetaTransation::where('transaction_id', $receivedTransaction->transaction->transaction_id)->delete();


                            $transactionContainers = $receivedTransaction->transactionDetails;
                            foreach ($transactionContainers as $key => $transactionContainer) {
                                TransactionDetail::create([
                                    'transaction_id' => $receivedTransaction->transaction->transaction_id,
                                    'container_number' => $transactionContainer->container_number,
                                    'created_by' => $receivedTransaction->transaction->created_by,
                                    'is_local' => FALSE,
                                    'container_weight' => $transactionContainer->container_weight,
                                    'weight_unit' => $transactionContainer->weight_unit,
                                    'center_id' => $transactionContainer->center_id,
                                    'reference_id' => $transactionContainer->reference_id,
                                ]);
                            }

                            $transactionMeta = $receivedTransaction->transactionMeta;
                            foreach ($transactionMeta as $key => $transactionMe) {
                                if (strstr($transactionMe->key, 'BS') || strstr($transactionMe->key, 'DT') || strstr($transactionMe->key, 'SC') || strstr($transactionMe->key, 'DM') || strstr($transactionMe->key, 'DS') || strstr($transactionMe->key, 'GS') || strstr($transactionMe->key, 'ES') || strstr($transactionMe->key, 'PS') || strstr($transactionMe->key, 'SS') || strstr($transactionMe->key, 'LS') || strstr($transactionMe->key, 'HS') || strstr($transactionMe->key, 'QS') || strstr($transactionMe->key, 'KS') || strstr($transactionMe->key, 'VB') || strstr($transactionMe->key, 'PB') || strstr($transactionMe->key, 'VP') || strstr($transactionMe->key, 'PP') || strstr($transactionMe->key, 'SM')) {
                                    $basketArray = explode("_", $transactionMe->key);
                                    $basket = $basketArray[0];
                                    $weight = $basketArray[1];
                                    $transationsExplodeId = $basketArray[2];
                                    TransactionDetail::where('transaction_id', $transationsExplodeId)->where('container_number', $basket)->update(['container_status' => 1]);
                                }
                                MetaTransation::create([
                                    'transaction_id' => $receivedTransaction->transaction->transaction_id,
                                    'key' => $transactionMe->key,
                                    'value' => $transactionMe->value,
                                    'local_created_at' => Carbon::parse($transactionMe->local_created_at)->toDateTimeString()
                                ]);
                            }
                        } else {

                            if ($receivedTransaction->transaction->is_server_id == FALSE) {

                                $code = $receivedTransaction->transaction->reference_id . '_' . $userId . '-T';

                                $checkTransaction = Transaction::where('local_code', 'like', "$code%")->latest('transaction_id')->first();

                                $refTransactions = $checkTransaction->transaction_id;
                            } else {
                                $refTransactions = $receivedTransaction->transaction->reference_id;
                            }

                            $transaction = Transaction::create([
                                        'batch_number' => $receivedTransaction->transaction->batch_number,
                                        'is_parent' => $receivedTransaction->transaction->is_parent,
                                        'is_mixed' => $receivedTransaction->transaction->is_mixed,
                                        'created_by' => $receivedTransaction->transaction->created_by,
                                        'is_local' => FALSE,
                                        'transaction_type' => 1,
                                        'local_code' => $receivedTransaction->transaction->local_code,
                                        'transaction_status' => 'sent',
                                        'reference_id' => $refTransactions,
                                        'is_server_id' => 1,
                                        'is_new' => 0,
                                        'sent_to' => $receivedTransaction->transaction->sent_to,
                                        'is_sent' => 1,
                                        'is_in_process' => $receivedTransaction->transaction->is_in_process,
                                        'session_no' => $receivedTransaction->transaction->session_no,
                                        'local_created_at' => date("Y-m-d H:i:s", strtotime($receivedTransaction->transaction->created_at)),
                            ]);

                            $transactionLog = TransactionLog::create([
                                        'transaction_id' => $transaction->transaction_id,
                                        'action' => 'sent',
                                        'created_by' => $receivedTransaction->transaction->created_by,
                                        'entity_id' => $receivedTransaction->transaction->center_id,
                                        'center_name' => '',
                                        'local_created_at' => date("Y-m-d H:i:s", strtotime($receivedTransaction->transaction->created_at)),
                                        'type' => 'coffee_drying',
                            ]);

                            $transactionContainers = $receivedTransaction->transactionDetails;
                            foreach ($transactionContainers as $key => $transactionContainer) {
                                TransactionDetail::create([
                                    'transaction_id' => $transaction->transaction_id,
                                    'container_number' => $transactionContainer->container_number,
                                    'created_by' => $receivedTransaction->transaction->created_by,
                                    'is_local' => FALSE,
                                    'container_weight' => $transactionContainer->container_weight,
                                    'weight_unit' => $transactionContainer->weight_unit,
                                    'center_id' => $transactionContainer->center_id,
                                    'reference_id' => $transactionContainer->reference_id,
                                ]);
                            }

                            $transactionMeta = $receivedTransaction->transactionMeta;
                            foreach ($transactionMeta as $key => $transactionMe) {
                                if (strstr($transactionMe->key, 'BS') || strstr($transactionMe->key, 'DT') || strstr($transactionMe->key, 'SC') || strstr($transactionMe->key, 'DM') || strstr($transactionMe->key, 'DS') || strstr($transactionMe->key, 'GS') || strstr($transactionMe->key, 'ES') || strstr($transactionMe->key, 'PS') || strstr($transactionMe->key, 'SS') || strstr($transactionMe->key, 'LS') || strstr($transactionMe->key, 'HS') || strstr($transactionMe->key, 'QS') || strstr($transactionMe->key, 'KS') || strstr($transactionMe->key, 'VB') || strstr($transactionMe->key, 'PB') || strstr($transactionMe->key, 'VP') || strstr($transactionMe->key, 'PP') || strstr($transactionMe->key, 'SM')) {
                                    $basketArray = explode("_", $transactionMe->key);
                                    $basket = $basketArray[0];
                                    $weight = $basketArray[1];
                                    TransactionDetail::where('transaction_id', $refTransactions)->where('container_number', $basket)->update(['container_status' => 1]);
                                }
                                MetaTransation::create([
                                    'transaction_id' => $transaction->transaction_id,
                                    'key' => $transactionMe->key,
                                    'value' => $transactionMe->value,
                                    'local_created_at' => Carbon::parse($transactionMe->local_created_at)->toDateTimeString()
                                ]);
                            }
                        }
                    }
                }
            }
            DB::commit();
        } catch (PDOException $e) {
//   DB::rollback();

            return Response::json(array('status' => 'error', 'message' => 'Something was wrong', 'data' => []), 499);
        }
        $allTransactions = array();

        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.RECV_COFFEE_MESSAGE"), $allTransactions);
    }

//    function spSendCoffeeDryingCoffee(Request $request) {
//        $validator = Validator::make($request->all(), [
//                    'transactions' => 'required',
//        ]);
//        if ($validator->fails()) {
//            $errors = implode(', ', $validator->errors()->all());
//            return sendError($errors, 400);
//        }
//        $userId = $this->userId;
//        $sentCofffeeArray = array();
//        $receivedTransactions = json_decode($request['transactions']);
//        foreach ($receivedTransactions as $key => $receivedTransaction) {
//            //::Process start transactions
//            if (isset($receivedTransaction->transaction) && $receivedTransaction->transaction) {
//                if ($receivedTransaction->transaction->is_server_id == True) {
//                    $receivedTransId = $receivedTransaction->transaction->reference_id;
//                } else {
//                    $code = $receivedTransaction->transaction->reference_id . '_' . $userId . '-T';
//                    $checkTransaction = Transaction::where('local_code', 'like', "$code%")->latest('transaction_id')->first();
//                    $receivedTransId = $checkTransaction->transaction_id;
//                }
//                $processTransaction = Transaction::create([
//                            'batch_number' => $receivedTransaction->transaction->batch_number,
//                            'is_parent' => $receivedTransaction->transaction->is_parent,
//                            'is_mixed' => $receivedTransaction->transaction->is_mixed,
//                            'created_by' => $receivedTransaction->transaction->created_by,
//                            'is_local' => FALSE,
//                            'transaction_type' => 2,
//                            'local_code' => $receivedTransaction->transaction->local_code,
//                            'transaction_status' => 'sent',
//                            'reference_id' => $receivedTransId,
//                            'is_server_id' => 1,
//                            'is_new' => 0,
//                            'sent_to' => 9,
//                            'is_sent' => 1,
//                ]);
//                array_push($sentCofffeeArray, $processTransaction->transaction_id);
//                $transactionLog = TransactionLog::create([
//                            'transaction_id' => $processTransaction->transaction_id,
//                            'action' => 'sent',
//                            'created_by' => $receivedTransaction->transaction->created_by,
//                            'entity_id' => $receivedTransaction->transaction->center_id,
//                            'center_name' => '',
//                            'local_created_at' => date("Y-m-d H:i:s", strtotime($receivedTransaction->transaction->created_at)),
//                            'type' => 'coffee_drying',
//                ]);
//                $transactionContainers = $receivedTransaction->transactionDetail;
//                foreach ($transactionContainers as $key => $transactionContainer) {
//                    TransactionDetail::create([
//                        'transaction_id' => $processTransaction->transaction_id,
//                        'container_number' => $transactionContainer->container_number,
//                        'created_by' => $userId,
//                        'is_local' => FALSE,
//                        'container_weight' => $transactionContainer->container_weight,
//                        'weight_unit' => 'kg',
//                        'center_id' => $receivedTransaction->transaction->center_id,
//                        'reference_id' => $receivedTransaction->transaction->reference_id,
//                    ]);
//                    TransactionDetail::where('transaction_id', $receivedTransId)->where('container_number', $transactionContainer->container_number)->update(['container_status' => 1]);
//                }
//                $transactionMeta = $receivedTransaction->transactionMeta;
//                foreach ($transactionMeta as $key => $transactionMe) {
//                    MetaTransation::create([
//                        'transaction_id' => $processTransaction->transaction_id,
//                        'key' => $transactionMe->key,
//                        'value' => $transactionMe->value,
//                    ]);
//                }
//            }
//        }
//        $allTransactions = array();
//        $currentlyReceivedCoffees = Transaction::whereIn('transaction_id', $sentCofffeeArray)->with('transactionDetail', 'log', 'meta')->get();
//        foreach ($currentlyReceivedCoffees as $key => $transaction) {
//            $transactionDetail = $transaction->transactionDetail;
//
//            $transactionDetailArray = array();
//            foreach ($transactionDetail as $key => $transactionDet) {
//                $transactionDet->is_local = FALSE;
//                $transactionDet->update_meta = FALSE;
//                array_push($transactionDetailArray, $transactionDet);
//            }
//            $transactionMeta = $transaction->meta;
//            $transaction->center_id = $transaction->log->entity_id;
//            $transaction->center_name = $transaction->log->center_name;
//            $transaction->is_sent = 0;
//            $transaction->makeHidden('transactionDetail');
//            $transaction->makeHidden('log');
//            $transaction->makeHidden('meta');
//            $data = ['transaction' => $transaction, 'transactionDetail' => $transactionDetailArray, 'transactionMeta' => $transactionMeta];
//            array_push($allTransactions, $data);
//        }
//        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.SENT_COFFEE"), $allTransactions);
//    }
}
