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


    // public function sendCoffee(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'transactions' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         $errors = implode(', ', $validator->errors()->all());
    //         return sendError($errors, 400);
    //     }

    //     $savedTransactions = collect();

    //     $sessionNo = CoffeeSession::max('server_session_id') + 1;

    //     DB::beginTransaction();

    //     try {

    //         // Start of Transaction saving loop
    //         foreach ($request->transactions as $transactionObj) {
    //             $transactionData = $transactionObj['transaction'];

    //             if (isset($transactionData) && $transactionData['is_local']) {

    //                 $status = null;
    //                 $type = null;
    //                 $sentTo = $transactionData['sent_to'];

    //                 if($sentTo == 22){
    //                     $status = 'received';
    //                     $type = 'received_by_so';
    //                 } elseif($sentTo == 23){
    //                     $status = 'sent';
    //                     $type = 'sent_by_so';
    //                 }

    //                 if($status && $type){
    //                     $transaction = Transaction::createAndLog(
    //                         $transactionData, 
    //                         $request->user()->user_id,
    //                         $status, 
    //                         $sessionNo, 
    //                         $type
    //                     );

    //                     $transactionDetails = TransactionDetail::createFromArray(
    //                         $transactionObj, 
    //                         $request->user()->user_id, 
    //                         $transaction->transaction_id, 
    //                         $transaction->reference_id
    //                     );
                        
    //                     $transaction->load(['details.metas']);
    //                     $savedTransactions->push($transaction);
    //                 }

    //                 if($sentTo == 22){
    //                     $peaberryIds = Product::peaberryWithoutDefectsIds();
    //                     $greenIds = Product::greenWithoutDefectsIds();
    //                     $defectiveIds = Product::allDefectiveIds();

    //                     $peaberryDetails = collect();
    //                     $greenDetails = collect();
    //                     $defectiveDetails = collect();

    //                     foreach ($transaction->details as $detail) {
    //                         $productIdMeta = $detail->metas->first(function ($value, $key) {
    //                             return $key == 'product_id';
    //                         });

    //                         if ($productIdMeta) {
    //                             $productId = $productIdMeta->value;

    //                             if ($peaberryIds->contains($productId)) {
    //                                 $peaberryDetails->push($detail);
    //                             } elseif ($greenIds->contains($productId)) {
    //                                 $greenDetails->push($detail);
    //                             } elseif ($defectiveIds->contains($productId)) {
    //                                 $defectiveDetails->push($detail);
    //                             }
    //                         }
    //                     }

    //                     // Start of Peaberry transaction
    //                     if ($peaberryDetails->isNotEmpty()) {
    //                     }
    //                     // End of Peaberry Transaction

    //                     // Start of Export Green transaction
    //                     if ($greenDetails->isNotEmpty()) {
    //                     }
    //                     // End of Export Green transaction

    //                     return response()->json([
    //                         'peaberry' => $peaberryDetails,
    //                         'green' => $greenDetails,
    //                         'defective' => $defectiveDetails,
    //                         'already_saved' => $savedTransactions
    //                     ]);
    //                 }

                        
    //                 }
    //             }
    //         }
    //         // End of Transaction saving loop
    //         DB::commit();
    //     } catch (Throwable $th) {
    //         DB::rollback();
    //         return Response::json(array('status' => 'error', 'message' => $th->getMessage(), 'data' => [
    //             'line' => $th->getLine()
    //         ]), 499);
    //     }

    //     // Start of setting session
    //     if ($savedTransactions->isNotEmpty()) {
    //         CoffeeSession::create([
    //             'user_id' => $request->user()->user_id,
    //             'local_session_id' => $savedTransactions->first()->local_session_no,
    //             'server_session_id' => $sessionNo
    //         ]);
    //     } else {
    //         $sessionNo--;
    //     }
    //     // End of setting session

    //     return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.RECV_COFFEE_MESSAGE"), [
    //         'session_no' => $sessionNo,
    //         'transactions' => $savedTransactions,
    //     ]);
    // }
}
