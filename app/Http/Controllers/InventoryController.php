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
                'name' => 'Grade 2 Green Coffee'
            ],
            [
                'batch_number' => 'GR3-CFE-00',
                'name' => 'Grade 3 Green Coffee'
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
                'name' => 'Special Grade 2 Green Coffee'
            ],
            [
                'batch_number' => 'SGR3-CFE-00',
                'name' => 'Special Grade 3 Green Coffee'
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

        $sentTo = [24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38];
        $nonspecialtransactions =   Transaction::whereIn('sent_to',  $sentTo)->where('is_special', 0)->with(['details' => function ($query) {
            $query->where('container_status', 0);
        }])->get();

        $nonspecialsum = 0;
        foreach ($nonspecialtransactions as $transaction) {
            foreach ($transaction->details as $detail) {
                $nonspecialsum += $detail->container_weight;
            }
        }
        $specialtransactions =   Transaction::whereIn('sent_to', $sentTo)->where('is_special', 1)->with(['details' => function ($query) {
            $query->where('container_status', 0);
        }])->get();

        $specialsum = 0;
        foreach ($specialtransactions as $transaction) {
            foreach ($transaction->details as $detail) {
                $specialsum += $detail->container_weight;
            }
        }
        return view('admin.inventory.index', [
            'products' => $products,
            'special_products' => $specialProducts,
            'nonspecialgradeonecfe' => $nonspecialsum,
            'speciallgradeonecfe' => $specialsum
        ]);
    }
}
