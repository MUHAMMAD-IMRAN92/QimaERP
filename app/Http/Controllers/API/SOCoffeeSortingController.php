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
        $transactions = Transaction::where('is_parent', 0)
            ->where('sent_to', 21)
            // ->where('transaction_type', 3)
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

                if (isset($transactionData) && $transactionData['is_local']) {
                    // Start getting parent transaction
                    $parentTransaction = null;

                    if ($transactionData['is_server_id']) {
                        $parentTransaction = Transaction::where('transaction_id', $transactionData['reference_id'])->first();
                    } else {
                        $localCode = $transactionData['reference_id'] . '_' . $request->user()->user_id . '-T';

                        $parentTransaction = Transaction::where('local_code', 'like', "$localCode%")
                            ->latest('transaction_id')
                            ->first();

                        // $parentTransaction = Transaction::latest()->first();

                        return $parentTransaction;
                    }

                    if (!$parentTransaction) {
                        throw new Exception('Parent transaction not found. refferecen_id = ' . $transactionData['reference_id'] . ' local_code = ' . $localCode);
                    }
                    // End getting parent transaction
                    if ($transactionData['sent_to'] == 22) {

                        $status = 'received';
                        $type = 'received_by_so';

                        $batchCheck = BatchNumber::where('batch_number', $transactionData['batch_number'])->exists();

                        if (!$batchCheck) {
                            throw new Exception("Batch Number [{$transactionData['batch_number']}] does not exists.");
                        }

                        $transaction =  Transaction::create([
                            'batch_number' => $transactionData['batch_number'],
                            'is_parent' => 0,
                            'created_by' => $request->user()->user_id,
                            'is_local' => FALSE,
                            'local_code' => $transactionData['local_code'],
                            'is_special' => $parentTransaction->is_special,
                            'is_mixed' => $transactionData['is_mixed'],
                            'transaction_type' => 1,
                            'reference_id' => $parentTransaction->transaction_id,
                            'transaction_status' => $status,
                            'is_new' => 0,
                            'sent_to' => 22,
                            'is_server_id' => true,
                            'is_sent' => $transactionData['is_sent'],
                            'session_no' => $sessionNo,
                            'ready_to_milled' => $transactionData['ready_to_milled'],
                            'is_in_process' => $transactionData['is_in_process'],
                            'is_update_center' => $transactionData['is_update_center'],
                            'local_session_no' => $transactionData['local_session_no'],
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
                        $log->center_name = $transactionData['center_name'];

                        $transaction->log()->save($log);

                        // Start of saving Transaction Details
                        foreach ($transactionObj['details'] as $detailObj) {

                            $detailData = $detailObj['detail'];

                            // Start of finding Conatiner
                            $container = Container::where('container_number', $detailData['container_number'])->first();

                            if (!$container) {
                                $containerCode = preg_replace('/[0-9]+/', '', $detailData['container_number']);

                                $containerDetail = Arr::first(containerType(), function ($detail) use ($containerCode) {
                                    return $detail['code'] == $containerCode;
                                });

                                if (!$containerDetail) {
                                    throw new Exception('Container type not found.', 400);
                                }

                                $container = new Container();
                                $container->container_number = $detailData['container_number'];
                                $container->container_type = $containerDetail['id'];
                                $container->capacity = 100;
                                $container->created_by = $request->user()->user_id;

                                $container->save();
                            }
                            // End of finding Conatiner

                            // Start of saving one Detail
                            $detail = new TransactionDetail();

                            $detail->container_number = $container->container_number;
                            $detail->created_by = $request->user()->user_id;
                            $detail->is_local = FALSE;
                            $detail->container_weight = $detailData['container_weight'];
                            $detail->weight_unit = $detailData['weight_unit'];
                            $detail->center_id = $detailData['center_id'];
                            $detail->reference_id = $transaction->reference_id;

                            $transaction->details()->save($detail);
                            // End of saving one Detail

                            TransactionDetail::where('transaction_id', $transaction->reference_id)
                                ->where('container_number', $detail->container_number)
                                ->update(['container_status' => 1]);

                            foreach ($detailObj['metas'] as $$metaData) {

                                $meta = new Meta();
                                $meta->key = $metaData['key'];
                                $meta->value = $metaData['value'];
                                $detail->metas()->save($meta);
                            }
                        }
                        // End of saving Transaction Details

                        $transaction->load(['details.metas']);
                        $savedTransactions->push($transaction);
                    } elseif ($transactionData['sent_to'] == 23) {
                        // Start of normal Transaction save
                        $status = 'sent';
                        $type = 'sent_by_so';

                        $batchCheck = BatchNumber::where('batch_number', $transactionData['batch_number'])->exists();

                        if (!$batchCheck) {
                            throw new Exception("Batch Number [{$transactionData['batch_number']}] does not exists.");
                        }

                        $transaction =  Transaction::create([
                            'batch_number' => $transactionData['batch_number'],
                            'is_parent' => 0,
                            'created_by' => $request->user()->user_id,
                            'is_local' => FALSE,
                            'local_code' => $transactionData['local_code'],
                            'is_special' => $parentTransaction->is_special,
                            'is_mixed' => $transactionData['is_mixed'],
                            'transaction_type' => 1,
                            'reference_id' => $parentTransaction->transaction_id,
                            'transaction_status' => $status,
                            'is_new' => 0,
                            'sent_to' => 23,
                            'is_server_id' => true,
                            'is_sent' => $transactionData['is_sent'],
                            'session_no' => $sessionNo,
                            'ready_to_milled' => $transactionData['ready_to_milled'],
                            'is_in_process' => $transactionData['is_in_process'],
                            'is_update_center' => $transactionData['is_update_center'],
                            'local_session_no' => $transactionData['local_session_no'],
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
                        $log->center_name = $transactionData['center_name'];

                        $transaction->log()->save($log);

                        // Start of saving Transaction Details
                        foreach ($transactionObj['details'] as $detailObj) {

                            $detailData = $detailObj['detail'];

                            // Start of finding Conatiner
                            $container = Container::where('container_number', $detailData['container_number'])->first();

                            if (!$container) {
                                $containerCode = preg_replace('/[0-9]+/', '', $detailData['container_number']);

                                $containerDetail = Arr::first(containerType(), function ($detail) use ($containerCode) {
                                    return $detail['code'] == $containerCode;
                                });

                                if (!$containerDetail) {
                                    throw new Exception('Container type not found.', 400);
                                }

                                $container = new Container();
                                $container->container_number = $detailData['container_number'];
                                $container->container_type = $containerDetail['id'];
                                $container->capacity = 100;
                                $container->created_by = $request->user()->user_id;

                                $container->save();
                            }
                            // End of finding Conatiner

                            // Start of saving one Detail
                            $detail = new TransactionDetail();

                            $detail->container_number = $container->container_number;
                            $detail->created_by = $request->user()->user_id;
                            $detail->is_local = FALSE;
                            $detail->container_weight = $detailData['container_weight'];
                            $detail->weight_unit = $detailData['weight_unit'];
                            $detail->center_id = $detailData->center_id;
                            $detail->reference_id = $transaction->reference_id;

                            $transaction->details()->save($detail);
                            // End of saving one Detail

                            TransactionDetail::where('transaction_id', $transaction->reference_id)
                                ->where('container_number', $detail->container_number)
                                ->update(['container_status' => 1]);

                            foreach ($detailData['metas'] as $$metaData) {

                                $meta = new Meta();
                                $meta->key = $metaData['key'];
                                $meta->value = $metaData['value'];
                                $detail->metas()->save($meta);
                            }
                        }
                        // End of saving Transaction Details

                        $transaction->load(['details.metas']);
                        $savedTransactions->push($transaction);
                        // End of normal Transaction save

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

                                if ($peaberryIds->contain($productId)) {
                                    $peaberryDetails->push($detail);
                                } elseif ($greenIds->contains($productId)) {
                                    $greenDetails->push($detail);
                                } elseif ($defectiveIds->contains($productId)) {
                                    $defectiveDetails->push($detail);
                                }
                            }
                        }

                        return response()->json([
                            'peaberry' => $peaberryDetails,
                            'green' => $greenDetails,
                            'defective' => $defectiveDetails
                        ]);
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
