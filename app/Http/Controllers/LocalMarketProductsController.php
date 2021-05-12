<?php

namespace App\Http\Controllers;

use App\Product;
use App\Transaction;
use App\TransactionDetail;
use Illuminate\Http\Request;

class LocalMarketProductsController extends Controller
{
    public function index()
    {
        $products = Product::local()->get();

        return response()->json([
            'products' => $products
        ]);
    }

    public function weights()
    {
        $productsBatches = collect([
            [
                'id' => 1,
                'regular_batch_number' => 'GR1-HSK-00',
                'special_batch_number' => 'SGR1-HSK-00',
                'name' => 'Grade 1 Husk'
            ],
            [
                'id' => 2,
                'regular_batch_number' => 'GR2-HSK-00',
                'special_batch_number' => 'SGR2-HSK-00',
                'name' => 'Grade 2 Husk'
            ],
            [
                'id' => 3,
                'regular_batch_number' => 'GR3-HSK-00',
                'special_batch_number' => 'SGR3-HSK-00',
                'name' => 'Grade 3 Husk'
            ],
            [
                'id' => 4,
                'regular_batch_number' => 'GR2-CFE-00',
                'special_batch_number' => 'SGR2-CFE-00',
                'name' => 'Grade 2 Green Cofee'
            ],
            [
                'id' => 5,
                'regular_batch_number' => 'GR3-CFE-00',
                'special_batch_number' => 'SR3-CFE-00',
                'name' => 'Grade 3 Green Cofee'
            ]
        ]);

        $products = $productsBatches->map(function ($productData) {
            $regularBatchNumber = $productData['regular_batch_number'];
            $specialBatchNumber = $productData['special_batch_number'];

            $regularWeightDetail = TransactionDetail::whereHas('transaction', function ($query) use ($regularBatchNumber) {
                $query->where('batch_number', $regularBatchNumber)
                    ->where('is_parent', 0)
                    ->where('transaction_type', 5)
                    ->where('sent_to', 193);
            })->first();

            $specialWeightDetail = TransactionDetail::whereHas('transaction', function ($query) use ($specialBatchNumber) {
                $query->where('batch_number', $specialBatchNumber)
                    ->where('is_parent', 0)
                    ->where('transaction_type', 5)
                    ->where('sent_to', 193);
            })->first();

            $productData['regular_weight'] = $regularWeightDetail ? $regularWeightDetail->container_weight : 0;
            $productData['special_weight'] = $specialWeightDetail ? $specialWeightDetail->container_weight : 0;

            return $productData;
        });

        return response()->json([
            'count' => $products->count(),
            'inventory' => $products
        ]);
    }
}
