<?php

namespace App\Http\Controllers\API;

use App\Order;
use Exception;
use Throwable;
use App\Product;
use App\Container;
use App\BatchNumber;
use App\Transaction;
use App\CoffeeSession;
use App\MetaTransation;
use App\TransactionLog;
use App\TransactionDetail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\OrderPrepared;
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
            ->whereIn('sent_to', [20, 193, 194, 195, 201])
            ->whereIn('transaction_type', [3, 5, 6])
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

                //transaction with
                $orderPrepareds = OrderPrepared::where('order_number', $order->order_number)->get();
                foreach ($order->details as $detail) {
                    $detail->status = $order->status;
                    $detail->remWeight = $detail->weight;
                    foreach ($orderPrepareds  as  $orderPrepared) {

                        $product = Product::where('id', $detail->product_id)->first();
                        $batch = $product->batch_number;
                        if ($detail->is_special) {
                            $batch  =  'S' . $batch;
                        }
                        if ($batch  == $orderPrepared->p_batch_number) {
                            if ($order->details->sum('weight') == $orderPrepareds->sum('weight') || $orderPrepared->prepared_weight == 0) {
                                $detail->remWeight = $detail->weight;
                            }
                            // if ($orderPrepared->prepared_weight == 0) {
                            //     $detail->remWeigth = $detail->weight;
                            // }
                            else {
                                $weight = $orderPrepared->weight - $orderPrepared->prepared_weight;
                                $detail->remWeigth =  $weight;
                            }
                        }
                    }
                }
                $details = $order->details;
                $order->makeHidden('details');

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
        $arrContainer = [];

        // DB::beginTransaction();

        try {
            foreach ($request->transactions as $transactionObj) {
                $transactionData = $transactionObj['transaction'];
                $detailsData = $transactionObj['details'];
                // $transactionMeta =  $transactionObj['transactionMeta'];


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
                            $transactionData['local_code'],
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
                            $transactionData['local_code'],
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
                        $oldWeight += $transaction->details->sum('container_weight');
                    }
                    $detailWeight = 0;
                    foreach ($detailsData as $detailObj) {
                        $detailData = $detailObj['detail'];
                        $detailWeight +=  $detailData['container_weight'];
                    }

                    $newWeight = $oldWeight - $detailWeight;

                    $accumulatedTransaction = Transaction::createGenericAccumulated(
                        $batchNumber,
                        $request->user()->user_id,
                        $isSpecial,
                        $transactionData['transaction_id'],
                        $transactionData['local_code'],
                        $status,
                        $sentTo,
                        $sessionNo,
                        $type
                    );
                    $bag = TransactionDetail::createFromArray(
                        $detailsData,
                        $request->user()->user_id,
                        $accumulatedTransaction->transaction_id,
                        $transaction->reference_id
                    );
                    // $detail = new TransactionDetail();

                    // $detail->container_number = $detailData['container_weight'];
                    // $detail->transaction_id =  $accumulatedTransaction->transaction_id;
                    // $detail->created_by = $request->user()->id;
                    // $detail->is_local = FALSE;
                    // $detail->container_weight = $detailData['container_weight'];
                    // $detail->weight_unit = $detailData['weight_unit'];
                    // $detail->center_id = $detailData['center_id'];
                    // $detail->reference_id =  $accumulatedTransaction->reference_id;

                    // $detail->save();

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

                    if ($orderWeight == $currentTransactionWeight) {
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
                        $transactionType = 6;
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

                        if ($transactionData['is_server_id'] == true) {
                            $parentTransaction = Transaction::where('transaction_id', $transactionData['reference_id'])->first();

                            if (!$parentTransaction) {
                                throw new Exception('Parent Transaction does not exists');
                            }
                        } else {
                            $code = $transactionData['reference_id'] . '_' . $request->user()->user_id . '-T';
                            $parentTransaction = Transaction::where('local_code', 'like', "$code%")
                                ->latest('transaction_id')
                                ->first();

                            if (!$parentTransaction) {
                                throw new Exception('Parent Transaction does not exists');
                            }
                        }

                        $batchCheck = BatchNumber::where('batch_number', $batch->batch_number)->exists();

                        if (!$batchCheck) {
                            throw new Exception("Batch Number [{$batch->batch_number}] does not exists.");
                        }

                        $transaction =  Transaction::create([
                            'batch_number' => $batch->batch_number,
                            'is_parent' => 0,
                            'created_by' =>  $request->user()->user_id,
                            'is_local' => FALSE,
                            'local_code' => $transactionData['local_code'],
                            'is_special' => $parentTransaction->is_special,
                            'is_mixed' => $transactionData['is_mixed'],
                            'transaction_type' => $transactionType,
                            'reference_id' => $parentTransaction->transaction_id,
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
                        $log->entity_id = $transactionData['center_id'];
                        $log->local_created_at = $transaction->local_created_at;
                        $log->local_updated_at = $transaction->local_updated_at;
                        $log->type =  $type;
                        $log->center_name = array_key_exists('center_name', $transactionData) ? $transactionData['center_name'] : null;

                        $transaction->log()->save($log);


                        if ($oldTranaction) {
                            $details = $oldTranaction->details;

                            foreach ($details as $detail) {

                                $newDetail =  $detail->replicate()->fill([
                                    'transaction_id' =>  $transaction->transaction_id,
                                ]);

                                $newDetail->save();

                                $container = $detail['container_number'];
                                array_push($arrContainer, $container);


                                foreach ($detail->metas as $meta) {
                                    $newMeta =  $meta->replicate()->fill([
                                        'transaction_detail_id' => $newDetail->transaction_detail_id
                                    ]);
                                    $newMeta->save();
                                }
                                $detail->update([
                                    'container_status' => 1
                                ]);
                            }
                        }
                        // return 'here';



                        $transactionDetails = TransactionDetail::createFromArray(
                            $detailsData,
                            $request->user()->user_id,
                            $transaction->transaction_id,
                            $transaction->reference_id
                        );

                        foreach ($transactionDetails as $details) {
                            $container = $details['container_number'];
                            array_push($arrContainer, $container);
                        }
                        $transaction->load(['details.metas']);
                        $savedTransactions->push($transaction);
                    } elseif ($condition == 'new_partial') {
                        $sessionNo = CoffeeSession::max('server_session_id') + 1;
                        $status = 'partial_prepaired';
                        $type = 'sent_to_yemen_sales';
                        $transactionType = 6;
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

                        if ($transactionData['is_server_id'] == true) {
                            $parentTransaction = Transaction::where('transaction_id', $transactionData['reference_id'])->first();

                            if (!$parentTransaction) {
                                throw new Exception('Parent Transaction does not exists');
                            }
                        } else {
                            $code = $transactionData['reference_id'] . '_' . $request->user()->user_id . '-T';
                            $parentTransaction = Transaction::where('local_code', 'like', "$code%")
                                ->latest('transaction_id')
                                ->first();

                            if (!$parentTransaction) {
                                throw new Exception('Parent Transaction does not exists');
                            }
                        }


                        $batchCheck = BatchNumber::where('batch_number', $batch->batch_number)->exists();

                        if (!$batchCheck) {
                            throw new Exception("Batch Number [{$batch->batch_number}] does not exists.");
                        }

                        $transaction =  Transaction::create([
                            'batch_number' => $batch->batch_number,
                            'is_parent' => 0,
                            'created_by' =>  $request->user()->user_id,
                            'is_local' => FALSE,
                            'local_code' => $transactionData['local_code'],
                            'is_special' => $parentTransaction->is_special,
                            'is_mixed' => $transactionData['is_mixed'],
                            'transaction_type' => $transactionType,
                            'reference_id' => $parentTransaction->transaction_id,
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
                        $log->entity_id = $transactionData['center_id'];
                        $log->local_created_at = $transaction->local_created_at;
                        $log->local_updated_at = $transaction->local_updated_at;
                        $log->type =  $type;
                        $log->center_name = array_key_exists('center_name', $transactionData) ? $transactionData['center_name'] : null;

                        $transaction->log()->save($log);

                        $transactionDetails = TransactionDetail::createFromArray(
                            $detailsData,
                            $request->user()->user_id,
                            $transaction->transaction_id,
                            $transaction->reference_id
                        );
                        foreach ($transactionDetails as $details) {
                            $container = $details['container_number'];
                            array_push($arrContainer, $container);
                        }
                        $transaction->load(['details.metas']);
                        $savedTransactions->push($transaction);
                    } elseif ($condition == 'old_partial') {
                        $sessionNo = CoffeeSession::max('server_session_id') + 1;
                        $status = 'partial_prepaired';
                        $type = 'sent_to_yemen_sales';
                        $transactionType = 6;
                        $sentTo = 194;

                        $details = $oldTranaction->details;

                        foreach ($details as $detail) {

                            $newDetail =  $detail->replicate()->fill([]);

                            $newDetail->save();
                            $container = $detail['container_number'];
                            array_push($arrContainer, $container);

                            foreach ($detail->metas as $meta) {
                                $newMeta =  $meta->replicate()->fill([
                                    'transaction_detail_id' => $newDetail->transaction_detail_id
                                ]);
                                $newMeta->save();
                            }
                            $detail->update([
                                'container_status' => 1
                            ]);
                        }

                        $transactionDetails = TransactionDetail::createFromArray(
                            $detailsData,
                            $request->user()->user_id,
                            $oldTranaction->transaction_id,
                            $oldTranaction->reference_id
                        );
                        foreach ($transactionDetails as $details) {
                            $container = $details['container_number'];
                            array_push($arrContainer, $container);
                        }

                        $oldTranaction->load(['details.metas']);
                        $savedTransactions->push($oldTranaction);
                    }
                }
            }


            DB::commit();
            $transIDs = [];
            $transactionnewforupdate = Transaction::whereHas('details', function ($query) use ($arrContainer) {
                $query->whereIn('container_number', $arrContainer);
            })->with('details')->where('sent_to', 193)->get();

            foreach ($transactionnewforupdate as $t) {
                array_push($transIDs, $t->transaction_id);
                foreach ($t->details as $d) {
                    $d->whereIn('transaction_id', $transIDs)->whereIn('container_number', $arrContainer)->update(['container_status' => 1]);
                }
            }
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
            ->whereIn('sent_to', [195, 197, 198])
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

            foreach ($transaction->details as $detail) {
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
                'transactionDetails' => $transaction->details,
                'transactionMeta' => $transaction->meta,
                'detail_metas' => $detailMetas,
                'child_transactions' => $transactionChilds,
            ];

            array_push($allTransactions, $data);
        }

        return sendSuccess(config("statuscodes." . $this->app_lang . ".success_messages.RECV_COFFEE_MESSAGE"), $allTransactions);
    }
    public function postLocalSales(Request $request)
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
            foreach ($request->transactions as $transactionObj) {
                $transactionData = $transactionObj['transaction'];
                $detailsData = $transactionObj['details'];

                // Start of Transaction saving loop
                if (isset($transactionData) && $transactionData['is_local'] && $transactionData['sent_to'] == 197) {

                    $oldTransactions = Transaction::with('details')->where('batch_number', $transactionData['batch_number'])
                        ->where('sent_to', 197)->first();

                    $detailsData = $transactionObj['details'];
                    $transactionPrepaired =  Transaction::with('details')->where('batch_number', $transactionData['batch_number'])
                        ->where('sent_to', 195)->first();

                    $currentTransactionDetail =  count($detailsData);

                    $prepaidTransactionCount = count($transactionPrepaired->details);

                    if ($currentTransactionDetail ==  $prepaidTransactionCount) {

                        $status = 'order_collected';
                        $type = 'sent';
                        $transactionType = 6;
                        $sentTo = 197;
                        $order = Order::where('order_number', $transactionData['batch_number'])->first();

                        $order->update([
                            'status' => 3
                        ]);

                        $transaction = Transaction::createAndLog(
                            $transactionData,
                            $request->user()->user_id,
                            $status,
                            $sessionNo,
                            $type,
                            $transactionType,
                            $sentTo
                        );

                        $transactionPrepaired->update([
                            'is_parent' => $transaction->transaction_id
                        ]);

                        if ($oldTransactions) {
                            $oldTransactions->update([
                                'is_parent' =>  $transaction->transaction_id
                            ]);
                        }
                        $transactionDetails = TransactionDetail::createFromArray(
                            $detailsData,
                            $request->user()->user_id,
                            $transaction->transaction_id,
                            $transaction->reference_id
                        );

                        $transaction->load('details');

                        $savedTransactions->push($transaction);
                    }
                    if ($currentTransactionDetail <  $prepaidTransactionCount) {
                        $status = 'order_collected_partial';
                        $type = 'sent';
                        $transactionType = 6;
                        $sentTo = 197;

                        $transaction = Transaction::createAndLog(
                            $transactionData,
                            $request->user()->user_id,
                            $status,
                            $sessionNo,
                            $type,
                            $transactionType,
                            $sentTo
                        );

                        if ($oldTransactions) {
                            $oldTransactions->update([
                                'is_parent' =>  $transaction->transaction_id
                            ]);
                            $details = $oldTransactions->details;
                            foreach ($details as $detail) {

                                $newDetail =  $detail->replicate()->fill([]);

                                $newDetail->save();
                                foreach ($detail->metas as $meta) {
                                    $newMeta =  $meta->replicate()->fill([
                                        'transaction_detail_id' => $newDetail->transaction_detail_id
                                    ]);
                                    $newMeta->save();
                                }
                                $detail->update([
                                    'container_status' => 1
                                ]);
                            }
                        }




                        $transactionDetails = TransactionDetail::createFromArray(
                            $detailsData,
                            $request->user()->user_id,
                            $transaction->transaction_id,
                            $transaction->reference_id
                        );

                        $transaction->load('details');

                        $savedTransactions->push($transaction);
                    }
                }
                if (isset($transactionData) && $transactionData['is_local'] && $transactionData['sent_to'] == 198) {
                    $status = 'sent';
                    $type = 'order_deliverd';
                    $transactionType = 6;
                    $sentTo = 198;
                    $order = Order::where('order_number', $transactionData['batch_number'])->first();

                    $order->update([
                        'status' => 4
                    ]);

                    $transaction = Transaction::createAndLog(
                        $transactionData,
                        $request->user()->user_id,
                        $status,
                        $sessionNo,
                        $type,
                        $transactionType,
                        $sentTo
                    );

                    $oldTransaction =  Transaction::where('batch_number', $transactionData['batch_number'])->where('sent_to', 197)->first();

                    $oldTransaction->update([
                        'is_parent' => $transaction->transaction_id
                    ]);



                    $transactionDetails = TransactionDetail::createFromArray(
                        $detailsData,
                        $request->user()->user_id,
                        $transaction->transaction_id,
                        $transaction->reference_id
                    );

                    $transaction->load('details');

                    $savedTransactions->push($transaction);
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
