<?php

namespace App\Http\Controllers\API;

use App\Meta;
use App\Transaction;
use App\TransactionLog;
use App\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

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
                'details',
                function ($q) {
                    $q->where('container_status', 0);
                },
                '>',
                0
            )->with(['details' => function ($query) {
                $query->where('container_status', 0)->with('metas');
            }])->with('meta')
            ->orderBy('transaction_id', 'desc')
            ->get();

        $allTransactions = array();


        foreach ($transactions as $transaction) {

            $transactionDetails = $transaction->details;
            $transaction->center_id = $transaction->log->entity_id;
            $transaction->center_name = $transaction->log->center_name;
            $transactionMata = $transaction->meta;

            $detailMetas = [];

            foreach ($transactionDetails as $detail) {
                foreach ($detail->metas as $meta) {
                    array_push($detailMetas, $meta);
                }

                $detail->makeHidden('metas');
            }

            $transaction->makeHidden('details');
            $transaction->makeHidden('log');
            $transaction->makeHidden('meta');

            $data = [
                'transaction' => $transaction,
                'transactionDetails' => $transactionDetails,
                'transactionMeta' => $transactionMata,
                'detail_metas' => $detailMetas
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


        DB::beginTransaction();
        try {
            foreach ($request->transactions as $transactionData) {

                $transaction = $transactionData->transaction;

           

                if ($transaction->is_local == TRUE && ($transaction->is_sent == 17 || $transaction->is_sent == 21)) {

                    if ($transaction->is_sent == 17) {
                        $status = 'received';
                        $type = 'received_by_mill';
                    } elseif ($transaction->is_sent == 21) {
                        $status = 'created';
                        $type = 'sent_to_market';
                    }

                    $transaction =  Transaction::create([
                        'batch_number' => $transaction->batch_number,
                        'is_parent' => $transaction->is_parent,
                        'created_by' => $this->userId,
                        'is_local' => FALSE,
                        'local_code' => $transaction->local_code,
                        'is_mixed' => $transaction->is_mixed,
                        'transaction_type' => $transaction->transaction_type,
                        'reference_id' => $transaction->reference_id,
                        'transaction_status' => $status,
                        'is_new' => 0,
                        'sent_to' => $transaction->sent_to,
                        'is_server_id' => 1,
                        'is_sent' => $transaction->is_sent,
                        'session_no' => $transaction->session_no,
                        'ready_to_milled' => $transaction->ready_to_milled,
                        'is_in_process' => $transaction->is_in_process,
                        'is_update_center' => $transaction->isUpdateCenter,
                        'local_session_no' => $transaction->local_session_no,
                        'local_created_at' => toSqlDT($transaction->local_created_at),
                        'local_updated_at' => toSqlDT($transaction->local_updated_at)
                    ]);

                    $log = new TransactionLog();
                    $log->action = $status;
                    $log->created_by = $this->userId;
                    $log->entity_id = $transaction->center_id;
                    $log->local_created_at = toSqlDT($transaction->local_created_at);
                    $log->local_updated_at = toSqlDT($transaction->local_updated_at);
                    $log->type =  $type;
                    $log->center_name = $transaction->center_name;

                    $transaction->log()->save($log);

                    foreach ($transaction->details as $detailData) {
                        $detail = new TransactionDetail();

                        $detail->container_number = $detailData->container_number;
                        $detail->created_by = $this->userId;
                        $detail->is_local = FALSE;
                        $detail->container_weight = $detailData->container_weight;
                        $detail->weight_unit = $detailData->weight_unit;
                        $detail->container_status = $detailData->container_status;
                        $detail->center_id = $detailData->center_id;
                        $detail->reference_id = $detailData->reference_id;

                        $transaction->details()->save($detail);

                        foreach ($detailData->metas as $metaData) {
                            $meta = new Meta();
                            $meta->key = $metaData->key;
                            $meta->value = $metaData->value;
                            $detail->metas()->save($meta);
                        }
                    }
                }
            }
            DB::commit();
        } catch (\PDOException $e) {
            DB::rollback();
            return Response::json(array('status' => 'error', 'message' => $e->getMessage(), 'data' => []), 499);
        }
        $allTransactions = $transactions;
        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.RECV_COFFEE_MESSAGE"), $allTransactions);
    }
}
