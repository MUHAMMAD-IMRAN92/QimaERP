<?php

namespace App\Http\Controllers\API;

use App\User;
use App\Center;
use App\Farmer;
use App\LoginUser;
use Carbon\Carbon;
use App\CenterUser;
use App\BatchNumber;
use App\Transaction;
use App\TransactionLog;
use App\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Throwable;

class CenterManagerController extends Controller
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

    function receivedTransactions(Request $request)
    {
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
            DB::beginTransaction();

            try {
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
                        'session_no' => $sentTransaction->transaction->session_no,
                        'local_created_at' => Carbon::parse($sentTransaction->transaction->local_created_at)->toDateTimeString(),
                        'local_updated_at' => Carbon::parse($sentTransaction->transaction->local_updated_at)->toDateTimeString()
                    ]);
                    $transactionLog = TransactionLog::create([
                        'transaction_id' => $transaction->transaction_id,
                        'action' => 'received',
                        'created_by' => $sentTransaction->transaction->created_by,
                        'entity_id' => $sentTransaction->transaction->center_id,
                        'center_name' => $sentTransaction->transaction->center_name,
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
                            'reference_id' => $transactionContainer->reference_id,
                        ]);

                        TransactionDetail::where('transaction_id', $transactionContainer->reference_id)->where('container_number', $transactionContainer->container_number)->update(['container_status' => 1]);
                    }
                    array_push($reciviedCoffee, $transaction->transaction_id);
                    DB::commit();
                }
            } catch (Throwable $e) {
                DB::rollback();

                return Response::json(array('status' => 'error', 'message' => $e->getMessage(), 'data' => []), 499);
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
            $currentlyReceivedCoffee->makeHidden('transactionDetail');
            $currentlyReceivedCoffee->center_name = $transaction->log->center_name;
            $currentlyReceivedCoffee->already_received = FALSE;
            $currentlyReceivedCoffee->makeHidden('log');
            $recCoffee = ['transaction' => $currentlyReceivedCoffee, 'transactionDetails' => $transactionDeatil];
            array_push($dataArray, $recCoffee);
        }
        $data = array_merge($dataArray, $alreadyReciviedCoffee);
        if (count($alreadyReciviedCoffee) > 0) {
            return sendSuccess(Config("statuscodes." . $this->app_lang . ".error_messages.TRANSACTION_REC_ALREADY"), $data);
        }
        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.RECV_COFFEE"), $data);
    }

    function centerManagerCoffee(Request $request)
    {
        $userCenter = CenterUser::where('user_id', $this->userId)->first();

        $centerId = 0;

        if ($userCenter) {
            $centerId = $userCenter->center_id;
        }

        $allTransactions = array();

        $transactions = Transaction::where('is_parent', 0)
            ->where('transaction_status', 'sent')
            ->whereHas('transactionLog', function ($q) use ($centerId) {
                $q->where('action', 'sent')
                    ->where('type', 'center')
                    ->where('entity_id', $centerId);
            })->whereHas('transactionDetail', function ($q) use ($centerId) {
                $q->where('container_status', 0);
            }, '>', 0)->with(['transactionDetail' => function ($query) {
                $query->where('container_status', 0);
            }])->with('log')->orderBy('transaction_id', 'desc')->get();

        foreach ($transactions as $key => $transaction) {

            $transaction->buyer_name = '';
            $parentCheckBatch = BatchNumber::where('batch_number', $transaction->batch_number)
                ->with('buyer')->first();

            if ($parentCheckBatch && isset($parentCheckBatch->buyer)) {
                $transaction->buyer_name = $parentCheckBatch->buyer->first_name . ' ' . $parentCheckBatch->buyer->last_name;
            }

            $transactionDetail = $transaction->transactionDetail;
            $transaction->center_id = $transaction->log->entity_id;
            $transaction->center_name = $transaction->log->center_name;
            $transaction->makeHidden('transactionDetail');
            $transaction->makeHidden('childTransation');
            $transaction->makeHidden('log');

            $data = ['transaction' => $transaction, 'transactionDetails' => $transactionDetail];
            array_push($allTransactions, $data);
        }
        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.CENTER_MANAGER_RECV_COFFEE"), $allTransactions);
    }

    function centerManagerReceivedCoffee(Request $request)
    {
        $userId = $this->userId;
        $centerId = 0;
        $userCenter = CenterUser::where('user_id', $this->userId)->first();
        if ($userCenter) {
            $centerId = $userCenter->center_id;
        }
        $allTransactions = array();
        $transactions = Transaction::where('is_parent', 0)->where('created_by', $userId)->where('transaction_status', 'received')->whereHas('transactionLog', function ($q) use ($centerId) {
            $q->where('action', 'received')->where('type', 'center')->where('entity_id', $centerId);
        })->with('transactionDetail')->with('log')->orderBy('transaction_id', 'desc')->get();

        foreach ($transactions as $key => $transaction) {
            $transaction->buyer_name = '';
            $parentCheckBatch = BatchNumber::where('batch_number', $transaction->batch_number)->with('buyer')->first();
            if ($parentCheckBatch && isset($parentCheckBatch->buyer)) {
                $transaction->buyer_name = $parentCheckBatch->buyer->first_name . ' ' . $parentCheckBatch->buyer->last_name;
            }
            $transactionDetail = $transaction->transactionDetail;
            $transaction->center_id = $transaction->log->entity_id;
            $transaction->center_name = $transaction->log->center_name;
            $transaction->makeHidden('transactionDetail');
            $transaction->makeHidden('childTransation');
            $transaction->makeHidden('log');

            $data = ['transaction' => $transaction, 'transactionDetails' => $transactionDetail];
            array_push($allTransactions, $data);
        }
        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.CENTER_MANAGER_RECV_COFFEE"), $allTransactions);
    }
}
