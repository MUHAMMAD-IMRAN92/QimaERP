<?php

namespace App\Http\Controllers\API;

use App\Order;
use Exception;
use Throwable;
use App\Container;
use App\BatchNumber;
use App\Transaction;
use App\CoffeeSession;
use App\TransactionLog;
use App\TransactionDetail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class YOLocalMarketController extends Controller
{
    private $normalProductBatchNumbers = [
        1 => 'GR1-HSK-00',
        2 => 'GR2-HSK-00',
        3 => 'GR3-HSK-00',
        4 => 'GR2-CFE-00',
        5 => 'GR2-CFE-00'
    ];

    private $specialProductBatchNumbers = [
        1 => 'SGR1-HSK-00',
        2 => 'SGR2-HSK-00',
        3 => 'SGR3-HSK-00',
        4 => 'SGR2-CFE-00',
        5 => 'SGR2-CFE-00'
    ];

    private $fixedBatchNumbers = [
        'GR1-HSK-00',
        'GR2-HSK-00',
        'GR3-HSK-00',
        'GR2-CFE-00',
        'GR3-CFE-00',
        'SGR1-HSK-00',
        'SGR2-HSK-00',
        'SGR3-HSK-00',
        'SGR2-CFE-00',
        'SGR3-CFE-00',
    ];

    public function __construct(Request $request)
    {
        set_time_limit(0);

        $this->app_lang = $request->header('x-app-lang') ?? 'en';
    }

    public function getCoffee()
    {
        $transactions = Transaction::where('is_parent', 0)
            ->whereIn('sent_to', [20, 193, 194, 195])
            ->whereIn('transaction_type', [1, 3, 5])
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

            $transactionDetails = $transaction->details;
            // $transaction->center_id = $transaction->log->entity_id;
            // $transaction->center_name = $transaction->log->center_name;
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

        $orders = Order::with('details')
            ->whereIn('status', [1, 2])
            ->where('is_sent', false)
            ->get()
            ->map(function ($order) {
                $details = $order->details;
                $order->makeHidden('details');
                //transaction with

                $transactions = Transaction::with('details')->where('batch_number', $order->order_number)->whereIn('sent_to', [194, 195])->get();
                $transactionWeight = 0;

                if ($transactions->count() > 0) {
                    foreach ($transactions as $transaction) {
                        $transactionWeight += $transaction->details->sum('container_weight');
                    }
                }
                // return  $transactionWeight;
                $orderWeight = $details->sum('weight');
                $newWeight =  $orderWeight - $transactionWeight;

                foreach ($details as $detail) {
                    $detail->weight = $newWeight;
                    $detail->actual_weight = $orderWeight;
                    $detail->status = $order->status;
                }

                return [
                    'order' => $order,
                    'details' => $details,
                ];
            });

        return sendSuccess(config("statuscodes." . $this->app_lang . ".success_messages.RECV_COFFEE_MESSAGE"), [
            'transactions' => $allTransactions,
            'orders' => $orders
        ]);
    }


    public function sendCoffee(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transactions' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            return sendError($errors, 400);
        }

        $savedTransactions = collect();

        $sessionNo = CoffeeSession::max('server_session_id') + 1;

        DB::beginTransaction();

        try {
            foreach ($request->transactions as $transactionObj) {
                $transactionData = $transactionObj['transaction'];
                $detailsData = $transactionObj['details'];

                if (isset($transactionData) && $transactionData['is_local'] && $transactionData['sent_to'] == 191) {
                    $status = 'received';
                    $type = 'received_by_yo_local_market';
                    $transactionType = 1;
                    $sentTo = 191;

                    $transaction = Transaction::createAndLog(
                        $transactionData,
                        $request->user()->user_id,
                        $status,
                        $sessionNo,
                        $type,
                        $transactionType,
                        $sentTo
                    );

                    $transactionDetails = TransactionDetail::createFromArray(
                        $detailsData,
                        $request->user()->user_id,
                        $transaction->transaction_id,
                        $transaction->reference_id
                    );

                    $transaction->load('details');

                    $savedTransactions->push($transaction);

                    $transactionBatchNumberPrefix = Str::beforeLast($transaction->batch_number, '-');

                    if (!in_array($transactionBatchNumberPrefix, $this->fixedBatchNumbers)) {
                        throw new Exception('Wrong batch number for this endpoint');
                    }

                    $accumulatedTransaction = Transaction::with('details')->where('batch_number', $transactionBatchNumberPrefix)
                        ->where('is_parent', 0)
                        ->where('transaction_type', 5)
                        ->first();

                    $oldBatch = BatchNumber::where('batch_number', $transaction->batch_number)->first();

                    $accumulatedBatch = BatchNumber::firstOrCreate(
                        ['batch_number' => $transactionBatchNumberPrefix],
                        [
                            'created_by' => $request->user()->user_id,
                            'local_code' => $transactionBatchNumberPrefix,
                            'is_server_id' => 1,
                            'season_id' => $oldBatch->season_id,
                            'season_status' => $oldBatch->season_status,
                        ]
                    );

                    if ($accumulatedTransaction) {
                        $status = 'stored';
                        $sentTo = 193;
                        $type = 'sent_to_inventory';

                        $isSpecial = $transaction->batch_number[0] == 'S';

                        $newAccumulatedTransaction = Transaction::createGenericAccumulated(
                            $accumulatedBatch->batch_number,
                            $request->user()->user_id,
                            $isSpecial,
                            $accumulatedTransaction->transaction_id,
                            $status,
                            $sentTo,
                            $sessionNo,
                            $type
                        );

                        $accumulatedWeight = $transaction->details->sum('container_weight');

                        $accumulatedWeight += $accumulatedTransaction->details->first()->container_weight;
                        foreach ($accumulatedTransaction->details as $detail) {
                            $detail->update([
                                'container_status' => 1
                            ]);
                        }
                        $accumulatedDetail = TransactionDetail::createAccumulated($request->user()->user_id, $newAccumulatedTransaction->transaction_id, $accumulatedWeight);

                        $accumulatedTransaction->is_parent = $newAccumulatedTransaction->transaction_id;

                        $accumulatedTransaction->save();

                        $transaction->is_parent = $newAccumulatedTransaction->transaction_id;

                        $transaction->save();

                        $newAccumulatedTransaction->load('details');

                        $savedTransactions->push($newAccumulatedTransaction);
                    } else {
                        $status = 'stored';
                        $sentTo = 193;
                        $type = 'sent_to_inventory';

                        $isSpecial = $transaction->batch_number[0] == 'S';

                        $accumulatedTransaction = Transaction::createGenericAccumulated(
                            $accumulatedBatch->batch_number,
                            $request->user()->user_id,
                            $isSpecial,
                            $transaction->transaction_id,
                            $status,
                            $sentTo,
                            $sessionNo,
                            $type
                        );

                        $accumulatedWeight = $transaction->details->sum('container_weight');
                        $bag = TransactionDetail::createFromArray(
                            $detailsData,
                            $request->user()->user_id,
                            $transaction->transaction_id,
                            $transaction->reference_id
                        );
                        $accumulatedDetail = TransactionDetail::createAccumulated($request->user()->user_id, $accumulatedTransaction->transaction_id, $accumulatedWeight);

                        $transaction->is_parent = $accumulatedTransaction->transaction_id;
                        $transaction->save();

                        $accumulatedTransaction->load('details');

                        $savedTransactions->push($accumulatedTransaction);
                    }
                }

                if (isset($transactionData) && $transactionData['is_local'] && $transactionData['sent_to'] == 193) {
                    $savedTransactions = collect();
                    $batchNumber = $transactionData['batch_number'];
                    $isSpecial = $transactionData['batch_number'] == 'S';
                    $status = 'stored';
                    $sentTo = 193;
                    $type = 'sent_to_inventory';
                    $transactions = Transaction::with(['details' => function ($query) {
                        $query->where('container_status', 0)->where('container_number', '000');
                    }])->where('batch_number', $batchNumber)
                        ->where('is_parent', 0)
                        ->where('transaction_type', 5)->get();
                    $oldWeight = 0;
                    foreach ($transactions as $transaction) {
                        foreach ($transaction->details as $detail) {
                            $oldWeight += $detail->container_weight;
                        }
                    }

                    foreach ($detailsData as $detailObj) {
                        $detailData = $detailObj['detail'];
                        $newWeight  =   $oldWeight - $detailData['container_weight'];
                    }

                    $accumulatedTransaction = Transaction::createGenericAccumulated(
                        $batchNumber,
                        $request->user()->user_id,
                        $isSpecial,
                        $transactionData['transaction_id'],
                        $status,
                        $sentTo,
                        $sessionNo,
                        $type
                    );
                    $bag = TransactionDetail::createFromArray(
                        $detailsData,
                        $request->user()->user_id,
                        $transaction->transaction_id,
                        $transaction->reference_id
                    );
                    $accumulatedDetail = TransactionDetail::createAccumulated($request->user()->user_id, $accumulatedTransaction->transaction_id, $newWeight);


                    foreach ($transactions as $transaction) {
                        foreach ($transaction->details as $detail) {
                            $detail->update([
                                'container_status' => 1
                            ]);
                        }
                    }

                    $accumulatedTransaction->load('details');

                    $savedTransactions->push($accumulatedTransaction);
                }
                if (isset($transactionData) && $transactionData['is_local'] && $transactionData['sent_to'] == 194) {

                    $orders = Order::with('details')->where('status', 1)->where('order_number',  $transactionData['batch_number'])->get();
                    $batch = BatchNumber::firstOrCreate(
                        ['batch_number' => $transactionData['batch_number']],
                        [
                            'created_by' => $request->user()->user_id,
                            'is_mixed' => true,
                            'is_server_id' => true,
                            'season_id' => BatchNumber::max('season_id')

                        ]
                    );
                    $oldTranactionWeight = 0;
                    $oldTranaction = Transaction::with(['details' => function ($query) {
                        $query->where('container_status', 0);
                    }])->where('batch_number',  $batch->batch_number)->first();
                    if ($oldTranaction) {
                        $oldTranactionWeight +=   $oldTranaction->details->sum('container_weight');
                    }

                    $orderWeight = 0;

                    foreach ($orders as $order) {
                        $orderWeight += $order->details->sum('weight');
                    }
                    $currentTransactionWeight = 0;
                    foreach ($detailsData as $detailObj) {
                        $detailData = $detailObj['detail'];
                        $currentTransactionWeight +=  $detailData['container_weight'];
                    }
                    $condition = '';

                    if ($currentTransactionWeight > $orderWeight  && $oldTranactionWeight + $currentTransactionWeight > $orderWeight) {
                        return Response::json(array(
                            'status' => 'error', 'message' => 'weight is grater than order weight'
                        ), 499);
                    } elseif ($orderWeight == $currentTransactionWeight) {
                        $condition = 'prepaired';
                    } elseif ($oldTranactionWeight + $currentTransactionWeight == $orderWeight) {
                        $condition = 'prepaired';
                    } elseif ($oldTranactionWeight > 0 && $oldTranactionWeight + $currentTransactionWeight < $orderWeight) {
                        $condition = 'old_partial';
                    } elseif ($oldTranactionWeight == 0 && $currentTransactionWeight < $orderWeight) {
                        $condition = 'new_partial';
                    }

                    if ($condition == 'prepaired') {
                        foreach ($orders as $order) {
                            $order->update([
                                'status' =>  2
                            ]);
                        }
                        $sessionNo = CoffeeSession::max('server_session_id') + 1;
                        $status = 'order_prepaired';
                        $type = 'sent_to_yemen_sales';
                        $transactionType = 1;
                        $sentTo = 195;

                        $batch = BatchNumber::firstOrCreate(
                            ['batch_number' => $transactionData['batch_number']],
                            [
                                'created_by' => $request->user()->user_id,
                                'is_mixed' => true,
                                'is_server_id' => true,
                                'season_id' => BatchNumber::max('season_id')

                            ]
                        );

                        $transaction =  Transaction::create([
                            'batch_number' => $batch->batch_number,
                            'is_parent' => 0,
                            'created_by' =>  $request->user()->user_id,
                            'is_local' => FALSE,
                            'local_code' => $transactionData['local_code'],
                            'is_special' => false,
                            'is_mixed' => $transactionData['is_mixed'],
                            'transaction_type' => $transactionType,
                            'reference_id' => 0,
                            'transaction_status' => $status,
                            'is_new' => 0,
                            'sent_to' => $sentTo ?? $transactionData['sent_to'],
                            'is_server_id' => true,
                            'is_sent' => $transactionData['is_sent'],
                            'session_no' => $sessionNo,
                            'ready_to_milled' => $transactionData['ready_to_milled'],
                            'is_in_process' => $transactionData['is_in_process'],
                            'is_update_center' => array_key_exists('is_update_center', $transactionData) ? $transactionData['is_update_center'] : false,
                            'local_session_no' => array_key_exists('local_session_no', $transactionData) ? $transactionData['local_session_no'] : false,
                            'local_created_at' => toSqlDT($transactionData['local_created_at']),
                            'local_updated_at' => toSqlDT($transactionData['local_updated_at'])
                        ]);
                        $log = new TransactionLog();
                        $log->action = $status;
                        $log->created_by = $request->user()->user_id;
                        $log->entity_id = 0;
                        $log->local_created_at = $transaction->local_created_at;
                        $log->local_updated_at = $transaction->local_updated_at;
                        $log->type =  $type;
                        $log->center_name = null;

                        $transaction->log()->save($log);


                        $transactionDetails = TransactionDetail::createFromArray(
                            $detailsData,
                            $request->user()->user_id,
                            $transaction->transaction_id,
                            $transaction->reference_id
                        );

                        $transaction->load(['details.metas']);
                        $savedTransactions->push($transaction);
                    } elseif ($condition == 'new_partial') {
                        $sessionNo = CoffeeSession::max('server_session_id') + 1;
                        $status = 'partial_prepaired';
                        $type = 'sent_to_yemen_sales';
                        $transactionType = 1;
                        $sentTo = 194;

                        $batch = BatchNumber::firstOrCreate(
                            ['batch_number' => $transactionData['batch_number']],
                            [
                                'created_by' => $request->user()->user_id,
                                'is_mixed' => true,
                                'is_server_id' => true,
                                'season_id' => BatchNumber::max('season_id')

                            ]
                        );

                        $transaction =  Transaction::create([
                            'batch_number' => $batch->batch_number,
                            'is_parent' => 0,
                            'created_by' =>  $request->user()->user_id,
                            'is_local' => FALSE,
                            'local_code' => $transactionData['local_code'],
                            'is_special' => false,
                            'is_mixed' => $transactionData['is_mixed'],
                            'transaction_type' => $transactionType,
                            'reference_id' => 0,
                            'transaction_status' => $status,
                            'is_new' => 0,
                            'sent_to' => $sentTo ?? $transactionData['sent_to'],
                            'is_server_id' => true,
                            'is_sent' => $transactionData['is_sent'],
                            'session_no' => $sessionNo,
                            'ready_to_milled' => $transactionData['ready_to_milled'],
                            'is_in_process' => $transactionData['is_in_process'],
                            'is_update_center' => array_key_exists('is_update_center', $transactionData) ? $transactionData['is_update_center'] : false,
                            'local_session_no' => array_key_exists('local_session_no', $transactionData) ? $transactionData['local_session_no'] : false,
                            'local_created_at' => toSqlDT($transactionData['local_created_at']),
                            'local_updated_at' => toSqlDT($transactionData['local_updated_at'])
                        ]);
                        $log = new TransactionLog();
                        $log->action = $status;
                        $log->created_by = $request->user()->user_id;
                        $log->entity_id = 0;
                        $log->local_created_at = $transaction->local_created_at;
                        $log->local_updated_at = $transaction->local_updated_at;
                        $log->type =  $type;
                        $log->center_name = null;

                        $transaction->log()->save($log);


                        $transactionDetails = TransactionDetail::createFromArray(
                            $detailsData,
                            $request->user()->user_id,
                            $transaction->transaction_id,
                            $transaction->reference_id
                        );

                        $transaction->load(['details.metas']);
                        $savedTransactions->push($transaction);
                    } elseif ($condition == 'old_partial') {
                        $sessionNo = CoffeeSession::max('server_session_id') + 1;
                        $status = 'partial_prepaired';
                        $type = 'sent_to_yemen_sales';
                        $transactionType = 1;
                        $sentTo = 194;

                        $details = $oldTranaction->details;

                        foreach ($details as $detail) {
                            $newDetail =  $detail->replicate()->fill([]);

                            $newDetail->save();

                            $detail->update([
                                'container_status' => 1
                            ]);
                        }

                        $transactionDetails = TransactionDetail::createFromArray(
                            $detailsData,
                            $request->user()->user_id,
                            $oldTranaction->transaction_id,
                            $transaction->reference_id
                        );


                        $oldTranaction->load(['details.metas']);
                        $savedTransactions->push($oldTranaction);
                    }
                }
            }

            DB::commit();
        } catch (Throwable $th) {
            DB::rollback();
            return Response::json(array('status' => 'error', 'message' => $th->getMessage(), 'data' => [
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
    public function prepaired(Request $request)
    {
        $transactions = Transaction::where('is_parent', 0)
            ->whereIn('sent_to', [195])
            ->whereIn('transaction_type', [1])
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

            $transactionDetails = $transaction->details;
            // $transaction->center_id = $transaction->log->entity_id;
            // $transaction->center_name = $transaction->log->center_name;
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

        return sendSuccess(config("statuscodes." . $this->app_lang . ".success_messages.RECV_COFFEE_MESSAGE"), [
            'transactions' => $allTransactions,
        ]);
    }
}
