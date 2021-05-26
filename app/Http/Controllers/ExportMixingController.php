<?php

namespace App\Http\Controllers;

use App\Meta;
use Throwable;
use App\BatchNumber;
use App\Transaction;
use App\CoffeeSession;
use App\MetaTransation;
use App\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExportMixingController extends Controller
{
    public function get()
    {
        $transactions = Transaction::with('details')
            ->where('is_parent', 0)
            ->where('sent_to', 24)
            ->orderBy('transaction_id', 'desc')
            ->get();

        return view('admin.export.mixing.index', [
            'transactions' => $transactions,
        ]);
    }
    public function post(Request $request)
    {
        $request->validate([
            'batch_number' => 'required|string|max:255|unique:batch_numbers',
            'mixings' => 'required|array'
        ]);

        $sessionNo = CoffeeSession::max('server_session_id') + 1;

        DB::beginTransaction();

        try {
            $userId = $request->user()->user_id;

            $batch = BatchNumber::create([
                'batch_number' => $request->batch_number,
                'created_by' => $userId,
                'is_mixed' => true,
                'is_server_id' => true,
                'season_id' => BatchNumber::max('season_id')
            ]);

            $status = 'sent';
            $sentTo = 26;
            $type = 'sent_to_yo_mixing';

            $transaction = Transaction::createGeneric(
                $batch->batch_number,
                $userId,
                implode(',', $request->mixings),
                $status,
                $sentTo,
                $sessionNo,
                $type
            );

            $transactions = Transaction::whereIn('transaction_id', $request->mixings)->get();

            foreach ($transactions as $oldTransaction) {
                $meta = new MetaTransation();
                $meta->transaction_id = $transaction->transaction_id;
                $meta->key = 'bacth_number';
                $meta->value = $oldTransaction->batch_number;
                $meta->save();
            }

            $mixDetails = TransactionDetail::whereIn('transaction_id', $request->mixings)->get();

            foreach ($mixDetails as $detail) {
                $originalTransactionId = $detail->transaction_id;
                $detail->replicate()->fill([
                    'transaction_id' => $transaction->transaction_id,
                    'created_by' => $userId,
                    'reference_id' => $originalTransactionId
                ])->save();
            }

            Transaction::whereIn('transaction_id', $request->mixings)
                ->update(['is_parent' => $transaction->transaction_id]);

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
