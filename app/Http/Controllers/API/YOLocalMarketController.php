<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class YOLocalMarketController extends Controller
{
    public function getCoffee()
    {
        return 'coffee is coming...';
    }

    public function sendCoffee(Request $request)
    {
        return response()->json([
            'request' => $request->all()
        ]);
    }
}
