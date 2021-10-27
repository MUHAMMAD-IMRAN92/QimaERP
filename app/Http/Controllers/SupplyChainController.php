<?php

namespace App\Http\Controllers;

use App\Transaction;
use Illuminate\Http\Request;

class SupplyChainController extends Controller
{
    public function supplyChain()
    {
        $sentTo = ['Coffe Buyer' => 2, 'Coffee Buyer Manager' => 3, 'Special processing' =>  7, 'Coffee drying' => 10, 'Yemen operative' => 13, 'Mill operative' => 17,   'Coffee Sorting' => 22, 'Yemen Pack Coffe' => 24, 'Yemen Pack Operative ' => 33, 'Yemen Local Market' => 193, 'Yemen Sales Operative' => 197, 'Shipping' => 39, 'Dispatch ' => 41, 'Uk Warehouse' => 43, 'China Warehouse' => 474];
        $weightLabel = [];
        $managerName = [];
        foreach ($sentTo as $key => $sent) {
            if ($sent == 2) {
                $transactions = Transaction::with('details')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->get();
            } elseif ($sent == 193) {
                $transactions = Transaction::with(['details' => function ($query) {
                    $query->where('container_status', 0)->with('metas');
                }])->where('sent_to', $sent)->where('transaction_type', 5)->where('is_parent', 0)->whereHas('details', function ($q) {
                    $q->where('container_status', 0);
                })->get();
            } else {
                $transactions = Transaction::with(['details' => function ($query) {
                    $query->where('container_status', 0)->with('metas');
                }])->where('sent_to', $sent)->where('is_parent', 0)->whereHas('details', function ($q) {
                    $q->where('container_status', 0);
                })->get();
            }

            $weight = 0;
            foreach ($transactions as $transaction) {
                $weight += $transaction->details->sum('container_weight');
            }
            array_push($weightLabel, $weight);
            array_push($managerName, $key);
        }
        return view('admin.supplyChain.index', [
            'weightLabel' => $weightLabel,
            'managerName' => $managerName,
        ]);
    }
}
