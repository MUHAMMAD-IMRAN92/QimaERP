<?php

namespace App\Http\Controllers\API;

use App\Transaction;
use App\TransactionDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LMInventoryController extends Controller
{
    public function index()
    {
        $productsBatches = collect([
            [
                'id' => 1,
                'batch_number' => 'GR1-HSK-00',
                'name' => 'Grade 1 Husk'
            ],
            [
                'id' => 2,
                'batch_number' => 'SGR1-HSK-00',
                'name' => 'Special Grade 1 Husk'
            ],
            [
                'id' => 3,
                'batch_number' => 'GR2-HSK-00',
                'name' => 'Grade 2 Husk'
            ],
            [
                'id' => 4,
                'batch_number' => 'SGR2-HSK-00',
                'name' => 'Special Grade 2 Husk'
            ],
            [
                'id' => 5,
                'batch_number' => 'GR3-HSK-00',
                'name' => 'Grade 3 Husk'
            ],
            [
                'id' => 6,
                'batch_number' => 'SGR3-HSK-00',
                'name' => 'Special Grade 3 Husk'
            ],
            [
                'id' => 7,
                'batch_number' => 'GR2-CFE-00',
                'name' => 'Grade 2 Green Cofee'
            ],
            [
                'id' => 8,
                'batch_number' => 'SGR2-CFE-00',
                'name' => 'Special Grade 2 Green Cofee'
            ],
            [
                'id' => 9,
                'batch_number' => 'GR3-CFE-00',
                'name' => 'Grade 3 Green Cofee'
            ],
            [
                'id' => 10,
                'batch_number' => 'SGR3-CFE-00',
                'name' => 'Special Grade 3 Green Cofee'
            ]
        ]);

        $inventory = $productsBatches->map(function ($productData) {
            $batchNumber = $productData['batch_number'];

            // $productData['weight'] = TransactionDetail::whereHas('transaction', function ($query) use ($batchNumber) {
            //     $query->where('batch_number', $batchNumber)
            //         ->where('is_parent', 0)
            //         ->where('transaction_type', 5);
            // })->sum('container_weight');
            $transactions = Transaction::with(['details' => function ($query) {
                $query->where('container_status', 0)->where('container_number', '000');
            }])->where('batch_number', $productData['batch_number'])
                ->where('is_parent', 0)
                ->where('transaction_type', 5)->get();
            $weight = 0;
            foreach ($transactions as $transaction) {
                foreach ($transaction->details as $detail) {
                    $weight += $detail->container_weight;
                }
            }
            $productData['rawWeight'] =  $weight;

            $transactions = Transaction::with(['details' => function ($query) {
                $query->where('container_status', 0)->where('container_number', '!=', '000');
            }])->where('batch_number', $productData['batch_number'])
                ->where('is_parent', 0)
                ->where('transaction_type', 5)->get();
            $bagweight = 0;
            foreach ($transactions as $transaction) {
                foreach ($transaction->details as $detail) {
                    $bagweight += $detail->container_weight;
                }
            }
            $productData['bagWight'] =  $bagweight;

            $productData['weight'] = $weight + $bagweight;
            return $productData;
        });

        return response()->json([
            'inventory' => $inventory
        ]);
    }
}
