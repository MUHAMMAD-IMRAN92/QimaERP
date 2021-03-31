<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Meta;
use App\Transaction;
use App\TransactionDetail;
use App\TransactionLog;
use Doctrine\DBAL\Driver\IBMDB2\DB2Driver;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MillOperativeController extends Controller
{
    private $app_lang;

    public function __construct(Request $request)
    {
        set_time_limit(0);

        $this->app_lang = $request->header('x-app-lang') ?? 'en';
    }

    public function sendCoffee()
    {
        $transactions = Transaction::where('is_parent', 0)
            ->whereHas('log', function ($q) {
                $q->whereIn('action', ['sent'])
                    ->whereIn('type', ['sent_to_mill']);
            })->whereHas(
                'transactionDetail',
                function ($q) {
                    $q->where('container_status', 0);
                },
                '>',
                0
            )->with(['transactionDetail' => function ($query) {
                $query->where('container_status', 0);
            }])->with('meta', 'child')
            ->orderBy('transaction_id', 'desc')
            ->get();

        $allTransactions = array();

        foreach ($transactions as $transaction) {

            $childTransaction = array();
            $transactionDetail = $transaction->transactionDetail;
            $transaction->center_id = $transaction->log->entity_id;
            $transaction->center_name = $transaction->log->center_name;
            $transactionMata = $transaction->meta;

            $transaction->makeHidden('transactionDetail');
            $transaction->makeHidden('log');
            $transaction->makeHidden('meta');

            $data = [
                'transaction' => $transaction,
                'transactionDetails' => $transactionDetail,
                'transactionMeta' => $transactionMata,
                'child_transactions' => $childTransaction,
            ];

            array_push($allTransactions, $data);
        }

        return sendSuccess(config("statuscodes." . $this->app_lang . ".success_messages.RECV_COFFEE_MESSAGE"), $allTransactions);
    }
    public function receiveCoffee(Request $request)
    {
        $validator = validator::make($request->all(), [
            'transactions' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            return sendError($errors, 400);
        }

        Log::channel('dev')->debug($request->all());
        
        return response()->json([
            'message' => 'request is ok',
            'status' => true,
            'requst_data' => $request->all()
        ], 200);

        // DB::beginTransaction();
        // try {
        //     foreach ($transactions->transaction as $transactionData) {
        //         if ($transactionData->is_local == 'true') {
        //             //do This
        //             $transactions =  Transaction::create([
        //                 'batch_number' => $transactionData->batch_number,
        //                 'is_parent' => $transactionData->is_parent,
        //                 'created_by' => $this->userId,
        //                 'is_local' => FALSE,
        //                 'local_code' => $transactionData->local_code,
        //                 'created_at' => $transactionData->created_at,
        //                 'updated_at' => $transactionData->updated_at,
        //                 'is_mixed' => $transactionData->is_mixed,
        //                 'transaction_type' => $transactionData->transaction_type,
        //                 'reference_id' => $transactionData->reference_id,
        //                 'transaction_status' => 'received',
        //                 'is_new' => 0,
        //                 'sent_to' => $transactionData->sent_to,
        //                 'is_server_id' => 1,
        //                 'is_sent' => $transactionData->is_sent,
        //                 'session_no' => $transactionData->session_no,
        //                 'ready_to_milled' => $transactionData->ready_to_milled,
        //                 'is_in_process' =>$transactionData->is_in_process,
        //                 'is_update_center' => $transactionData->isUpdateCenter,
        //                 'local_session_no' => $transactionData->local_session_no,
        //                 'local_created_at' => toSqlDT($transactionData->local_created_at),
        //                 'local_updated_at' => toSqlDT($transactionData->local_updated_at)
        //             ]);

        //             foreach ($transactionData->details as $detailsData) {
        //                 $transactionDetails = new TransactionDetail();
        //                 $transactionDetails->container_number = $detailsData->container_number;
        //                 $transactionDetails->created_by = $this->userId;
        //                 $transactionDetails->is_local = FALSE;
        //                 // $transactionDetails->local_code = $detailsData->;
        //                 $transactionDetails->updated_at = $detailsData->updated_at;
        //                 $transactionDetails->container_weight = $detailsData->container_weight;
        //                 $transactionDetails->weight_unit = $detailsData->weight_unit;
        //                 $transactionDetails->container_status = $detailsData->container_status;
        //                 $transactionDetails->center_id = $detailsData->center_id;
        //                 $transactionDetails->reference_id = $detailsData->reference_id;
        //                 $transactionDetails->details()->save;
        //                 foreach ($detailsData->meta as $metas) {
        //                     $transactionMetas = new Meta();
        //                     $transactionMetas->key = $metas->key;
        //                     $transactionMetas->value = $metas->value;
        //                     $transactionMetas->created_at = $metas->created_at;
        //                     $transactionMetas->updated_at = $metas->updated_at;
        //                     $transactionMetas->meta()->save;
        //                 }
        //             }
        //         }
        //     }
        //     DB::commit();
        // } catch (\PDOException $e) {
        //     DB::rollback();
        //     return Response::json(array('status' => 'error', 'message' => $e->getMessage(), 'data' => []), 499);
        // }
        // $allTransactions = $transactions;
        // return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.RECV_COFFEE_MESSAGE"), $allTransactions);
    }
}
