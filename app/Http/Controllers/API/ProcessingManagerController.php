<?php

namespace App\Http\Controllers\API;

use App\User;
use Throwable;
use App\LoginUser;
use Carbon\Carbon;
use App\CenterUser;
use App\BatchNumber;
use App\CoffeeSession;
use App\Transaction;
use App\TransactionLog;
use App\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Season;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class ProcessingManagerController extends Controller
{

    private $userId;
    private $user;
    private $app_lang;

    public function __construct(Request $request)
    {
        set_time_limit(0);

        $this->app_lang = $request->header('x-app-lang') ?? 'en';

        $this->middleware(function ($request, $next) {
            $this->user = $request->user();
            $this->userId = $request->user()->user_id;

            return $next($request);
        });
    }

    function getProcessingManager(Request $request)
    {
        $userId = $this->userId;
        $centerId = 0;
        $userCenter = CenterUser::where('user_id', $this->userId)->first();
        if ($userCenter) {
            $centerId = $userCenter->center_id;
        }
        $allTransactions = array();
        $transactions = Transaction::where('is_parent', 0)->where('transaction_status', 'received')->whereHas('log', function ($q) use ($centerId) {
            $q->where('action', 'received')->where('type', 'center')->where('entity_id', $centerId);
        })->doesntHave('isReference')->with(['transactionDetail' => function ($query) {
            $query->where('container_status', 0);
        }])->whereHas('transactionDetail', function ($q) {
            $q->where('container_status', 0);
        }, '>', 0)->orderBy('transaction_id', 'desc')->get();

        foreach ($transactions as $key => $transaction) {
            $transaction->buyer_name = '';
            $parentCheckBatch = BatchNumber::where('batch_number', $transaction->batch_number)->with('buyer')->first();
            if ($parentCheckBatch && isset($parentCheckBatch->buyer)) {
                $transaction->buyer_name = $parentCheckBatch->buyer->first_name . ' ' . $parentCheckBatch->buyer->last_name;
            }

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

    function fetchProcessorRole(Request $request)
    {
        $userId = $this->userId;
        $centerId = 0;
        $userCenter = CenterUser::where('user_id', $this->userId)->first();
        if ($userCenter) {
            $centerId = $userCenter->center_id;
        }
        $role = Role::whereIn('name', ['Special Processing', 'Coffee Drying'])->select('name')->get();
        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.ROLE"), $role);
    }

    function sentToSpecialProcessingAndCoffeeDrying(Request $request)
    {
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
            $alreadySent = Transaction::where('reference_id', $sentTransaction->transaction->reference_id)->first();
            if ($alreadySent) {
                $alreadySent->created_at =  toSqlDT($alreadySent->created_at);
                $alreadySent->local_created_at = toSqlDT($alreadySent->local_created_at);
                $alreadySent->local_updated_at = toSqlDT($alreadySent->local_updated_at);
                $sentTransaction->transaction->created_at =  toSqlDT($sentTransaction->transaction->created_at);
                $sentTransaction->transaction->local_created_at = toSqlDT($sentTransaction->transaction->local_created_at);
                $sentTransaction->transaction->local_updated_at = toSqlDT($sentTransaction->transaction->local_updated_at);
                foreach ($sentTransaction->transactionDetails as $detail) {
                    $detail->created_at = toSqlDT($detail->created_at);
                }
                array_push($alreadyReciviedCoffee, $sentTransaction);
            } else {

                //            $transationRef = array();
                //            foreach ($sentTransaction->transactionDetails as $key => $transactionDetailsvalue) {
                //                if (!in_array($transactionDetailsvalue->reference_id, $transationRef)) {
                //                    array_push($transationRef, $transactionDetailsvalue->reference_id);
                //                }
                //            }
                DB::beginTransaction();
                try {
                    if (isset($sentTransaction->transaction) && $sentTransaction->transaction) {
                        $isSpecial = false;
                        if ($sentTransaction->transaction->sent_to == 5) {
                            $type = 'special_processing';
                            $isSpecial = true;
                        } else {
                            $type = 'coffee_drying';
                        }

                        // $transaction = Transaction::create([
                        //     'batch_number' => $sentTransaction->transaction->batch_number,
                        //     'is_parent' => $sentTransaction->transaction->is_parent,
                        //     'is_mixed' => $sentTransaction->transaction->is_mixed,
                        //     'created_by' => $sentTransaction->transaction->created_by,
                        //     'is_local' => FALSE,
                        //     'is_special' => $isSpecial,
                        //     'transaction_type' => 2,
                        //     'local_code' => $sentTransaction->transaction->local_code,
                        //     'transaction_status' => 'sent',
                        //     // 'reference_id' => implode(",", $transationRef),
                        //     'reference_id' => $sentTransaction->transaction->reference_id,
                        //     'is_server_id' => 1,
                        //     'is_new' => 0,
                        //     'sent_to' => $sentTransaction->transaction->sent_to,
                        //     'is_sent' => 1,
                        //     'session_no' => $sentTransaction->transaction->session_no,
                        //     'local_created_at' => toSqlDT($sentTransaction->transaction->local_created_at),
                        //     'local_updated_at' => toSqlDT($sentTransaction->transaction->local_updated_at)
                        // ]);
                        $savedtransaction = new Transaction();
                        $savedtransaction->batch_number =  $sentTransaction->transaction->batch_number;
                        $savedtransaction->is_parent =  $sentTransaction->transaction->is_parent;
                        $savedtransaction->is_mixed =  $sentTransaction->transaction->is_mixed;
                        $savedtransaction->created_by =  $sentTransaction->transaction->created_by;
                        $savedtransaction->is_local = FALSE;
                        $savedtransaction->is_special = $isSpecial;
                        $savedtransaction->transaction_type = 2;
                        $savedtransaction->local_code =  $sentTransaction->transaction->local_code;
                        $savedtransaction->transaction_status = 'sent';
                        $savedtransaction->reference_id =  $sentTransaction->transaction->reference_id;
                        $savedtransaction->is_server_id = 1;
                        $savedtransaction->is_new = 0;
                        $savedtransaction->sent_to =  $sentTransaction->transaction->sent_to;
                        $savedtransaction->is_sent = 1;
                        $savedtransaction->session_no =  $sentTransaction->transaction->session_no;
                        $savedtransaction->local_created_at = toSqlDT($sentTransaction->transaction->local_created_at);
                        $savedtransaction->local_updated_at =   toSqlDT($sentTransaction->transaction->local_updated_at);

                        // $sentTransaction->update([
                        //     'is_parent' => $savedtransaction->transaction_id,
                        // ]);

                        $transactionContainers = $sentTransaction->transactionDetails;
                        $count = 0;
                        foreach ($transactionContainers as $key => $transactionContainer) {
                            $count++;
                            if ($count == 1) {
                                $savedtransaction->save();
                                $transactionLog = TransactionLog::create([
                                    'transaction_id' => $savedtransaction->transaction_id,
                                    'action' => 'sent',
                                    'created_by' => $this->userId,
                                    'entity_id' => $sentTransaction->transaction->center_id,
                                    'center_name' => $sentTransaction->transaction->center_name,
                                    'local_created_at' => date("Y-m-d H:i:s", strtotime($sentTransaction->transaction->created_at)),
                                    'type' => $type,
                                ]);
                            }
                            TransactionDetail::create([
                                'transaction_id' => $savedtransaction->transaction_id,
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
                        array_push($reciviedCoffee, $savedtransaction->transaction_id);
                    }
                    DB::commit();
                } catch (Throwable $th) {
                    DB::rollback();
                    return Response::json(array('status' => 'error', 'message' => $th->getMessage(), '  data' => [
                        'line' => $th->getLine()
                    ]), 499);
                }
            }
        }
        $currentlyReceivedCoffees = Transaction::whereIn('transaction_id', $reciviedCoffee)->with('transactionDetail', 'log')->get();
        $dataArray = array();
        foreach ($currentlyReceivedCoffees as $key => $currentlyReceivedCoffee) {
            $currentlyReceivedCoffee->buyer_name = '';
            $parentCheckBatch = BatchNumber::where('batch_number', $currentlyReceivedCoffee->batch_number)->with('buyer')->first();
            if ($parentCheckBatch && isset($parentCheckBatch->buyer)) {
                $currentlyReceivedCoffee->buyer_name = $parentCheckBatch->buyer->first_name . ' ' . $parentCheckBatch->buyer->last_name;
            }


            $transactionDeatil = $currentlyReceivedCoffee->transactionDetail;
            foreach ($currentlyReceivedCoffee->transactionDetail as $detail) {
                $detail->created_at = toSqlDT($detail->created_at);
            }
            $currentlyReceivedCoffee->makeHidden('transactionDetail');
            $currentlyReceivedCoffee->center_name = $currentlyReceivedCoffee->log->center_name;
            $currentlyReceivedCoffee->already_received = FALSE;
            $currentlyReceivedCoffee->makeHidden('log');
            $recCoffee = ['transaction' => $currentlyReceivedCoffee, 'transactionDetails' => $transactionDeatil];
            array_push($dataArray, $recCoffee);
        }
        $data = array_merge($dataArray, $alreadyReciviedCoffee);
        if (count($alreadyReciviedCoffee) > 0) {
            return sendSuccess(Config("statuscodes." . $this->app_lang . ".error_messages.TRANSACTION_SENT_ALREADY"), $data);
        }
        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.SENT_COFFEE"), $data);
    }

    function getSendSpecialProcessingAndDryingCoffee(Request $request)
    {
        $userId = $this->userId;
        $centerId = 0;
        $userCenter = CenterUser::where('user_id', $this->userId)->first();
        if ($userCenter) {
            $centerId = $userCenter->center_id;
        }
        $allTransactions = array();
        $transactions = Transaction::where('created_by', $userId)->where('transaction_status', 'sent')->whereHas('log', function ($q) use ($centerId) {
            $q->where('action', 'sent')->whereIn('type', ['special_processing', 'coffee_drying'])->where('entity_id', $centerId);
        })->with('transactionDetail')->orderBy('transaction_id', 'desc')->get();

        foreach ($transactions as $key => $transaction) {

            $transaction->buyer_name = '';
            $parentCheckBatch = BatchNumber::where('batch_number', $transaction->batch_number)->with('buyer')->first();
            if ($parentCheckBatch && isset($parentCheckBatch->buyer)) {
                $transaction->buyer_name = $parentCheckBatch->buyer->first_name . ' ' . $parentCheckBatch->buyer->last_name;
            }
            $transactionDetail = $transaction->transactionDetail;
            $transaction->center_id = $transaction->log->entity_id;
            $transaction->center_name = $transaction->log->center_name;
            $transaction->is_sent = 1;
            $transaction->makeHidden('transactionDetail');
            $transaction->makeHidden('log');
            $data = ['transaction' => $transaction, 'transactionDetails' => $transactionDetail];
            array_push($allTransactions, $data);
        }
        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.RECV_COFFEE_MESSAGE"), $allTransactions);
    }

    function getSendCoffeeDrying(Request $request)
    {
        $userId = $this->userId;
        $centerId = 0;
        $userCenter = CenterUser::where('user_id', $this->userId)->first();
        if ($userCenter) {
            $centerId = $userCenter->center_id;
        }
        $allTransactions = array();
        $transactions = Transaction::where('created_by', $userId)->where('transaction_status', 'sent')->whereHas('log', function ($q) use ($centerId) {
            $q->where('action', 'sent')->where('type', 'coffee_drying')->where('entity_id', $centerId);
        })->whereHas('transactionDetail', function ($q) use ($centerId) {
            $q->where('container_status', 1);
        }, '>', 0)->with(['transactionDetail' => function ($query) {
            $query->where('container_status', 1);
        }])->orderBy('transaction_id', 'desc')->get();

        foreach ($transactions as $key => $transaction) {
            $transaction->buyer_name = '';
            $parentCheckBatch = BatchNumber::where('batch_number', $transaction->batch_number)->with('buyer')->first();
            if ($parentCheckBatch && isset($parentCheckBatch->buyer)) {
                $transaction->buyer_name = $parentCheckBatch->buyer->first_name . ' ' . $parentCheckBatch->buyer->last_name;
            }

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

    public function post(Request $request)
    {
        try {
            $data = collect();
            $transactions = $request->transactions;
            $user = auth()->user();
            foreach ($transactions as $transaction) {
                $transactionObj = $transaction['transaction'];
                if ($transactionObj['sent_to'] == 4) {
                    $sessionNo =  $sessionNo = CoffeeSession::max('server_session_id') + 1;
                    if ($transactionObj['sent_to'] == 5) {
                        $type = 'special_processing';
                        $isSpecial = true;
                    } else {
                        $isSpecial = 1; //change from  parent
                        $type = 'coffee_drying';
                    }
                    $removeLocalId = explode("-", $transactionObj['batch_number']);
                    array_pop($removeLocalId);
                    $season = Season::where('status', 0)->first();
                    $lastBatchNumber = BatchNumber::orderBy('batch_id', 'desc')->first();
                    if ($lastBatchNumber) {
                        $newLastBID = ($lastBatchNumber->batch_id + 1);
                    }

                    $parentBatchCode = implode("-", $removeLocalId) . '-' . ($newLastBID);
                    $parentBatch = BatchNumber::create([
                        'batch_number' => $parentBatchCode,
                        'is_parent' => 0,
                        'is_mixed' => $lastBatchNumber->is_mixed,
                        'created_by' => $lastBatchNumber->created_by,
                        'is_local' => FALSE,
                        'season_no' =>  $lastBatchNumber->season_no,
                        'local_code' => $lastBatchNumber->local_code,
                        'is_server_id' => $lastBatchNumber->is_server_id,
                        'season_id' => $season->season_id,
                        'season_status' => $season->status,
                    ]);
                    $result = Transaction::create([
                        'batch_number' => $parentBatchCode,
                        'is_parent' => 0,
                        'created_by' =>  $user->user_id,
                        'is_local' => FALSE,
                        'local_code' => $transactionObj['local_code'],
                        'is_special' => $isSpecial,
                        'is_mixed' => $transactionObj['is_mixed'],
                        'transaction_type' => 0,
                        'reference_id' => 000, //change from  parent
                        'transaction_status' => 'minxed',
                        'is_new' => 0,
                        'sent_to' => $sentTo ?? $transactionObj['sent_to'],
                        'is_server_id' => true,
                        'is_sent' => $transactionObj['is_sent'],
                        'session_no' => $transactionObj['session_no'],
                        'ready_to_milled' => 0,
                        'is_in_process' => 0,
                        'is_update_center' => array_key_exists('is_update_center', $transactionObj) ? $transactionObj['is_update_center'] : false,
                        'local_session_no' => array_key_exists('local_session_no', $transactionObj) ? $transactionObj['local_session_no'] : false,
                        'local_created_at' => toSqlDT($transactionObj['local_created_at']),
                        'local_updated_at' => toSqlDT($transactionObj['local_updated_at'])
                    ]);
                    $transactionLog = TransactionLog::create([
                        'transaction_id' => $result->transaction_id,
                        'action' => 'sent', ///
                        'created_by' => $user->user_id,
                        'entity_id' => $transactionObj['center_id'],
                        'center_name' => "",
                        'local_created_at' => date("Y-m-d H:i:s", strtotime($transactionObj['created_at'])),
                        'type' => $type,
                    ]);
                    $ref_local_code =    explode(",", $transactionObj['ref_local_code']);
                    foreach ($ref_local_code as $rlc) {
                        if (Str::contains($rlc, '-')) {
                            $transaction = Transaction::where('local_code', 'LIKE', "%$rlc%")->first();
                            $transaction->update([
                                'is_parent' => $result->transaction_id,
                            ]);
                            $result->update([
                                'reference_id' => $result->reference_id . ',' . $transaction->transaction_id
                            ]);
                        } else {
                            $transaction = Transaction::where('transaction_id', $rlc)->first();
                            $transaction->update([
                                'is_parent' => $result->transaction_id,
                            ]);
                            $result->update([
                                'reference_id' => $result->reference_id . ',' . $transaction->transaction_id
                            ]);
                        }
                    }


                    foreach ($transaction['transactionDetails'] as $key => $detail) {
                        TransactionDetail::create([
                            'transaction_id' => $result->transaction_id,
                            'container_number' => $detail['container_number'],
                            'created_by' => $detail['created_by'],
                            'is_local' => FALSE,
                            'container_weight' => $detail['container_weight'],
                            'weight_unit' => $detail['weight_unit'],
                            'center_id' => $detail['center_id'],
                            'reference_id' => $detail['reference_id'],
                        ]);

                        TransactionDetail::where('transaction_id', $detail['reference_id'])->where('container_number', $detail['container_number'])->update(['container_status' => 1]);
                    }
                }
            }
            return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.RECV_COFFEE_MESSAGE"), [
                $data
            ]);
        } catch (Throwable $th) {
            DB::rollback();
            return Response::json(array('status' => 'error', 'message' => $th->getMessage(), '  data' => [
                'line' => $th->getLine()
            ]), 499);
        }
    }
}
