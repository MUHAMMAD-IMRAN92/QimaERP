<?php

namespace App\Http\Controllers\API;

use App\Meta;
use Exception;
use Throwable;
use App\Container;
use App\BatchNumber;
use App\Transaction;
use App\CoffeeSession;
use App\MetaTransation;
use App\TransactionLog;
use App\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class UkWareHouse extends Controller
{
    public function __construct(Request $request)
    {
        set_time_limit(0);

        $this->app_lang = $request->header('x-app-lang') ?? 'en';
    }
    public function get()
    {
        $transactions = Transaction::where('is_parent', 0)
            ->whereIn('sent_to', [41, 43, 472, 473])
            ->whereHas(
                'details',
                function ($q) {
                    $q->where('container_status', 0);
                },
                '>',
                0
            )->with(['details' => function ($query) {
                $query->where('container_status', 0)->with('metas');
            }])->with(['meta', 'child'])
            ->orderBy('transaction_id', 'desc')
            ->get();

        $allTransactions = array();


        foreach ($transactions as $transaction) {
            $transaction->center_id = $transaction->log->entity_id;
            $transaction->center_name = $transaction->log->center_name;

            $detailMetas = [];
            $transactionChilds = [];
            $transactionMeta = [];

            foreach ($transaction->details as $detail) {
                foreach ($detail->metas as $meta) {
                    array_push($detailMetas, $meta);
                }

                $detail->makeHidden('metas');
            }

            foreach ($transaction->child as $child) {
                array_push($transactionChilds, $child);
            }
            foreach ($transaction->meta as $metas) {
                array_push($transactionMeta, $metas);
            }

            $transaction->makeHidden('details');
            $transaction->makeHidden('log');
            $transaction->makeHidden('meta');

            $data = [
                'transaction' => $transaction,
                'transactionMeta' =>  $transactionMeta,
                'transactionDetails' => $transaction->details,
                'transactionMeta' => $transaction->meta,
                'detail_metas' => $detailMetas,
                'child_transactions' => $transactionChilds,
            ];

            array_push($allTransactions, $data);
        }

        return sendSuccess(config("statuscodes." . $this->app_lang . ".success_messages.RECV_COFFEE_MESSAGE"), $allTransactions);
    }
    public function post(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transactions' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = implode(',', $validator->errors()->all());
            return sendError($errors, 400);
        }

        $savedTransactions = collect();

        $sessionNo = CoffeeSession::max('server_session_id') + 1;

        DB::beginTransaction();

        try {

            // Start of Transaction saving loop
            foreach ($request->transactions as $transactionObj) {

                $transactionData = $transactionObj['transaction'];
                $detailsData = $transactionObj['details'];
                $transactionMeta =  $transactionObj['transactionMeta'];

                if (isset($transactionData) && $transactionData['is_local']) {

                    $sentTo = $transactionData['sent_to'];

                    if ($sentTo == 43) {

                        $status = 'sent';
                        $type = 'sent_to_UK_Quality';
                        $transactionType = 1;

                        $refenceId = explode(',',  $transactionData->reference_id);

                        if ($transactionData->is_server_id == true) {

                            $parentTransaction = Transaction::whereIn('transaction_id',   $refenceId)->get();

                            if (!$parentTransaction) {
                                throw new Exception('Parent Transaction does not exists');
                            }
                        }

                        $batchCheck = BatchNumber::where('batch_number', $transactionData->batch_number)->exists();

                        if (!$batchCheck) {
                            throw new Exception("Batch Number [{$transactionData->batch_number}] does not exists.");
                        }
                        $transaction =  Transaction::create([
                            'batch_number' => $transactionData->batch_number,
                            'is_parent' => 0,
                            'created_by' => $request->user()->user_id,
                            'is_local' => FALSE,
                            'local_code' => $transactionData->local_code,
                            'is_special' => $parentTransaction->is_special,
                            'is_mixed' => $transactionData->is_mixed,
                            'transaction_type' => 1,
                            'reference_id' => $transactionData->reference_id,
                            'transaction_status' => $status,
                            'is_new' => 0,
                            'sent_to' => 43,
                            'is_server_id' => 1,
                            'is_sent' => $transactionData->is_sent,
                            'session_no' => $sessionNo,
                            'ready_to_milled' => $transactionData->ready_to_milled,
                            'is_in_process' => $transactionData->is_in_process,
                            'is_update_center' => $transactionData->is_update_center,
                            'local_session_no' => $transactionData->local_session_no,
                            'local_created_at' => toSqlDT($transactionData->local_created_at),
                            'local_updated_at' => toSqlDT($transactionData->local_updated_at)
                        ]);
                        // $transaction = Transaction::createAndLog(
                        //     $transactionData,
                        //     $request->user()->user_id,
                        //     $status,
                        //     $sessionNo,
                        //     $type,
                        //     $transactionType
                        // );
                        $referenceTransaction =   Transaction::whereIn('transaction_id',   $refenceId)->get();
                        foreach ($referenceTransaction as $reftransaction) {
                            $reftransaction->update([
                                'is_parent' =>  $transaction->transaction_id,
                            ]);
                        }
                        foreach ($transactionMeta  as $meta) {
                            $transactionMeta = new MetaTransation();
                            $transactionMeta->key = $meta['key'];
                            $transactionMeta->value = $meta['value'];
                            $transactionMeta->local_created_at = $transaction->local_created_at;
                            $transaction->meta()->save($transactionMeta);
                        }

                        $log = new TransactionLog();
                        $log->action = $status;
                        $log->created_by =  $request->user()->user_id;
                        $log->entity_id = $transactionData['center_id'];
                        $log->local_created_at = $transaction->local_created_at;
                        $log->local_updated_at = $transaction->local_updated_at;
                        $log->type =  $type;
                        $log->center_name = array_key_exists('center_name', $transactionData) ? $transactionData['center_name'] : null;

                        $transaction->log()->save($log);

                        // $transactionDetails = TransactionDetail::createFromArray(
                        //     $detailsData,
                        //     $request->user()->user_id,
                        //     $transaction->transaction_id,
                        //     $transaction->reference_id
                        // );
                        foreach ($detailsData as $detailObj) {

                            $detailData = $detailObj['detail'];

                            // Start of finding Conatiner
                            $container = Container::findOrCreate($detailData['container_number'], $request->user()->user_id);
                            $detail = new TransactionDetail();

                            $detail->container_number = $container->container_number;
                            $detail->transaction_id = $transaction->transaction_id;
                            $detail->created_by = $request->user()->user_id;
                            $detail->is_local = FALSE;
                            $detail->container_weight = $detailData['container_weight'];
                            $detail->weight_unit = $detailData['weight_unit'];
                            $detail->center_id = $detailData['center_id'];
                            $detail->reference_id = $detailData['reference_id'];

                            $transaction->log()->save($detail);
                            foreach ($detailObj['metas'] as $metaData) {

                                $meta = new Meta();
                                $meta->key = $metaData['key'];
                                $meta->value = $metaData['value'];
                                $detail->metas()->save($meta);
                            }
                        }
                        TransactionDetail::whereIn('transaction_id', $refenceId)
                            ->where('container_number', $detail->container_number)
                            ->update(['container_status' => 1]);

                        $transaction->load(['details.metas']);
                        $savedTransactions->push($transaction);
                    }
                    if ($sentTo == 473) {

                        $status = 'sent';
                        $type = 'sent_chaina';
                        $transactionType = 1;

                        $transaction = Transaction::createAndLog(
                            $transactionData,
                            $request->user()->user_id,
                            $status,
                            $sessionNo,
                            $type,
                            $transactionType
                        );
                        foreach ($transactionMeta as $meta) {
                            $transactionMeta = new MetaTransation();
                            $transactionMeta->key = $meta['key'];
                            $transactionMeta->value = $meta['value'];
                            $transaction->meta()->save($transactionMeta);
                        }
                        Transaction::where('transaction_id',  $transactionData['reference_id'])->first()
                            ->update([
                                'is_parent' =>  $transaction->transaction_id,
                            ]);

                        $transactionDetails = TransactionDetail::createFromArray(
                            $detailsData,
                            $request->user()->user_id,
                            $transaction->transaction_id,
                            $transaction->reference_id
                        );

                        $transaction->load(['details.metas']);
                        $savedTransactions->push($transaction);
                    }
                }
            }
            // End of Transaction saving loop
            DB::commit();
        } catch (Throwable $th) {
            DB::rollback();
            return Response::json(array('status' => 'error', 'message' => $th->getMessage(), '  data' => [
                'line' => $th->getLine()
            ]), 499);
        }

        // Start of setting session
        if ($savedTransactions->isNotEmpty()) {
            CoffeeSession::create([
                'user_id' => $request->user()->user_id,
                'local_session_id' => $savedTransactions->first()->local_session_no,
                'server_session_id' => $sessionNo
            ]);
        } else {
            $sessionNo--;
        }
        // End of setting session

        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.RECV_COFFEE_MESSAGE"), [
            'session_no' => $sessionNo,
            'transactions' => $savedTransactions,
        ]);
    }
}
