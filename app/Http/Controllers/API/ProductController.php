<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Product;

class ProductController extends Controller
{
    protected $millingFors = [1, 2];
    protected $sortingFors = [3, 4, 5];
    protected $exportFors = [3, 4];

    public function all()
    {
        $products = Product::all(['id', 'name', 'container_code']);

        return response()->json($products);
    }

    public function milling()
    {
        $products = Product::whereIn('for', $this->millingFors)
            ->get(['id', 'name', 'container_code']);

        return response()->json($products);
    }

    public function sorting()
    {
        $products = Product::whereIn('for', $this->sortingFors)
            ->get(['id', 'name', 'container_code']);

        return response()->json($products);
    }

    public function export()
    {
        $products = Product::whereIn('for', $this->exportFors)
            ->get(['id', 'name', 'container_code']);

        return response()->json($products);
    }
}
