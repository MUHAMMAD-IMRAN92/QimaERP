<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transaction;
use App\TransactionDetail;
use App\Farmer;

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
    public function detail($id)
    {
        // $transaction
        $transaction = Transaction::find($id);
        $batchNumber = $transaction->batch_number;
        $allTransactions = Transaction::where('batch_number', $batchNumber)->with('details')->get();
        $transactionChild = Transaction::where('is_parent', $id)->with('details')->get();

        $data['allTransactions'] =  $allTransactions;
        $data['batchNumber'] =  $batchNumber;

        $data['transactionChild'] = $transactionChild;
        // dd($data['TransactionChild']);
        return view('admin.transaction.transactiondetail', $data);
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
