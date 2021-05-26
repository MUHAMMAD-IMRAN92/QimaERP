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
                'sent_to' => 41
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
        $farmer =  Farmer::where('farmer_name', $request->farmer)->first();

        $farmerTransactions = collect();

        if ($farmer) {
            $transactions = Transaction::whereHas('meta', function ($query) {
                $query->where('key', 'bacth_number');
            })->where('is_parent', 0)
                ->where('sent_to', 26)->with('details')->get();

            $farmerTransactions = $transactions->filter(function ($transaction) use ($farmer) {
                $farmerMetas = $transaction->meta->filter(function ($meta)  use ($farmer) {
                    $faremrId = explode('-', $meta->value)[3];

                    return $farmer->farmer_id == $faremrId;
                });

                return $farmerMetas->isNotEmpty();
            });
        }

        return $farmerTransactions;
    }
}
