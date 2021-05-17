<?php

namespace App\Http\Controllers;

use Throwable;
use App\BatchNumber;
use App\Transaction;
use App\CoffeeSession;
use App\TransactionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PackingApprovalController extends Controller
{
    public function get()
    {
        $transactions = Transaction::with('details')
            ->where('is_parent', 0)
            ->where('sent_to', 29)
            ->orderBy('transaction_id', 'desc')
            ->get();

        return view('admin.export.packing.index', [
            'transactions' => $transactions,
        ]);
    }

    public function post(Request $request)
    {
        $request->validate([
            'approvals' => 'required|array'
        ]);

        $sessionNo = CoffeeSession::max('server_session_id') + 1;

        DB::beginTransaction();

        try {
            $userId = $request->user()->user_id;
            $status = 'sent';
            $sentTo = 30;
            $type = 'sent_to_yo_preparing';

            $transactions = Transaction::whereIn('transaction_id', $request->approvals)->get();

            foreach ($transactions as $transaction) {
                $replicatedTransaction = $transaction->replicate()->fill([
                    'created_by' => $request->user()->user_id,
                    'local_code' => $transaction->batch_number,
                    'reference_id' => $transaction->transaction_id,
                    'sent_to' => $sentTo,
                    'session_no' => $sessionNo,
                    'local_created_at' => now()->toDateTimeString(),
                    'local_updated_at' => now()->toDateTimeString(),
                ]);

                $replicatedTransaction->save();

                // commented for app team request
                // $transaction->is_parent = $replicatedTransaction->transaction_id;
                // $transaction->save();

                $log = new TransactionLog();
                $log->action = $status;
                $log->created_by = $userId;
                $log->entity_id = $transaction->log->entity_id;
                $log->local_created_at = $replicatedTransaction->local_created_at;
                $log->local_updated_at = $replicatedTransaction->local_updated_at;
                $log->type =  $type;
                $log->center_name = $transaction->log->center_name;

                $replicatedTransaction->log()->save($log);

                foreach($transaction->details as $detail){
                    $replicatedDetail = $detail->replicate()->fill([
                        'transaction_id' => $replicatedTransaction->transaction_id,
                        'created_by' => $userId,
                        'is_local' => false,
                        'local_code' => null,
                        'container_status' => 0,
                        'reference_id' => $transaction->transaction_id
                    ]);

                    $replicatedDetail->save();

                    // commented on just for now on app team request
                    // $detail->container_status = 1;
                    // $detail->save();

                    foreach($detail->metas as $meta){
                        $replicatedMeta = $meta->replicate()->fill([
                            'transaction_detail_id' => $replicatedDetail->transaction_detail_id
                        ]);

                        $replicatedMeta->save();
                    }
                }
            }

            DB::commit();
        } catch (Throwable $th) {
            DB::rollBack();

            dd($th);
            return back()->withErrors([
                'database' => $th->getMessage()
            ]);
        }

        return back()->with('msg', 'Batches have been mixed sucessfully');
    }
}
