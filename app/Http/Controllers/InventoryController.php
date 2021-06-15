<?php

namespace App\Http\Controllers;

use App\Transaction;
use App\TransactionDetail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $productsBatches = collect([
            [
                'batch_number' => 'GR1-HSK-00',
                'name' => 'Grade 1 Husk'
            ],
            [
                'batch_number' => 'GR2-HSK-00',
                'name' => 'Grade 2 Husk'
            ],
            [
                'batch_number' => 'GR3-HSK-00',
                'name' => 'Grade 3 Husk'
            ],
            [
                'batch_number' => 'GR2-CFE-00',
                'name' => 'Grade 2 Green Cofee'
            ],
            [
                'batch_number' => 'GR3-CFE-00',
                'name' => 'Grade 3 Green Cofee'
            ],
        ]);

        $specialProductsBatches = collect([
            [
                'batch_number' => 'SGR1-HSK-00',
                'name' => 'Special Grade 1 Husk'
            ],
            [
                'batch_number' => 'SGR2-HSK-00',
                'name' => 'Special Grade 2 Husk'
            ],
            [
                'batch_number' => 'SGR3-HSK-00',
                'name' => 'Special Grade 3 Husk'
            ],
            [
                'batch_number' => 'SGR2-CFE-00',
                'name' => 'Special Grade 2 Green Cofee'
            ],
            [
                'batch_number' => 'SGR3-CFE-00',
                'name' => 'Special Grade 3 Green Cofee'
            ],
        ]);

        $products = $productsBatches->map(function ($productData) {
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

        $specialProducts = $specialProductsBatches->map(function ($productData) {
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
        return view('admin.inventory.index', [
            'products' => $products,
            'special_products' => $specialProducts
        ]);
    }
}
