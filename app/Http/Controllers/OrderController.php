<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function create()
    {
        return view('admin.orders.index');
    }

    public function store(Request $request)
    {
        return response()->json([
            'request_data' => $request->all()
        ]);
    }
}
