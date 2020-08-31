<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BatchNumber;
use App\Transaction;

class BatchNumberController extends Controller {

    public function index() {
        $data['batch'] = BatchNumber::where('is_parent', '0')->get();
        return view('admin.allbatchnumber', $data);
    }

    function getbatchAjax(Request $request) {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $search = $request->search['value'];
        $orderby = 'ASC';
        $column = 'batch_id';
//::count total record
        $total_members = BatchNumber::count();
        $members = BatchNumber::query();
        //::select columns
        $members = $members->select('batch_id', 'batch_number');
        //::search with farmername or farmer_code or  village_code
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
            } else {
                $column = 'batch_number';
            }
        }
        $members = $members->where('is_parent', '0')->orderBy($column, $orderby)->get();
        $data = array(
            'draw' => $draw,
            'recordsTotal' => $total_members,
            'recordsFiltered' => $total_members,
            'data' => $members,
        );
        //:: return json
        return json_encode($data);
    }

    public function show(Request $request, $id) {
        $data['batch'] = BatchNumber::where('batch_number', $id)->first();
        $data['transations_data'] = Transaction::where('batch_number', $id)
                        ->where('is_parent', 0)
                        ->where('transaction_status', 'created')
                        ->with('childTransation.transactionDetail', 'transactionDetail')
                        ->with(['sent_transaction' => function ($query) {
                                $query->where('is_parent', 0)->where('transaction_status', 'sent')->with('transactionDetail')->whereHas('transactionLog', function ($query) {
                                            $query->where('action', 'sent')->where('type', 'center');
                                        });
                                $query->with(['center_manager_received_transaction' => function ($query) {
                                        $query->where('is_parent', 0)->where('transaction_status', 'received')->with('transactionDetail')->whereHas('transactionLog', function ($query) {
                                                    $query->where('action', 'received')->where('type', 'center');
                                                });
                                    }]);
                            }])->get();
        ///return sendSuccess('Successfully retrieved farmers', $data['transations_data']);

        return view('admin.batchdetail', $data);
    }

//    public function show(Request $request, $id) {
//        $data['batch'] = BatchNumber::where('batch_number', $id)->with(['transaction' => function ($query) {
//                        $query->where('is_parent', 0)->where('transaction_status', 'created')->with('childTransation.transactionDetail', 'transactionDetail');
//                    }])->with(['sent_transaction' => function ($query) {
//                        $query->where('is_parent', 0)->where('transaction_status', 'sent')->with('transactionDetail')->whereHas('transactionLog', function ($query) {
//                                    $query->where('action', 'sent')->where('type', 'center');
//                                });
//                    }])->with(['center_manager_received_transaction' => function ($query) {
//                        $query->where('is_parent', 0)->where('transaction_status', 'received')->with('transactionDetail')->whereHas('transactionLog', function ($query) {
//                                    $query->where('action', 'received')->where('type', 'center');
//                                });
//                    }])->first();
//        return sendSuccess('Successfully retrieved farmers', $data['batch']);
//
//        return view('admin.batchdetail', $data);
//    }
}
