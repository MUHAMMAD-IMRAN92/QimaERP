<?php

namespace App\Http\Controllers\API;

use App\BatchNumber;
use Throwable;
use App\Transaction;
use App\CoffeeSession;
use App\Container;
use App\TransactionDetail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Exception;
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
            ->whereIn('sent_to', [20, 193, 192])
            ->whereIn('transaction_type', [3, 5])
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

                    $transactionBatchNumberPrefix = Str::beforeLast($transaction->batch_number, '-');

                    if (!in_array($transactionBatchNumberPrefix, $this->fixedBatchNumbers)) {
                        throw new Exception('Wrong batch number for this endpoint');
                    }

                    $accumulatedTransaction = Transaction::with('details')->where('batch_number', $transactionBatchNumberPrefix)
                        ->where('is_parent', 0)
                        ->where('transaction_type', 5)
                        ->first();

                    if ($accumulatedTransaction) {
                        $accumulatedBatch = BatchNumber::where('batch_number', '0000')->first();

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

                        $accumulatedDetail = TransactionDetail::createAccumulated($request->user()->user_id, $newAccumulatedTransaction->transaction_id, $accumulatedWeight);

                        $accumulatedTransaction->is_parent = $newAccumulatedTransaction->transaction_id;

                        $accumulatedTransaction->save();
                    } else {
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

                        $accumulatedDetail = TransactionDetail::createAccumulated($request->user()->user_id, $accumulatedTransaction->transaction_id, $accumulatedWeight);
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
}
