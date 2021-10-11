<?php

namespace App\Http\Controllers;

use App\Farmer;
use App\FileSystem;
use App\Transaction;
use App\TransactionDetail;
use App\TransactionInvoice;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class TransectionController extends Controller
{
    public function index()
    {
        // $data['transaction'] = Transaction::where('is_parent', '0')->get();
        // return view('admin.transaction.alltransection', $data);
        $transaction = Transaction::where('sent_to', 2)->distinct('batch_number')->get();
        return view('admin.transaction.alltransection', [
            'transaction' => $transaction
        ]);
    }
    public function detail(Request $request, $id)
    {
        // $transaction
        // $transaction = Transaction::find($id);
        // $batchNumber = $transaction->batch_number;
        // $allTransactions = Transaction::where('batch_number', $batchNumber)->with('details', 'meta')->orderBy('transaction_id', 'desc')->get();
        // foreach ($allTransactions as $trans) {
        //     $parentId = $trans->is_parent;

        //     if ($parentId != 0) {
        //         $transactionparentId = Transaction::find($parentId);
        //         if ($transactionparentId->batch_number !=  $batchNumber) {
        //             $transactionsparentId = Transaction::where('batch_number', $transactionparentId->batch_number)->with('details', 'meta')->orderBy('transaction_id', 'desc')->get();
        //             $data['transactionparentId'] = $transactionsparentId;
        //         }
        //     }
        // }

        // $transactionChild = Transaction::where('is_parent', $id)->with('details', 'meta')->orderBy('transaction_id', 'desc')->get();
        // $data['allTransactions'] =  $allTransactions;
        // $data['batchNumber'] =  $batchNumber;

        // $data['transactionChild'] = $transactionChild;
        // $invoice = TransactionInvoice::whereIn('transaction_id', [$allTransactions->last()['transaction_id']])->get();
        // $data['invoiceName'] = [];
        // foreach ($invoice as $inv) {
        //     $invName = FileSystem::find($inv->invoice_id);
        //     array_push($data['invoiceName'], $invName->user_file_name);
        // }
        // if ($transactionChild->count() > 0) {
        //     $invoice = TransactionInvoice::whereIn('transaction_id', [$transactionChild->last()['transaction_id']])->get();
        //     foreach ($invoice as $inv) {
        //         $invName = FileSystem::find($inv->invoice_id);
        //         array_push($data['invoiceName'], $invName->user_file_name);
        //     }
        // }
        // // dd($data['TransactionChild']);

        // $url =  $request->url();

        // $checkUrl =  Str::contains($url, 'rawTransaction');
        // if ($checkUrl) {
        //     return view('admin.transaction.raw_transactions', $data);
        // } else {
        //     return view('admin.transaction.transactiondetail', $data);
        // }
        $data1 = collect();
        $transaction = Transaction::find($id);
        $batchNumber = $transaction->batch_number;
        $allTransactions = Transaction::where('batch_number', $batchNumber)->with('details', 'meta')->orderBy('transaction_id', 'desc')->get();
        foreach ($allTransactions as $trans) {

            $parentId = $trans->is_parent;

            if ($parentId != 0) {
                $transactionparentId = Transaction::find($parentId);
                if ($transactionparentId->batch_number !=  $batchNumber) {  
                    $transactionsparentId = Transaction::where('batch_number', $transactionparentId->batch_number)->with('details', 'meta')->orderBy('transaction_id', 'desc')->get();
                    // $data['transactionparentId'] = $transactionsparentId;
                    foreach ($transactionsparentId as $transParent) {
                        $data1->push($transParent);
                    }
                    // $data1->push($transactionsparentId);
                }
            }
            $data1->push($trans);
        }

        $transactionChild = Transaction::where('is_parent', $id)->with('details', 'meta')->orderBy('transaction_id', 'desc')->get();
        // $data1->push($allTransactions);
        $data['batchNumber'] =  $batchNumber;
        foreach ($transactionChild as $transChild) {
            $data1->push($transChild);
        }
        // $data1->push($transactionChild);
        $invoice = TransactionInvoice::whereIn('transaction_id', [$allTransactions->last()['transaction_id']])->get();
        $data['invoiceName'] = [];
        foreach ($invoice as $inv) {
            $invName = FileSystem::find($inv->invoice_id);
            array_push($data['invoiceName'], $invName->user_file_name);
        }
        if ($transactionChild->count() > 0) {
            $invoice = TransactionInvoice::whereIn('transaction_id', [$transactionChild->last()['transaction_id']])->get();
            foreach ($invoice as $inv) {
                $invName = FileSystem::find($inv->invoice_id);
                array_push($data['invoiceName'], $invName->user_file_name);
            }
        }
        // dd($data['TransactionChild']);
        $data1->sortDesc();
        $data1->values()->all();
        // return $data;
        $url =  $request->url();

        $checkUrl =  Str::contains($url, 'rawTransaction');
        if ($checkUrl) {
            return view('admin.transaction.raw_transactions1',  [
                'data1' => $data1,
                'data' => $data
            ]);
        } else {
            return view('admin.transaction.transactiondetail1', [
                'data1' => $data1,
                'data' => $data
            ]);
        }
    }
    function getTransectionAjax(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $search = $request->search['value'];
        $orderby = 'ASC';
        $column = 'transaction_id';
        //::count total record
        $total_members = Transaction::count();
        $members = Transaction::query();
        $members = Transaction::query();
        //::select columns
        $members = $members->select('transaction_id', 'batch_number');
        //::search with batch number
        $members = $members->when($search, function ($q) use ($search) {
            $q->where('batch_number', 'like', "%$search%");
        });
        if ($request->has('order') && !is_null($request['order'])) {
            $orderBy = $request->get('order');
            $orderby = 'asc';
            if (isset($orderBy[0]['dir'])) {
                $orderby = $orderBy[0]['dir'];
            }
            if (isset($orderBy[0]['column']) && $orderBy[0]['column'] == 1) {
                $column = 'batch_number';
            } else {
                $column = 'batch_number';
            }
        }
        $members = $members->orderBy($column, $orderby)->get();
        $data = array(
            'draw' => $draw,
            'recordsTotal' => $total_members,
            'recordsFiltered' => $total_members,
            'data' => $members,
        );
        //:: return json
        return json_encode($data);
    }
}
