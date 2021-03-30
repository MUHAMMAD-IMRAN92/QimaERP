<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Meta;
use App\Transaction;
use App\TransactionDetail;
use App\TransactionLog;
use Doctrine\DBAL\Driver\IBMDB2\DB2Driver;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;

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
                'transactionDetail',
                function ($q) {
                    $q->where('container_status', 0);
                },
                '>',
                0
            )->with(['transactionDetail' => function ($query) {
                $query->where('container_status', 0);
            }])->with('meta', 'child')
            ->orderBy('transaction_id', 'desc')
            ->get();

        $allTransactions = array();

        foreach ($transactions as $transaction) {

            $childTransaction = array();
            $transactionDetail = $transaction->transactionDetail;
            $transaction->center_id = $transaction->log->entity_id;
            $transaction->center_name = $transaction->log->center_name;
            $transactionMata = $transaction->meta;

            $transaction->makeHidden('transactionDetail');
            $transaction->makeHidden('log');
            $transaction->makeHidden('meta');

            // $removeLocalId = explode("-", $transaction->batch_number);
            // if ($removeLocalId[3] == '000') {
            //     $FindParentTransactions = Transaction::where('is_parent', 0)->where('batch_number', $transaction->batch_number)->first();
            //     if ($FindParentTransactions) {
            //         $childTransaction = Transaction::where('is_parent', $FindParentTransactions->transaction_id)->get();

            //         foreach ($childTransaction as $key => $childTransactio) {
            //             $childTransactio->is_parent = $transaction->transaction_id;
            //         }
            //     }
            // }

            $data = [
                'transaction' => $transaction,
                'transactionDetails' => $transactionDetail,
                'transactionMeta' => $transactionMata,
                'child_transactions' => $childTransaction,
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
        $transactions = json_decode($request['transactions']);

        DB::beginTransaction();
        try {
            foreach ($transactions as $transactionData) {
                if ('something' == 'something') {
                    //do This
                    $transaction =  Transaction::create([]);

                    foreach ($transactionData->details as $detailsData) {
                        $transactionDetails = new TransactionDetail();

                        $transactionDetails->details()->save;
                        foreach ($detailsData->meta as $metas) {
                            $transactionMetas = new Meta();

                            $transactionMetas->meta()->save;
                        }
                    }
                }
            }
            DB::commit();
        } catch (\PDOException $e) {
            DB::rollback();
            return Response::json(array('status' => 'error', 'message' => $e->getMessage(), 'data' => []), 499);
        }
        $allTransactions = $transaction;
        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.RECV_COFFEE_MESSAGE"), $allTransactions);
    }
}
