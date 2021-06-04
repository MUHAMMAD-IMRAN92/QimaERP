<?php

namespace App\Http\Controllers;

use Throwable;
use App\Transaction;
use App\CoffeeSession;
use App\Farmer;
use App\Region;
use App\TransactionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShipingController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('details', 'meta')->where('is_parent', 0)
            ->where('sent_to', 39)->get();

        return view('admin.shipping.index', [
            'transactions' => $transactions
        ]);
    }
    public function post(Request $request)
    {

        $request->validate([
            'bags' => 'required',
        ]);
        $sessionNo = CoffeeSession::max('server_session_id') + 1;

        $transactions = Transaction::with('details', 'meta')->whereIn('transaction_id', $request->bags)->get();

        foreach ($transactions as $transaction) {
            $replicatedTransaction = $transaction->replicate()->fill([
                'transaction_status' => 'sent',
                'created_by' =>   $request->user()->user_id,
                'sent_to' => 40
            ]);
            $replicatedTransaction->save();

            $transaction->is_parent = $replicatedTransaction->transaction_id;
            $transaction->save();

            $log = new TransactionLog();
            $log->action = 'sent';
            $log->created_by =  $request->user()->user_id;
            $log->entity_id = $transaction->log->entity_id;
            $log->local_created_at = $replicatedTransaction->local_created_at;
            $log->local_updated_at = $replicatedTransaction->local_updated_at;
            $log->type =  'sent_to_yemen_wh_op';
            $log->center_name = $transaction->log->center_name;

            $replicatedTransaction->log()->save($log);

            foreach ($transaction->details as $detail) {
                $replicatedDetail = $detail->replicate()->fill([
                    'transaction_id' => $replicatedTransaction->transaction_id,
                    'created_by' => $request->user()->user_id,
                    'is_local' => false,
                    'local_code' => null,
                    'container_status' => 0,
                    'reference_id' => $transaction->transaction_id
                ]);

                $detail->container_status = 1;
                $detail->save();


                $replicatedDetail->save();

                foreach ($detail->metas as $meta) {
                    $replicatedMeta = $meta->replicate()->fill([
                        'transaction_detail_id' => $replicatedDetail->transaction_detail_id
                    ]);

                    $replicatedMeta->save();
                }
            }
        }



        return back()->with('msg', 'Shipping Approved');
    }
    public function search(Request $request)
    {
        //finding farmer
        $farmerByName =  Farmer::where('farmer_name', $request->farmerName)->first();

        $farmerByCode =  Farmer::where('farmer_code', $request->farmerCode)->first();

        $transactionBysearch = collect();

        $oldtransactions = Transaction::with('details', 'meta')->where('is_parent', 0)
            ->where('sent_to', 39)->get();

        if ($farmerByName) {
            foreach ($oldtransactions as $transaction) {
                foreach ($transaction->meta as $metas) {
                    $faremrId = explode('-', $metas->value)[3];
                    $farmer = Farmer::find($faremrId);
                    if ($farmer) {
                        if ($farmer->farmer_name == $farmerByName->farmer_name) {
                            $transactionBysearch->push($transaction);
                        } else {
                            return 'no batch number';
                        }
                    }
                }
            }
        } elseif ($farmerByCode) {
            foreach ($oldtransactions as $transaction) {
                foreach ($transaction->meta as $metas) {
                    $faremrId = explode('-', $metas->value)[3];
                    $farmer = Farmer::find($faremrId);
                    if ($farmer) {
                        if ($farmer->farmer_code == $farmerByCode->farmer_code) {
                            $transactionBysearch->push($transaction);
                        } else {
                            return 'no batch number';
                        }
                    }
                }
            }
        } else {
            return 'Nothing found';
        }



        // if ($farmerByName) {
        //     $transactions =   $oldtransactions->filter(function ($transaction) use ($farmerByName) {
        //         $farmerMetas = $transaction->meta->filter(function ($meta)  use ($farmerByName) {
        //             $faremrId = explode('-', $meta->value)[3];
        //             $farmer = Farmer::find($faremrId);
        //          return $farmerByName->farmer_name == $farmer->farmer_name;
        //         });
        //         return  $farmerMetas == !null;
        //     });
        //    $transactionBysearch->push($transactions);
        // } 
        // return $transactionBysearch;
        // elseif ($farmerByCode) {
        //      $transactionBysearch = $transactions->filter(function ($transaction) use ($farmerByCode) {
        //         $farmerMetas = $transaction->meta->filter(function ($meta)  use ($farmerByCode) {
        //             $faremrId = explode('-', $meta->value)[3];
        //             $farmer = Farmer::find($faremrId);
        //             return $farmerByCode->farmer_code ==  $farmer->farmer_code;
        //         });
        //         return  $farmerMetas == !null;
        //     });

        // }
        //  return $transactionBysearch;
        //     //returning results
        return response()->json([
            'view' => view('admin.shipping.shipping_view', [
                'transactions' =>  $transactionBysearch
            ])->render()
        ]);
    }
}
