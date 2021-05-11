<?php

namespace App\Http\Controllers;

use App\Transaction;
use Illuminate\Http\Request;

class LocalMarketProductsController extends Controller
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
            $transaction = Transaction::with('details')->where('batch_number', $productData['batch_number'])
                ->where('is_parent', 0)
                ->where('transaction_type', 5)
                ->where('sent_to', 193)
                ->first();

            $weightDetail = $transaction ? $transaction->details->first() : null;

            $productData['weight'] = $weightDetail ? $weightDetail->container_weight : 0;

            return $productData;
        });

        return response()->json([
            'count' => $products->count(),
            'products' => $products
        ]);
    }
}
