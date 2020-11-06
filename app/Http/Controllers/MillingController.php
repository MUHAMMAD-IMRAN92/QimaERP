<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Transaction;
use App\TransactionDetail;
use App\TransactionLog;
use Session;
use DB;

class MillingController extends Controller {

    public function index() {
        return view('admin.milling.allsession');
    }

    function getMillingSessionAjax(Request $request) {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $search = $request->search['value'];
//::count total record
        $total_members = Transaction::where('is_parent', 0)->whereHas('log', function($q) {
                    $q->where('action', 'received')->where('type', 'received_by_yemen');
                })->whereHas('transactionDetail', function($q) {
                    $q->where('container_status', 0);
                }, '>', 0)->distinct('session_no')->count();
        $members = Transaction::query();
        $members = $members->where('is_parent', 0)->whereHas('log', function($q) {
                    $q->where('action', 'received')->where('type', 'received_by_yemen');
                })->whereHas('transactionDetail', function($q) {
                    $q->where('container_status', 0);
                }, '>', 0)->with(['transactionDetail' => function($query) {
                $query->where('container_status', 0);
            }]);
        //::search session_no
        $members = $members->when($search, function($q)use ($search) {
            $q->where('session_no', 'like', "%$search%");
        });
        $members = $members->distinct()->select('session_no')->skip($start)->take($length)->groupBy('session_no')->orderBy('session_no', 'DESC')->get();
        foreach ($members as $key => $member) {
            $countBatch = Transaction::where('session_no', $member->session_no)->where('is_parent', 0)->distinct()->select('batch_number')->groupBy('batch_number')->whereHas('log', function($q) {
                        $q->where('action', 'received')->where('type', 'received_by_yemen');
                    })->count();
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

    public function milling(Request $request, $id) {
        $data = array();
        $allTransactions = array();
        $transactions = Transaction::where('is_parent', 0)->where('session_no', $id)->whereHas('log', function($q) {
                    $q->where('action', 'received')->where('type', 'received_by_yemen');
                })->whereHas('transactionDetail', function($q) {
                    $q->where('container_status', 0);
                }, '>', 0)->with(['transactionDetail' => function($query) {
                        $query->where('container_status', 0);
                    }])->orderBy('transaction_id', 'desc')->get();
        $sessionTransactions = $transactions->groupBy('session_no');

        foreach ($sessionTransactions as $key => $sessionTransaction) {
            $sessionTransation = array();
            foreach ($sessionTransaction as $key => $transaction) {
                $childTransaction = array();
                $transactionDetail = $transaction->transactionDetail;
                $transaction->makeHidden('transactionDetail');
                $transaction->makeHidden('log');
                $removeLocalId = explode("-", $transaction->batch_number);
                if ($removeLocalId[3] == '000') {
                    $FindParentTransactions = Transaction::where('is_parent', 0)->where('batch_number', $transaction->batch_number)->first();
                    if ($FindParentTransactions) {
                        $childTransaction = Transaction::where('is_parent', $FindParentTransactions->transaction_id)->get();
                    }
                }
                $data = ['transaction' => $transaction, 'transactionDetails' => $transactionDetail, 'child_transactions' => $childTransaction];
                array_push($sessionTransation, $data);
            }
            array_push($allTransactions, $sessionTransation);
        }
        $data['transactions'] = $allTransactions;
        return view('admin.milling.index', $data);
    }

    public function millingCoffee(Request $request) {
        $validator = Validator::make($request->all(), [
                    'transaction_id' => "required|array|min:1",
                        ], [
                    'transaction_id.required' => 'Please select at least one batch number',
        ]);
        if ($validator->fails()) {
            //::validation failed
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $checkMillId = Transaction::orderBy('mill_id', 'desc')->first();
        $millId = ++$checkMillId->mill_id;
      //  DB::beginTransaction();
      //  try {
       
          //  DB::commit();
            Session::flash('message', 'Milling coffee successfully.');
            return redirect('admin/milling_coffee');
       // } catch (\PDOException $e) {
           // Session::flash('error', 'Something was wrong.');
          //  return redirect('admin/milling_coffee');
       // }
    }

}
