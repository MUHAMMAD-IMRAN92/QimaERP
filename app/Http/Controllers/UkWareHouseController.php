<?php

namespace App\Http\Controllers;

use App\Transaction;
use App\CoffeeSession;
use App\MetaTransation;
use App\TransactionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UkWareHouseController extends Controller
{
    public function index()
    {
        $transactionsWOS = Transaction::with(
            [
                'meta' => function ($query) {
                    $query->where('key', 'Price Per KG');
                }
            ]

        )->with('details')->where('is_parent', 0)->where('sent_to', 43)->get();

        $transactionsWS = Transaction::with([
            'meta' => function ($query) {
                $query->where('key', 'Price Per KG');
            }
        ])->with('details')->where('is_parent', 0)->where('sent_to', 44)->get();

        return view('admin.uk_warehouse.set_prices', [
            'transactionWOS' => $transactionsWOS,
            'transactionsWS' => $transactionsWS
        ]);
    }
    public function prices(Request $request, $id)
    {

        $transactionsWS = Transaction::with('details', 'meta')->find($id);

        return view('admin.uk_warehouse.modal', [
            'transactionsWS' => $transactionsWS
        ])->render();
    }
    public function post(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'price' => 'required | numeric',
        ]);
        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            return sendError($errors, 400);
        }
        $transactionsWS = Transaction::with('details', 'meta')->find($id);
        // return   $transactionsWS; 
        $selectedMeta = $transactionsWS->meta->where('key', 'Price Per KG');

        if (count($transactionsWS->meta) > 0 && count($selectedMeta) > 0) {

            foreach ($selectedMeta as $metas) {
                if ($metas->key == 'Price Per KG') {
                    $metas->update([
                        'value' => $request->price,
                    ]);
                } else {
                    $transactionMeta = new MetaTransation();
                    $transactionMeta->key = 'Price Per KG';
                    $transactionMeta->value = $request->price;
                    $transactionsWS->meta()->save($transactionMeta);
                }
            }
        } else {
            $transactionMeta = new MetaTransation();
            $transactionMeta->key = 'Price Per KG';
            $transactionMeta->value = $request->price;
            $transactionsWS->meta()->save($transactionMeta);
        }



        return redirect()->route('uk_warehouse.index')->with('msg', 'price added successfully');
    }
    public function assignToChaina(Request $request)
    {

        $request->validate([
            'transaction' => 'required',
        ]);
        $sessionNo = CoffeeSession::max('server_session_id') + 1;

        $transactions = Transaction::with('details', 'meta')->whereIn('transaction_id', $request->transaction)->get();

        foreach ($transactions as $transaction) {
            $replicatedTransaction = $transaction->replicate()->fill([
                'transaction_status' => 'allocated_chaina',
                'created_by' =>   $request->user()->user_id,
                'sent_to' => 472
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
            foreach ($transaction->meta as $metas) {
                $replicatedMetas = $metas->replicate()->fill([]);
                $replicatedMetas->save();
            }
        }

        return back()->with('msg', 'Allocated To Chaina');
    }
}
