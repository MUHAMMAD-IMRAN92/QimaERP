<?php

namespace App\Http\Controllers;

use App\Customer;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function create()
    {
        return view('admin.orders.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'customerId' => 'required',
            'customer' => 'required',
            'orders' => 'required'
        ]);

        $customer = null;
        $customerData = $request->customer;

        if($request->customerId == 0){
            $customer = Customer::create([
                'name' => $customerData->name,
                'phone' => $customerData->phone,
                'email' => $customerData->email,
                'address' => $customerData->address
            ]);
        } else {
            $customer = Customer::find($request->customerId);
        }

        if(!$customer){
            return response()->json([
                'message' => 'Customer not found.'
            ], 422);
        }

        return response()->json([
            'request_data' => $request->all()
        ]);
    }
}
