<?php

namespace App\Http\Controllers\API;

use App\CoffeeSession;
use App\Container;
use App\Meta;
use App\Transaction;
use App\TransactionLog;
use App\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\ProductName;
use Exception;
use Illuminate\Support\Arr;
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
                $q->whereIn('action', ['sent', 'received'])
                    ->whereIn('type', ['received_by_mill', 'sent_to_mill', 'sent_to_market', 'sent_to_sorting']);
            })->whereHas(
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

            $transactionDetails = $transaction->details;
            $transaction->center_id = $transaction->log->entity_id;
            $transaction->center_name = $transaction->log->center_name;
            $transactionMata = $transaction->meta;

            $detailMetas = [];
            $transactionChilds = [];

            foreach ($transactionDetails as $detail) {
                foreach ($detail->metas as $meta) {
                    array_push($detailMetas, $meta);
                }

                $detail->makeHidden('metas');
            }

            foreach ($transaction->child as $child) {
                array_push($transactionChilds, $child);
            }

            $transaction->makeHidden('details');
            $transaction->makeHidden('log');
            $transaction->makeHidden('meta');

            $data = [
                'transaction' => $transaction,
                'transactionDetails' => $transactionDetails,
                'transactionMeta' => $transactionMata,
                'detail_metas' => $detailMetas,
                'child_transactions' => $transactionChilds,
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

        $sessionNo = CoffeeSession::max('server_session_id') + 1;

        $savedTransactions = collect();

        DB::beginTransaction();
        try {
            foreach ($request->transactions as $transactionArray) {

                $transactionData = (object) $transactionArray['transaction'];

                if ($transactionData->is_local == true && ($transactionData->sent_to == 17)) {

                    $status = 'received';
                    $type = 'received_by_mill';

                    $transaction =  Transaction::create([
                        'batch_number' => $transactionData->batch_number,
                        'is_parent' => $transactionData->is_parent,
                        'created_by' => $request->user()->user_id,
                        'is_local' => FALSE,
                        'local_code' => $transactionData->local_code,
                        'is_mixed' => $transactionData->is_mixed,
                        'transaction_type' => $transactionData->transaction_type,
                        'reference_id' => $transactionData->reference_id,
                        'transaction_status' => $status,
                        'is_new' => 0,
                        'sent_to' => $transactionData->sent_to,
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

                    $log = new TransactionLog();
                    $log->action = $status;
                    $log->created_by = $request->user()->user_id;
                    $log->entity_id = $transactionData->center_id;
                    $log->local_created_at = $transaction->local_created_at;
                    $log->local_updated_at = $transaction->local_updated_at;
                    $log->type =  $type;
                    $log->center_name = $transactionData->center_name;

                    $transaction->log()->save($log);

                    foreach ($transactionArray['details'] as $detailArray) {

                        $detailData = (object) $detailArray['detail'];

                        $container = Container::where('container_number', $detailData->container_number)->first();

                        if (!$container) {
                            $containerCode = preg_replace('/[0-9]+/', '', $detailData->container_number);

                            $containerDetail = Arr::first(containerType(), function ($detail) use ($containerCode) {
                                return $detail['code'] == $containerCode;
                            });

                            if (!$containerDetail) {
                                throw new Exception('Container type not found.', 400);
                            }

                            $container = new Container();
                            $container->container_number = $detailData->container_number;
                            $container->container_type = $containerDetail['id'];
                            $container->capacity = 100;
                            $container->created_by = $request->user()->user_id;

                            $container->save();
                        }

                        $detail = new TransactionDetail();

                        $detail->container_number = $detailData->container_number;
                        $detail->created_by = $request->user()->user_id;
                        $detail->is_local = FALSE;
                        $detail->container_weight = $detailData->container_weight;
                        $detail->weight_unit = $detailData->weight_unit;
                        $detail->container_status = $detailData->container_status;
                        $detail->center_id = $detailData->center_id;
                        $detail->reference_id = $detailData->reference_id;

                        $transaction->details()->save($detail);

                        TransactionDetail::where('transaction_id', $transaction->reference_id)
                            ->where('container_number', $detail->container_number)
                            ->update(['container_status' => 1]);

                        foreach ($detailArray['metas'] as $metaArray) {
                            $metaData = (object) $metaArray;

                            $meta = new Meta();
                            $meta->key = $metaData->key;
                            $meta->value = $metaData->value;
                            $detail->metas()->save($meta);
                        }
                    }

                    $transaction->load(['details.metas']);

                    $savedTransactions->push($transaction);
                }

                if ($transactionData->is_local == true && ($transactionData->sent_to == 21)) {

                    $marketDetails = [];
                    $sortingDetails = [];

                    foreach ($transactionArray['details'] as $detailArray) {

                        foreach ($detailArray['metas'] as $metaArray) {
                            $metaData = (object) $metaArray;

                            if ($metaData->key == 'product_id') {
                                if (ProductName::marketIds()->contains($metaData->value)) {
                                    array_push($marketDetails, $detailArray);
                                } elseif (ProductName::sortingIds()->contains($metaData->value)) {
                                    array_push($sortingDetails, $detailArray);
                                }
                            }
                        }
                    }

                    if (!empty($marketDetails)) {

                        $status = 'sent';
                        $type = 'sent_to_market';
                        $sent_to = 20;

                        $transaction =  Transaction::create([
                            'batch_number' => $transactionData->batch_number,
                            'is_parent' => $transactionData->is_parent,
                            'created_by' => $request->user()->user_id,
                            'is_local' => FALSE,
                            'local_code' => $transactionData->local_code,
                            'is_mixed' => $transactionData->is_mixed,
                            'transaction_type' => $transactionData->transaction_type,
                            'reference_id' => $transactionData->reference_id,
                            'transaction_status' => $status,
                            'is_new' => 0,
                            'sent_to' => $sent_to,
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

                        $log = new TransactionLog();
                        $log->action = $status;
                        $log->created_by = $request->user()->user_id;
                        $log->entity_id = $transactionData->center_id;
                        $log->local_created_at = $transaction->local_created_at;
                        $log->local_updated_at = $transaction->local_updated_at;
                        $log->type =  $type;
                        $log->center_name = $transactionData->center_name;

                        $transaction->log()->save($log);

                        foreach ($marketDetails as $detailArray) {

                            $detailData = (object) $detailArray['detail'];

                            $container = Container::where('container_number', $detailData->container_number)->first();

                            if (!$container) {
                                $containerCode = preg_replace('/[0-9]+/', '', $detailData->container_number);

                                $containerDetail = Arr::first(containerType(), function ($detail) use ($containerCode) {
                                    return $detail['code'] == $containerCode;
                                });

                                if (!$containerDetail) {
                                    throw new Exception('Container type not found.', 400);
                                }

                                $container = new Container();
                                $container->container_number = $detailData->container_number;
                                $container->container_type = $containerDetail['id'];
                                $container->capacity = 100;
                                $container->created_by = $request->user()->user_id;

                                $container->save();
                            }

                            $detail = new TransactionDetail();

                            $detail->container_number = $detailData->container_number;
                            $detail->created_by = $request->user()->user_id;
                            $detail->is_local = FALSE;
                            $detail->container_weight = $detailData->container_weight;
                            $detail->weight_unit = $detailData->weight_unit;
                            $detail->container_status = $detailData->container_status;
                            $detail->center_id = $detailData->center_id;
                            $detail->reference_id = $detailData->reference_id;

                            $transaction->details()->save($detail);

                            foreach ($detailArray['metas'] as $metaArray) {
                                $metaData = (object) $metaArray;

                                $meta = new Meta();
                                $meta->key = $metaData->key;
                                $meta->value = $metaData->value;
                                $detail->metas()->save($meta);
                            }
                        }

                        $transaction->load(['details.metas']);

                        $savedTransactions->push($transaction);
                    }

                    if (!empty($sortingDetails)) {
                        $status = 'sent';
                        $type = 'sent_to_sorting';
                        $sent_to = 21;


                        $transaction =  Transaction::create([
                            'batch_number' => $transactionData->batch_number,
                            'is_parent' => $transactionData->is_parent,
                            'created_by' => $request->user()->user_id,
                            'is_local' => FALSE,
                            'local_code' => $transactionData->local_code,
                            'is_mixed' => $transactionData->is_mixed,
                            'transaction_type' => $transactionData->transaction_type,
                            'reference_id' => $transactionData->reference_id,
                            'transaction_status' => $status,
                            'is_new' => 0,
                            'sent_to' => $sent_to,
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

                        $log = new TransactionLog();
                        $log->action = $status;
                        $log->created_by = $request->user()->user_id;
                        $log->entity_id = $transactionData->center_id;
                        $log->local_created_at = $transaction->local_created_at;
                        $log->local_updated_at = $transaction->local_updated_at;
                        $log->type =  $type;
                        $log->center_name = $transactionData->center_name;

                        $transaction->log()->save($log);

                        foreach ($sortingDetails as $detailArray) {

                            $detailData = (object) $detailArray['detail'];

                            $container = Container::where('container_number', $detailData->container_number)->first();

                            if (!$container) {
                                $containerCode = preg_replace('/[0-9]+/', '', $detailData->container_number);

                                $containerDetail = Arr::first(containerType(), function ($detail) use ($containerCode) {
                                    return $detail['code'] == $containerCode;
                                });

                                if (!$containerDetail) {
                                    throw new Exception('Container type not found.', 400);
                                }

                                $container = new Container();
                                $container->container_number = $detailData->container_number;
                                $container->container_type = $containerDetail['id'];
                                $container->capacity = 100;
                                $container->created_by = $request->user()->user_id;

                                $container->save();
                            }

                            $detail = new TransactionDetail();

                            $detail->container_number = $detailData->container_number;
                            $detail->created_by = $request->user()->user_id;
                            $detail->is_local = FALSE;
                            $detail->container_weight = $detailData->container_weight;
                            $detail->weight_unit = $detailData->weight_unit;
                            $detail->container_status = $detailData->container_status;
                            $detail->center_id = $detailData->center_id;
                            $detail->reference_id = $detailData->reference_id;

                            $transaction->details()->save($detail);

                            foreach ($detailArray['metas'] as $metaArray) {
                                $metaData = (object) $metaArray;

                                $meta = new Meta();
                                $meta->key = $metaData->key;
                                $meta->value = $metaData->value;
                                $detail->metas()->save($meta);
                            }
                        }

                        $transaction->load(['details.metas']);

                        $savedTransactions->push($transaction);
                    }
                }
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return Response::json(array('status' => 'error', 'message' => $e->getMessage(), 'data' => []), 499);
        }

        if ($savedTransactions->last()) {
            CoffeeSession::create([
                'user_id' => $request->user()->user_id,
                'local_session_id' => $savedTransactions->last()->local_session_no,
                'server_session_id' => $sessionNo
            ]);
        } else {
            $sessionNo--;
        }

        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.RECV_COFFEE_MESSAGE"), [
            'session_no' => $sessionNo,
            'transactions' => $savedTransactions,

        ]);
    }
}
