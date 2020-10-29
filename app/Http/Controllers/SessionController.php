<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BatchNumber;
use App\Transaction;

class SessionController extends Controller {

    public function index() {
        return view('admin.session.allsession');
    }

    function getSessionAjax(Request $request) {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $search = $request->search['value'];
//::count total record
        $total_members = Transaction::distinct('session_no')->count();
        $members = Transaction::query();
        //::search session_no
        $members = $members->when($search, function($q)use ($search) {
            $q->where('session_no', 'like', "%$search%");
        });
        $members = $members->distinct()->select('session_no')->skip($start)->take($length)->groupBy('session_no')->orderBy('session_no', 'DESC')->get();
        foreach ($members as $key => $member) {
            $countBatch = Transaction::where('session_no', $member->session_no)->where('is_parent', 0)->distinct()->select('batch_number')->groupBy('batch_number')->count();
            $member->batch_count = $countBatch;
        }
        $data = array(
            'draw' => $draw,
            'recordsTotal' => $total_members,
            'recordsFiltered' => $total_members,
            'data' => $members,
        );
        //:: return json
        return json_encode($data);
    }

    function sessionDetail(Request $request, $session_no) {
        $data = array();
         $data['session_no']=$session_no;
        $transactions = Transaction::where('is_parent', 0)->where('session_no', $session_no)->doesntHave('isReference')->whereHas('transactionDetail', function($q) {
                    $q->where('container_status', 0);
                }, '>', 0)->with(['transactionDetail' => function($query) {
                        $query->where('container_status', 0);
                    }])->orderBy('transaction_id', 'desc')->get();
                    $data['transactions'] = $transactions->groupBy('batch_number'); 
        return view('admin.session.sessiondetail', $data);
    }

}
