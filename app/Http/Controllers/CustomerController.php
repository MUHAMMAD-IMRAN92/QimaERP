<?php

namespace App\Http\Controllers;

use App\Customer;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::orderBy('name')->get();

        return response()->json([
            'customers' => $customers
        ]);
    }
}
