<?php

namespace App\Http\Controllers\API;

use App\Transaction;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PackagingOpController extends Controller
{
    public function __construct(Request $request)
    {
        set_time_limit(0);

        $this->app_lang = $request->header('x-app-lang') ?? 'en';
    }

    public function get()
    {
        $transactions = Transaction::where('is_parent', 0)
            // ->whereHas('log', function ($q) {
            //     $q->whereIn('action', ['sent'])
            //         ->whereIn(
            //             'type',
            //             [
            //                 'sent_to_po_packaging'
            //             ]
            //         );
            // })
            ->whereIn('sent_to', [31])
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
    // public function post(Request $request)
    // {
    //     $transactions = collect();
    //     $details =  collect();
    //     $metas  =  collect();

    //     foreach ($request->transactions as $transactionData) {
    //         $transactions->push($transactionData['transaction']);

    //         foreach ($transactionData['details'] as $detail) {
    //             $detailonly = Arr::only($detail, 'metas');
    //             $details->push($detailonly);

    //             foreach ($detail['metas'] as $metaData) {
    //                 $metas->push($metaData);
    //             }
    //         }
    //     }

    //     return response()->json([
    //         "transactions" => $transactions,
    //         "details" => $details,
    //         "metas" => $metas,
    //     ]);
    // }
}
