<?php

namespace App\Http\Controllers\API;

use Throwable;
use App\Transaction;
use App\CoffeeSession;
use App\MetaTransation;
use App\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class UkQuality extends Controller
{
    public function __construct(Request $request)
    {
        set_time_limit(0);

        $this->app_lang = $request->header('x-app-lang') ?? 'en';
    }
    public function get()
    {
        $transactions = Transaction::where('is_parent', 0)
            ->whereIn('sent_to', [43, 44])
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

                    if ($sentTo == 44) {

                        $status = 'sent';
                        $type = 'sent_to_Admin';
                        $transactionType = 1;

                        $transaction = Transaction::createAndLog(
                            $transactionData,
                            $request->user()->user_id,
                            $status,
                            $sessionNo,
                            $type,
                            $transactionType
                        );
                        foreach ($transactionMeta  as $meta) {
                            $transactionMeta = new MetaTransation();
                            $transactionMeta->key = $meta['key'];
                            $transactionMeta->value = $meta['value'];
                            $transactionMeta->local_created_at = $transaction->local_created_at;
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

                        foreach ($transactionDetails as $detail) {
                            if ($detail->container_weight == 0) {
                                $detail->update([
                                    'container_status' => 1,
                                ]);
                            }
                        }


                        $transaction->load(['details.metas', 'meta']);
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
