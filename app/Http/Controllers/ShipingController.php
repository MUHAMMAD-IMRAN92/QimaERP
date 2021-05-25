<?php

namespace App\Http\Controllers;

use App\Transaction;
use Doctrine\Inflector\Rules\English\Rules;
use Illuminate\Http\Request;

class ShipingController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['details' => function ($query) {
            return $query->with('metas');
        }])->where('sent_to', 36)->get();

        return view('admin.shipping.index', [
            'transactions' => $transactions
        ]);
    }
    public function post(Request $request)
    {
      
        $request->validate([
            'bags' => 'required',
        ]);

        return $request->all();
    }
}
