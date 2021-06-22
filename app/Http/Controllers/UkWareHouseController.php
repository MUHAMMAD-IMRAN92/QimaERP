<?php

namespace App\Http\Controllers;

use App\Transaction;
use App\MetaTransation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UkWareHouseController extends Controller
{
    public function index()
    {
        $transactionsWOS = Transaction::with(
            [
                'meta' => function ($query) {
                    $query->where('key', 'Price Per KG');
                }
            ]

        )->with('details')->where('is_parent', 0)->where('sent_to', 43)->get();

        $transactionsWS = Transaction::with([
            'meta' => function ($query) {
                $query->where('key', 'Price Per KG');
            }
        ])->with('details')->where('is_parent', 0)->where('sent_to', 44)->get();

        return view('admin.uk_warehouse.set_prices', [
            'transactionWOS' => $transactionsWOS,
            'transactionsWS' => $transactionsWS
        ]);
    }
    public function prices(Request $request, $id)
    {

        $transactionsWS = Transaction::with('details', 'meta')->find($id);

        return view('admin.uk_warehouse.modal', [
            'transactionsWS' => $transactionsWS
        ])->render();
    }
    public function post(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'price' => 'required | numeric',
        ]);
        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            return sendError($errors, 400);
        }
        $transactionsWS = Transaction::with('details', 'meta')->find($id);
        if (count($transactionsWS->meta) > 0) {
            $selectedMeta = $transactionsWS->meta->where('key', 'Price Per KG');
            foreach ($selectedMeta as $metas) {
                if ($metas->key == 'Price Per KG') {
                    $metas->update([
                        'value' => $request->price,
                    ]);
                } else {
                    $transactionMeta = new MetaTransation();
                    $transactionMeta->key = 'Price Per KG';
                    $transactionMeta->value = $request->price;
                    $transactionsWS->meta()->save($transactionMeta);
                }
            }
        } else {
            $transactionMeta = new MetaTransation();
            $transactionMeta->key = 'Price Per KG';
            $transactionMeta->value = $request->price;
            $transactionsWS->meta()->save($transactionMeta);
        }



        return redirect()->route('uk_warehouse.index')->with('msg', 'price added successfully');
    }
}
