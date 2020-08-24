<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transaction;
use App\TransactionDetail;
class TransectionController extends Controller
{
    public function index(){
    	$data['transaction']=Transaction::where('is_parent', '0')->get();
    	return view('admin.alltransection',$data);
    }
    public function detail($id){
    	// dd($id)
    	$data['TransactionChild']=Transaction::where('is_parent', $id)->get();
    	// $child = '';
    	// foreach ($data['TransactionChild'] as  $transIdget) 
    	// {
    		
    	// 	$child .=TransactionDetail::where('transaction_id', $transIdget->transaction_id)->get();
    	// 	//echo "<pre>",print_r($data['TransactionDetail']);
    	// }
    	// // dd($asss);
    	// $data['childs'] = $child;
    	// print_r($data['childs']);
    	$data['transaction']=Transaction::find($id);

    	$data['TransactionDetail']=TransactionDetail::where('transaction_id', $id)->get();
    	// dd($data['TransactionChild']);
    	return view('admin.transactiondetail',$data);

    }
    function getTransectionAjax(Request $request) {
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
        $members = $members->when($search, function($q)use ($search) {
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
            }else {
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
