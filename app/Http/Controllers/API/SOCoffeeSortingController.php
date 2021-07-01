<?php

namespace App\Http\Controllers\API;

use App\Meta;
use Exception;
use Throwable;
use App\Container;
use App\BatchNumber;
use App\Transaction;
use App\CoffeeSession;
use App\TransactionLog;
use App\TransactionDetail;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Product;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class SOCoffeeSortingController extends Controller
{
    public function __construct(Request $request)
    {
        set_time_limit(0);

        $this->app_lang = $request->header('x-app-lang') ?? 'en';
    }

    public function getCoffee()
    {
        $transactions = Transaction::selectRaw('transactions.*')->where('is_parent', 0)
            ->where('sent_to', 21)
            ->orWhere(function ($query) {
                $query->whereIn('sent_to', [22])
                    ->where('transaction_type', 1);
            })
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
        // ->leftjoin('sorting_remaining_weight', function ($join) {
        //     $join->on('sorting_remaining_weight.batch_number', 'transactions.batch_number');
        //     $join->on(DB::raw('sent_22-sent_201-sent_23'), '!=', DB::raw(0));
        // })
        // ->orderBy('transaction_id', 'desc')
        // ->get();



        $allTransactions = array();

        $loopint = 0;
        foreach ($transactions as $transaction) {

            $transaction->center_id = $transaction->log->entity_id;
            $transaction->center_name = $transaction->log->center_name;
            $transactionMata = $transaction->meta;

            $detailMetas = [];
            $transactionChilds = [];
            $loop = 0;
            foreach ($transaction->details as $detail) {
                if ($transaction->sent_to == 21 && $detail->container_status == 1) {
                    $transaction->details->forget(($loop));
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

            // Start of Transaction saving loop
            foreach ($request->transactions as $transactionObj) {

                $transactionData = $transactionObj['transaction'];
                $detailsData = $transactionObj['details'];

                if (isset($transactionData) && $transactionData['is_local']) {

                    $sentTo = $transactionData['sent_to'];

                    if ($sentTo == 22) {

                        $status = 'received';
                        $type = 'received_by_so';
                        $transactionType = 1;

                        $transaction = Transaction::createAndLog(
                            $transactionData,
                            $request->user()->user_id,
                            $status,
                            $sessionNo,
                            $type,
                            $transactionType
                        );

                        $transactionDetails = TransactionDetail::createFromArray(
                            $detailsData,
                            $request->user()->user_id,
                            $transaction->transaction_id,
                            $transaction->reference_id
                        );

                        $transaction->load(['details.metas']);
                        $savedTransactions->push($transaction);
                    } elseif ($sentTo == 23) {
                        $status = 'sent';
                        $type = 'sent_by_so';
                        $transactionType = 1;
                        if ($transactionData['is_server_id'] == true) {


                            $parentTransaction = Transaction::whereIn('transaction_id', $transactionData['reference_id'])->get();

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

                        $batchCheck = BatchNumber::where('batch_number', $transactionData['batch_number'])->exists();

                        if (!$batchCheck) {
                            throw new Exception("Batch Number [{$transactionData['batch_number']}] does not exists.");
                        }

                        $transaction =  Transaction::create([
                            'batch_number' => $transactionData['batch_number'],
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

                        Transaction::where('transaction_id', $transactionData['reference_id'])->update([
                            'is_parent' =>  $transaction->transaction_id
                        ]);

                        $transactionDetails = TransactionDetail::createFromArray(
                            $detailsData,
                            $request->user()->user_id,
                            $transaction->transaction_id,
                            $transaction->reference_id
                        );

                        foreach ($detailsData as $detailObj) {

                            $detailsData = $detailObj['detail'];
                            // return  $detailsData['reference_id'];
                            TransactionDetail::where('transaction_id',   $transactionData['reference_id'])->where('container_status', $detailsData['reference_id'])->update([
                                'container_status' => 1,
                            ]);
                        }
                  
                        $transaction->load(['details.metas']);
                        $savedTransactions->push($transaction);

                        // Seperate transactions for Peaberry and Export Green Coffee and defective
                        $peaberryIds = Product::peaberryWithoutDefectsIds();
                        $greenIds = Product::greenWithoutDefectsIds();
                        $defectiveIds = Product::allDefectiveIds();

                        $peaberryDetails = collect();
                        $greenDetails = collect();
                        $defectiveDetails = collect();

                        foreach ($transaction->details as $detail) {
                            $productIdMeta = $detail->metas->first(function ($value, $key) {
                                return $key == 'product_id';
                            });

                            if ($productIdMeta) {
                                $productId = $productIdMeta->value;

                                if ($peaberryIds->contains($productId)) {
                                    $peaberryDetails->push($detail);
                                } elseif ($greenIds->contains($productId)) {
                                    $greenDetails->push($detail);
                                } elseif ($defectiveIds->contains($productId)) {
                                    $defectiveDetails->push($detail);
                                }
                            }
                        }

                        // return response()->json([
                        //     'peaberry' => [
                        //         'count' => $peaberryDetails->count(),
                        //         'data' => $peaberryDetails
                        //     ],
                        //     'green' => [
                        //         'count' => $greenDetails->count(),
                        //         'data' => $greenDetails
                        //     ],
                        //     'defective' => [
                        //         'count' => $defectiveDetails->count(),
                        //         'data' => $defectiveDetails
                        //     ]
                        // ]);

                        // creating peaberry transaction
                        if ($peaberryDetails->isNotEmpty()) {
                            $status = 'sent';
                            $type = 'sent_to_packaging';
                            $transactionType = 3;

                            $transaction = Transaction::createAndLog(
                                $transactionData,
                                $request->user()->user_id,
                                $status,
                                $sessionNo,
                                $type,
                                $transactionType
                            );

                            $peaberryDetails->each(function ($detail) use ($transaction) {
                                $newDetail = $detail->replicate()->fill([
                                    'transaction_id' => $transaction->transaction_id
                                ]);

                                $newDetail->save();

                                $detail->metas->each(function ($meta) use ($newDetail) {
                                    $newMeta = $meta->replicate()->fill([
                                        'transaction_detail_id' => $newDetail->transaction_detail_id
                                    ]);

                                    $newMeta->save();
                                });
                            });

                            $transaction->load(['details.metas']);
                            $savedTransactions->push($transaction);
                        }

                        if ($greenDetails->isNotEmpty()) {
                            $status = 'sent';
                            $type = 'sent_to_packaging';
                            $transactionType = 3;

                            $transaction = Transaction::createAndLog(
                                $transactionData,
                                $request->user()->user_id,
                                $status,
                                $sessionNo,
                                $type,
                                $transactionType
                            );

                            $greenDetails->each(function ($detail) use ($transaction) {
                                $newDetail = $detail->replicate()->fill([
                                    'transaction_id' => $transaction->transaction_id
                                ]);

                                $newDetail->save();

                                $detail->metas->each(function ($meta) use ($newDetail) {
                                    $newMeta = $meta->replicate()->fill([
                                        'transaction_detail_id' => $newDetail->transaction_detail_id
                                    ]);

                                    $newMeta->save();
                                });
                            });

                            $transaction->load(['details.metas']);

                            $savedTransactions->push($transaction);
                        }

                        if ($defectiveDetails->isNotEmpty()) {
                            $status = 'sent';
                            $type = 'sent_to_market';
                            $transactionType = 3;
                            $sentTo = 201;

                            $parentTransaction = Transaction::findParent(
                                $transactionData['is_server_id'],
                                $transactionData['reference_id'],
                                $request->user()->user_id
                            );

                            if (!$parentTransaction) {
                                throw new Exception('Parent Transaction not found in sent to local market sales.');
                            }

                            $gradeThreeCoffeeBatchNumber = $parentTransaction->is_special ? 'SGR3-CFE-00' : 'GR3-CFE-00';

                            $hardcodeBatchNumber = BatchNumber::newBatchNumber($gradeThreeCoffeeBatchNumber);

                            $oldBatch = BatchNumber::where('batch_number', $transactionData['batch_number'])->first();

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

                            $transactionData['batch_number'] = $batch->batch_number;

                            $transaction = Transaction::createAndLog(
                                $transactionData,
                                $request->user()->user_id,
                                $status,
                                $sessionNo,
                                $type,
                                $transactionType,
                                $sentTo
                            );

                            $defectiveDetails->each(function ($detail) use ($transaction) {
                                $newDetail = $detail->replicate()->fill([
                                    'transaction_id' => $transaction->transaction_id
                                ]);

                                $newDetail->save();

                                $detail->metas->each(function ($meta) use ($newDetail) {
                                    $newMeta = $meta->replicate()->fill([
                                        'transaction_detail_id' => $newDetail->transaction_detail_id
                                    ]);

                                    $newMeta->save();
                                });
                            });

                            $transaction->load(['details.metas']);
                            $savedTransactions->push($transaction);
                        }
                    }
                }
            }
            // End of Transaction saving loop
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
}
