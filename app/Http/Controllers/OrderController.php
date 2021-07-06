<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Order;
use App\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{

    public function index()
    {
        $orders = Order::with('details')->get();

        $orders = $orders->map(function ($order) {
            $order->total = number_format($order->details->sum('total'), 2);

            return $order;
        });

        return view('admin.orders.index', [
            'orders' => $orders
        ]);
    }

    public function create()
    {
        return view('admin.orders.create');
    }

    public function store(Request $request)
    {
        // $request->validate([
        //     'customerId' => 'required',
        //     'customer' => 'required',
        //     'orders' => 'required'
        // ]);

        $validator = Validator::make($request->all(), [
            'customerId' => 'required',
            'customer' => 'required',
            'orders' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Data is invalid',
                'errors' => $validator->errors()->all()
            ]);
        }

        $customer = null;
        // dd($request->customer);
        $customerData = $request->customer;
        // dd($customerData);
        if ($request->customerId == 0) {
            $customer = Customer::create([
                'name' => $customerData['name'],
                'phone' => $customerData['phone'],
                'email' => $customerData['email'],
                'address' => $customerData['address']
            ]);
        } else {
            $customer = Customer::find($request->customerId);
        }

        if (!$customer) {
            return response()->json([
                'message' => 'You have an error in your data.',
                'errors' => [
                    'Customer not found.'
                ]

            ], 422);
        }

        $order = Order::create([
            'customer_id' => $customer->id,
            'order_number' => Order::genOrderNumber(),
            'status' => 1
        ]);

        foreach ($request->orders as $detailData) {
            $orderDetail = OrderDetail::create([
                'order_id' => $order->id,
                'product_id' => $detailData['productId'],
                'is_special' => $detailData['isSpecial'],
                'weight' => $detailData['weight'],
                'price' => $detailData['price'],
                'total' => $detailData['total']
            ]);
        }

        $order->load('details');

        return response()->json([
            'message' => "Order [{$order->order_number}] has been created successfully",
            'order' => $order
        ]);
    }

    public function show(Order $order)
    {
        $order->load(['details.product', 'customer']);

        $order->total = number_format($order->details->sum('total'), 2);

        // return $order;

        return view('admin.orders.show', [
            'order' => $order
        ]);
    }
    public function paidOrder(Request $request)
    {

        $orders = Order::whereIn('id', $request->order)->get();
        foreach ($orders as $order) {
            $order->update([
                'status' => 5
            ]);
        }

        return back()->with('msg', 'Selected Orders Mark As Piad');
    }
}
