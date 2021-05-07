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
        ]);

        $products = $productsBatches->map(function ($productData) {
            $productData['weight'] = TransactionDetail::whereHas('transaction', function (Builder $query) use ($productData) {
                $query->where('batch_number', 'like', "{$productData['batch_number']}%");
            })->sum('container_weight');

            return $productData;
        });

        $specialProducts = $specialProductsBatches->map(function ($productData) {
            $productData['weight'] = TransactionDetail::whereHas('transaction', function (Builder $query) use ($productData) {
                $query->where('batch_number', 'like', "{$productData['batch_number']}%");
            })->sum('container_weight');

            return $productData;
        });

        $defectsGreenCofee['name'] = 'Defects Green Coffee';
        $defectsGreenCofee['batch_number'] = null;
        $defectsGreenCofee['weight'] = TransactionDetail::whereHas('transaction', function (Builder $query) {
            $query->where('transaction_type', 4)
                ->where('sent_to', 193);
        })->sum('container_weight');

        $products->push($defectsGreenCofee);

        $specialDefectsGreenCofee['name'] = 'Defects Green Coffee';
        $specialDefectsGreenCofee['batch_number'] = null;
        $specialDefectsGreenCofee['weight'] = TransactionDetail::whereHas('transaction', function (Builder $query) {
            $query->where('transaction_type', 4)
                ->where('sent_to', 193)
                ->where('is_special', true);
        })->sum('container_weight');

        $specialProducts->push($specialDefectsGreenCofee);

        return view('admin.inventory.index', [
            'products' => $products,
            'special_products' => $specialProducts
        ]);
    }
}
