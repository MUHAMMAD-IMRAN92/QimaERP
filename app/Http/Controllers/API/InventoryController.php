<?php

namespace App\Http\Controllers\API;

use App\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InventoryController extends Controller
{
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

    public function get()
    {
        $transactions = Transaction::where('is_parent', 0)
            ->where('sent_to', 193)
            ->where('transaction_type', 5)
            ->whereIn('batch_number', $this->fixedBatchNumbers)
            ->whereHas(
                'details',
                function ($q) {
                    $q->where('container_status', 0);
                },
                '>',
                0
            )->with(['details' => function ($query) {
                $query->where('container_status', 0)->with('metas');
            }])
            ->orderBy('batch_number')
            ->get();

        $weights = collect();

        foreach($transactions as $transaction){
            $weight = [
                'batch_number' => $transaction->batch_number,
                'weight' => $transaction->details->sum('container_weight')
            ];

            $weights->push($weight);
        }

        return response()->json([
            'trans_count' => $transactions->count(),
            'weight_count' => $weights->count(),
            'weights' => $weights,
            'transaction' => $transactions
        ]);
    }
}
