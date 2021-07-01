<?php

namespace App\Http\Controllers\API;

use App\Lot;
use App\Meta;
use Exception;
use App\Product;
use App\Container;
use App\LotDetail;
use App\BatchNumber;
use App\Transaction;
use App\CoffeeSession;
use App\TransactionLog;
use App\TransactionDetail;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\TransactionDetailProduct;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\MetaTransation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class MillOperativeController extends Controller
{
    private $app_lang;

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

    public function __construct(Request $request)
    {
        set_time_limit(0);

        $this->app_lang = $request->header('x-app-lang') ?? 'en';
    }

    public function sendCoffee()
    {
        $transactions = Transaction::where('is_parent', 0)->where('transaction_type', 1)
            // ->whereHas('log', function ($q) {
            //     $q->whereIn('action', ['sent', 'received'])
            //         ->whereIn('type', ['received_by_mill', 'sent_to_mill', 'sent_to_market', 'sent_to_sorting']);
            // })
            ->whereIn('sent_to', [15, 17])
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

        // $transactions = Transaction::selectRaw('transactions.*')->where('is_parent', 0)->where('transaction_type', 1)
        //     ->whereHas('log', function ($q) {
        //         $q->whereIn('action', ['sent', 'received'])
        //             ->whereIn('type', ['received_by_mill', 'sent_to_mill', 'sent_to_market', 'sent_to_sorting']);
        //     })->with(['details' => function ($query) {
        //         $query->with('metas');
        //     }])->with(['meta', 'child'])
        //     ->leftjoin('milling_remaining_weight', function ($join) {
        //         $join->on('milling_remaining_weight.batch_number', 'transactions.batch_number');
        //         $join->on(DB::raw('sent_17-sent_20-sent_21'), '!=', DB::raw(0));
        //     })
        //     ->orderBy('transaction_id', 'desc')
        //     ->get();

        $allTransactions = array();

        // Transaction creation begins from here.
        $loopint = 0;
        foreach ($transactions as $transaction) {

            $transaction->center_id = $transaction->log->entity_id;
            $transaction->center_name = $transaction->log->center_name;
            $transactionMata = $transaction->meta;

            $detailMetas = [];
            $transactionChilds = [];
            $loop = 0;
            foreach ($transaction->details as $detail) {
                if ($transaction->sent_to == 15 && $detail->container_status == 1) {
                    // $transaction->details->forget(());
                    $detail->delete();
                    $loop++;
                    continue;
                }
                foreach ($detail->metas as $meta) {
                    array_push($detailMetas, $meta);
                }

                $detail->makeHidden('metas');
            }
            if (count($transaction->details) == 0) {
                $transactions->forget($loopint);
                $loopint++;
                continue;
            }
            $details = $transaction->details;
            $transactionDetails = $details->values();
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
        $validator = Validator::make($request->all(), [
            'transactions' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            return sendError($errors, 400);
        }

        // Here we are getting ids of market and sorting products to be used under for sorting the incoming request detials
        $marketIds = Product::market()->get(['id']);
        // $sortingIds = Product::sorting()->get(['id']);
        $peaberryIds = Product::peaberry()->get(['id']);
        $greenIds = Product::green()->get(['id']);

        // Get the maximum session id
        $sessionNo = CoffeeSession::max('server_session_id') + 1;

        $savedTransactions = collect();

        DB::beginTransaction();
        try {
            foreach ($request->transactions as $transactionArray) {

                $transactionData = (object) $transactionArray['transaction'];

                // We are trying to find parent transaction here


                // This is the recieved cofee
                if ($transactionData->is_local == true && $transactionData->sent_to == 17) {

                    if ($transactionData->is_server_id == true) {

                        $parentTransaction = Transaction::where('transaction_id', $transactionData->reference_id)->first();

                        if (!$parentTransaction) {
                            throw new Exception('Parent Transaction does not exists');
                        }
                    } else {
                        $code = $transactionData->reference_id . '_' . $request->user()->user_id . '-T';
                        $parentTransaction = Transaction::where('local_code', 'like', "$code%")
                            ->latest('transaction_id')
                            ->first();

                        if (!$parentTransaction) {
                            throw new Exception('Parent Transaction does not exists');
                        }
                    }


                    $status = 'received';
                    $type = 'received_by_mill';

                    $batchCheck = BatchNumber::where('batch_number', $transactionData->batch_number)->exists();

                    if (!$batchCheck) {
                        throw new Exception("Batch Number [{$transactionData->batch_number}] does not exists.");
                    }

                    $transaction =  Transaction::create([
                        'batch_number' => $transactionData->batch_number,
                        'is_parent' => $transactionData->is_parent,
                        'created_by' => $request->user()->user_id,
                        'is_local' => FALSE,
                        'local_code' => $transactionData->local_code,
                        'is_special' => $parentTransaction->is_special,
                        'is_mixed' => $transactionData->is_mixed,
                        'transaction_type' => 1,
                        'reference_id' => $parentTransaction->transaction_id,
                        'transaction_status' => $status,
                        'is_new' => 0,
                        'sent_to' => 17,
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
                        $detail->center_id = $detailData->center_id;
                        $detail->reference_id = $transaction->reference_id;

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

                // This coffe is sent further to next managers [sorting manager for sorting, yemen operative for local market]
                if ($transactionData->is_local == true && ($transactionData->sent_to == 21)) {

                    if ($transactionData->is_server_id == true) {
                        $referenceId =  explode(',', $transactionData->reference_id);

                        $parentTransaction = Transaction::whereIn('transaction_id', $referenceId)->get();

                        if (!$parentTransaction) {
                            throw new Exception('Parent Transaction does not exists');
                        }
                    } else {
                        $code = $transactionData->reference_id . '_' . $request->user()->user_id . '-T';
                        $parentTransaction = Transaction::where('local_code', 'like', "$code%")
                            ->latest('transaction_id')
                            ->first();

                        if (!$parentTransaction) {
                            throw new Exception('Parent Transaction does not exists');
                        }
                    }

                    $batchCheck = BatchNumber::where('batch_number', $transactionData->batch_number)->exists();

                    if (!$batchCheck) {
                        throw new Exception("Batch Number [{$transactionData->batch_number}] does not exists.");
                    }
                    /**
                     * Here we are sorting the transaction detials of incoming request
                     * into two parts for sending to two different managers
                     */
                    $marketDetails = collect();
                    $greenDetails = [];
                    $peaberryDetails = [];

                    foreach ($transactionArray['details'] as $detailArray) {

                        foreach ($detailArray['metas'] as $metaArray) {
                            $metaData = (object) $metaArray;

                            if ($metaData->key == 'product_id') {
                                if ($marketIds->contains($metaData->value)) {
                                    if ($metaData->value == 5) {
                                        $detailArray['product_id'] = 4;
                                    } else {
                                        $detailArray['product_id'] = $metaData->value;
                                    }

                                    $marketDetails->push($detailArray);
                                } elseif ($greenIds->contains($metaData->value)) {
                                    array_push($greenDetails, $detailArray);
                                } elseif ($peaberryIds->contains($metaData->value)) {
                                    array_push($peaberryDetails,  $detailArray);
                                }
                            }
                        }
                    }

                    if (!empty($greenDetails)) {
                        $status = 'sent';
                        $type = 'sent_to_sorting';
                        $sent_to = 21;
                        //if one of the transaction is specail
                        $isSpecial = false;
                        foreach ($parentTransaction as $metaTransaction) {
                            if ($metaTransaction->is_special == true) {
                                $isSpecial += true;
                            }
                        }

                        // Start of batch number Transaction
                        $transaction =  Transaction::create([
                            'batch_number' => $transactionData->batch_number,
                            'is_parent' => 0,
                            'created_by' => $request->user()->user_id,
                            'is_local' => FALSE,
                            'local_code' => $transactionData->local_code,
                            'is_special' => $isSpecial,
                            'is_mixed' => $transactionData->is_mixed,
                            'transaction_type' => 1,
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

                        foreach ($parentTransaction as $metaTransaction) {
                            if ($metaTransaction->is_special == true) {
                                MetaTransation::create([
                                    'transaction_id' =>   $transaction->transaction_id,
                                    'key' => 'Special',
                                    'transaction_id' => $metaTransaction->transaction_id
                                ]);
                            }
                            if ($metaTransaction->is_special == false) {
                                MetaTransation::create([
                                    'transaction_id' =>   $transaction->transaction_id,
                                    'key' => 'Non-Special',
                                    'transaction_id' => $metaTransaction->transaction_id
                                ]);
                            }
                            $metaTransaction->update([
                                'is_parent' =>  $transaction->transaction_id,
                            ]);
                        }

                        $referenceId =  explode(',', $transaction->reference_id);

                        foreach ($referenceId as $id) {
                            TransactionDetail::where('transaction_id', $id)
                                ->update(['container_status' => 1]);
                        }


                        $log = new TransactionLog();
                        $log->action = $status;
                        $log->created_by = $request->user()->user_id;
                        $log->entity_id = $transactionData->center_id;
                        $log->local_created_at = $transaction->local_created_at;
                        $log->local_updated_at = $transaction->local_updated_at;
                        $log->type =  $type;
                        $log->center_name = $transactionData->center_name;

                        $transaction->log()->save($log);

                        foreach ($greenDetails as $detailArray) {

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
                            $detail->center_id = $detailData->center_id;
                            $detail->reference_id = $transaction->reference_id;

                            $transaction->details()->save($detail);

                            // TransactionDetail::where('transaction_id', $transaction->reference_id)
                            //     ->where('container_number', $detail->container_number)
                            //     ->update(['container_status' => 1]);

                            foreach ($detailArray['metas'] as $metaArray) {
                                $metaData = (object) $metaArray;

                                $meta = new Meta();
                                $meta->key = $metaData->key;
                                $meta->value = $metaData->value;
                                $detail->metas()->save($meta);
                            }
                        }
                        // End of batch number Transaction

                        $transaction->load(['details.metas']);

                        $savedTransactions->push($transaction);
                    }

                    if (!empty($peaberryDetails)) {
                        $status = 'sent';
                        $type = 'sent_to_sorting';
                        $sent_to = 21;
                        $isSpecial = false;
                        foreach ($parentTransaction as $metaTransaction) {
                            if ($metaTransaction->is_special == true) {
                                $isSpecial += true;
                            }
                        }

                        // Start of batch number Transaction
                        $transaction =  Transaction::create([
                            'batch_number' => $transactionData->batch_number,
                            'is_parent' => $transactionData->is_parent,
                            'created_by' => $request->user()->user_id,
                            'is_local' => FALSE,
                            'local_code' => $transactionData->local_code,
                            'is_special' =>  $isSpecial,
                            'is_mixed' => $transactionData->is_mixed,
                            'transaction_type' => 1,
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

                        foreach ($parentTransaction as $metaTransaction) {
                            if ($metaTransaction->is_special == true) {
                                MetaTransation::create([
                                    'transaction_id' =>   $transaction->transaction_id,
                                    'key' => 'Special',
                                    'transaction_id' => $metaTransaction->transaction_id
                                ]);
                            }
                            if ($metaTransaction->is_special == false) {
                                MetaTransation::create([
                                    'transaction_id' =>   $transaction->transaction_id,
                                    'key' => 'Non-Special',
                                    'transaction_id' => $metaTransaction->transaction_id
                                ]);
                            }
                            $metaTransaction->update([
                                'is_parent' =>  $transaction->transaction_id,
                            ]);
                        }

                        $referenceId =  explode(',', $transaction->reference_id);

                        foreach ($referenceId as $id) {
                            TransactionDetail::where('transaction_id', $id)
                                ->update(['container_status' => 1]);
                        }
                        $log = new TransactionLog();
                        $log->action = $status;
                        $log->created_by = $request->user()->user_id;
                        $log->entity_id = $transactionData->center_id;
                        $log->local_created_at = $transaction->local_created_at;
                        $log->local_updated_at = $transaction->local_updated_at;
                        $log->type =  $type;
                        $log->center_name = $transactionData->center_name;

                        $transaction->log()->save($log);

                        foreach ($peaberryDetails as $detailArray) {

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
                            $detail->center_id = $detailData->center_id;
                            $detail->reference_id = $transaction->reference_id;

                            $transaction->details()->save($detail);

                            // TransactionDetail::where('transaction_id', $transaction->reference_id)
                            //     ->where('container_number', $detail->container_number)
                            //     ->update(['container_status' => 1]);

                            foreach ($detailArray['metas'] as $metaArray) {
                                $metaData = (object) $metaArray;

                                $meta = new Meta();
                                $meta->key = $metaData->key;
                                $meta->value = $metaData->value;
                                $detail->metas()->save($meta);
                            }
                        }
                        // End of batch number Transaction

                        $transaction->load(['details.metas']);

                        $savedTransactions->push($transaction);
                    }

                    // Saving the market coffee
                    if (!empty($marketDetails)) {

                        $status = 'sent';
                        $type = 'sent_to_market';
                        $sent_to = 20;
                        $isSpecial = false;
                        foreach ($parentTransaction as $metaTransaction) {
                            if ($metaTransaction->is_special == true) {
                                $isSpecial += true;
                            }
                        }
                        // Start of batch number Transaction
                        $transaction =  Transaction::create([
                            'batch_number' => $transactionData->batch_number,
                            'is_parent' => $transactionData->is_parent,
                            'created_by' => $request->user()->user_id,
                            'is_local' => FALSE,
                            'local_code' => $transactionData->local_code,
                            'is_special' => $isSpecial,
                            'is_mixed' => $transactionData->is_mixed,
                            'transaction_type' => 1,
                            'reference_id' =>  $transactionData->reference_id,
                            'transaction_status' => 'received',
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
                            $detail->center_id = $detailData->center_id;
                            $detail->reference_id = $transaction->reference_id;

                            $transaction->details()->save($detail);

                            $referenceId =  explode(',', $transaction->reference_id);

                            foreach ($referenceId as $id) {
                                TransactionDetail::where('transaction_id', $id)
                                    ->update(['container_status' => 1]);
                            }

                            foreach ($detailArray['metas'] as $metaArray) {
                                $metaData = (object) $metaArray;

                                $meta = new Meta();
                                $meta->key = $metaData->key;
                                $meta->value = $metaData->value;
                                $detail->metas()->save($meta);
                            }
                        }
                        // End of batch number Transaction

                        // Start of lot number Transaction

                        $marketDetailsGrouped = $marketDetails->groupBy('product_id');


                        foreach ($marketDetailsGrouped as $product_id => $detailArrays) {

                            $isSpecial = false;
                            foreach ($parentTransaction as $metaTransaction) {
                                if ($metaTransaction->is_special == true) {
                                    $isSpecial += true;
                                }
                            }

                            $batchNumbers =  $isSpecial ? $this->specialProductBatchNumbers : $this->normalProductBatchNumbers;

                            $hardcodeBatchNumberPrefix = $batchNumbers[$product_id];

                            $hardcodeBatchNumber = BatchNumber::newBatchNumber($hardcodeBatchNumberPrefix);

                            $oldBatch = BatchNumber::where('batch_number', $transactionData->batch_number)->first();

                            $batch = BatchNumber::firstOrCreate(
                                ['batch_number' => $hardcodeBatchNumber],
                                [
                                    'created_by' => $request->user()->user_id,
                                    'local_code' => $hardcodeBatchNumber,
                                    'is_server_id' => 1,
                                    'season_id' => $oldBatch->season_id,
                                    'season_status' => $oldBatch->season_status,
                                ]
                            );

                            $lotTransaction =  Transaction::create([
                                'batch_number' => $batch->batch_number,
                                'is_parent' => $transactionData->is_parent,
                                'created_by' => $request->user()->user_id,
                                'is_local' => FALSE,
                                'local_code' => $transactionData->local_code,
                                'is_special' => $transaction->is_special,
                                'is_mixed' => $transactionData->is_mixed,
                                'transaction_type' => 3,
                                'reference_id' => $transaction->transaction_id,
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

                            $lotTransaction->log()->save($log);

                            $lot = new Lot();
                            $lot->lot_number = $lotTransaction->batch_number;
                            $lot->transaction_id = $lotTransaction->transaction_id;
                            $lot->user_id = $request->user()->user_id;
                            $lot->is_mixed = $lotTransaction->is_mixed;
                            $lot->transaction_type = 3;
                            $lot->refference_ids = 0;
                            $lot->sent_to = $lotTransaction->sent_to;
                            $lot->is_in_process = $lotTransaction->is_in_process;
                            $lot->session_no = $lotTransaction->session_no;
                            $lot->is_sent = $lotTransaction->is_sent;
                            $log->is_special = $lotTransaction->is_special;

                            $lot->save();
                            foreach ($detailArrays as $detailArray) {

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
                                $detail->center_id = $detailData->center_id;
                                $detail->reference_id = $lotTransaction->reference_id;

                                $lotTransaction->details()->save($detail);

                                foreach ($detailArray['metas'] as $metaArray) {
                                    $metaData = (object) $metaArray;

                                    $meta = new Meta();
                                    $meta->key = $metaData->key;
                                    $meta->value = $metaData->value;
                                    $detail->metas()->save($meta);
                                }

                                $detailView = TransactionDetailProduct::where('transaction_detail_id', $detail->transaction_detail_id)->first();

                                if (!$detailView) {
                                    throw new Exception('Product does not exists.');
                                }

                                $product = $detailView->product;

                                if (!$product) {
                                    throw new Exception('Product not found in meta.');
                                }

                                $lotDetail = new LotDetail();

                                $lotDetail->lot_id = $lot->id;
                                $lotDetail->product_id = $product->id;
                                $lotDetail->container_number = $detail->container_number;
                                $lotDetail->weight = $detail->container_weight;
                                $lotDetail->weight_unit = $detail->weight_unit;

                                $lot->details()->save($lotDetail);
                            }

                            $lotTransaction->load(['details.metas']);

                            $savedTransactions->push($lotTransaction);
                        }
                        // End of lot number Transaction
                    }
                }
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            Log::channel('error')->debug('exception in mill operative', [
                'exception' => $e
            ]);
            return Response::json(array('status' => 'error', 'message' => $e->getMessage(), 'data' => []), 499);
        }

        if ($savedTransactions->isNotEmpty()) {
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
