<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\TransactionDetail;
use App\ChildTransaction;
use App\TransactionLog;
use App\BatchNumber;
use App\Transaction;
use App\Season;
use Session;
use Auth;
use DB;

class MillingController extends Controller
{

    public function index()
    {
        return view('admin.milling.allsession');
    }

    function getMillingSessionAjax(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $search = $request->search['value'];
        //::count total record
        $total_members = Transaction::where('is_parent', 0)->whereHas('log', function ($q) {
            $q->where('action', 'received')->where('type', 'received_by_yemen');
        })->whereHas('transactionDetail', function ($q) {
            $q->where('container_status', 0);
        }, '>', 0)->distinct('session_no')->count();
        $members = Transaction::query();
        $members = $members->where('is_parent', 0)->whereHas('log', function ($q) {
            $q->where('action', 'received')->where('type', 'received_by_yemen');
        })->whereHas('transactionDetail', function ($q) {
            $q->where('container_status', 0);
        }, '>', 0)->with(['transactionDetail' => function ($query) {
            $query->where('container_status', 0);
        }]);
        //::search session_no
        $members = $members->when($search, function ($q) use ($search) {
            $q->where('session_no', 'like', "%$search%");
        });
        $members = $members->distinct()->select('session_no')->skip($start)->take($length)->groupBy('session_no')->orderBy('session_no', 'DESC')->get();
        foreach ($members as $key => $member) {
            $countBatch = Transaction::where('session_no', $member->session_no)->where('is_parent', 0)->distinct()->select('batch_number')->groupBy('batch_number')->whereHas('log', function ($q) {
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

    public function milling(Request $request, $id)
    {
        $data = array();
        $allTransactions = array();
        $transactions = Transaction::where('is_parent', 0)->where('session_no', $id)->whereHas('log', function ($q) {
            $q->where('action', 'received')->where('type', 'received_by_yemen');
        })->whereHas('transactionDetail', function ($q) {
            $q->where('container_status', 0);
        }, '>', 0)->with(['transactionDetail' => function ($query) {
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

    public function millingCoffee(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => "required|array|min:1",
        ], [
            'transaction_id.required' => 'Please select at least one batch number',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $lastBatchNumber = BatchNumber::orderBy('batch_id', 'desc')->first();
        $newLastBID = ($lastBatchNumber->batch_id + 1);

        $childTransactions = array();
        $transactionsDetail = array();
        $refid = implode(",", $request->transaction_id);
        $season = Season::where('status', 0)->first();

        DB::beginTransaction();
        try {
            foreach ($request->transaction_id as $key => $transaction) {
                $serverTran = Transaction::where('transaction_id', $transaction)->first();
                $removeLocalId = explode("-", $serverTran->batch_number);
                if ($removeLocalId[3] == '000') {
                    $mixFirstServerTran = Transaction::where('batch_number', $serverTran->batch_number)->first();
                    $childTrans = Transaction::where('is_parent', $mixFirstServerTran->transaction_id)->get();
                    foreach ($childTrans as $key => $childTran) {
                        array_push($childTransactions, $childTran->transaction_id);
                    }
                } else {
                    array_push($childTransactions, $transaction);
                }
                $childData = TransactionDetail::where('transaction_id', $transaction)->get()->toArray();
                $transactionsDetail = array_merge($transactionsDetail, $childData);
                TransactionDetail::where('transaction_id', $transaction)->update(['container_status' => 1]);
            }
            $serverbatch = Transaction::where('transaction_id', $request->transaction_id[0])->with('log')->first();
            if (count($request->transaction_id) > 1) {
                $mixBatch = $serverbatch->batch_number;
                $a = explode("-", $mixBatch);
                $batchNumber = implode('-', array_slice($a, 0, 3)) . '-000-' . $newLastBID;
                BatchNumber::create([
                    'batch_number' => $batchNumber,
                    'is_parent' => 0,
                    'is_mixed' => 1,
                    'created_by' => Auth::user()->user_id,
                    'is_local' => FALSE,
                    'local_code' => $batchNumber . '_' . Auth::user()->user_id . '-B-' . strtotime("now"),
                    'is_server_id' => 1,
                    'season_id' => $season->season_id,
                    'season_status' => $season->status,
                ]);
            } else {
                $batchNumber = $serverbatch->batch_number;
            }
            $newtransaction = Transaction::create([
                'batch_number' => $batchNumber,
                'is_parent' => 0,
                'is_mixed' => 1,
                'created_by' => Auth::user()->user_id,
                'is_local' => FALSE,
                'transaction_type' => 1,
                'local_code' => null,
                'transaction_status' => 'received',
                'reference_id' => $refid,
                'is_server_id' => 1,
                'is_new' => 0,
                'sent_to' => 14,
                'is_sent' => 1,
                'session_no' => $serverbatch->session_no,
                'local_created_at' => date("Y-m-d H:i:s", strtotime($serverbatch->created_at)),
            ]);
            $transactionLog = TransactionLog::create([
                'transaction_id' => $newtransaction->transaction_id,
                'action' => 'received',
                'created_by' => Auth::user()->user_id,
                'entity_id' => $serverbatch->log->entity_id,
                'center_name' => '',
                'local_created_at' => date("Y-m-d H:i:s", strtotime($serverbatch->created_at)),
                'type' => 'milling_coffee',
            ]);
            foreach ($transactionsDetail as $key => $transactionsDet) {
                $checkDetail = TransactionDetail::where('transaction_id', $newtransaction->transaction_id)->where('container_number', $transactionsDet['container_number'])->first();
                if ($checkDetail) {
                    $checkDetail->container_weight = ($checkDetail->container_weight + $transactionsDet['container_weight']);
                    $checkDetail->save();
                } else {
                    TransactionDetail::create([
                        'transaction_id' => $newtransaction->transaction_id,
                        'container_number' => $transactionsDet['container_number'],
                        'created_by' => Auth::user()->user_id,
                        'is_local' => FALSE,
                        'container_weight' => $transactionsDet['container_weight'],
                        'weight_unit' => 'kg',
                        'center_id' => 0,
                        'reference_id' => 0,
                    ]);
                }
            }
            foreach ($childTransactions as $key => $childTransaction) {
                $newchild = Transaction::where('transaction_id', $childTransaction)->first();
                ChildTransaction::create([
                    'parent_transaction_id' => $newtransaction->transaction_id,
                    'transaction_id' => $newchild->transaction_id,
                    'batch_number' => $newchild->batch_number,
                    'is_parent' => $newchild->is_parent,
                    'is_mixed' => $newchild->is_mixed,
                    'created_by' => $newchild->created_by,
                    'is_local' => $newchild->is_local,
                    'transaction_type' => $newchild->transaction_type,
                    'local_code' => $newchild->local_code,
                    'transaction_status' => $newchild->transaction_status,
                    'reference_id' => $newchild->reference_id,
                    'is_server_id' => $newchild->is_server_id,
                    'is_new' => $newchild->is_new,
                    'sent_to' => $newchild->sent_to,
                    'is_sent' => $newchild->is_sent,
                    'session_no' => $newchild->session_no,
                    'local_created_at' => date("Y-m-d H:i:s", strtotime($newchild->local_created_at)),
                ]);
            }
            $serverbatch = Transaction::where('transaction_id', $newtransaction->transaction_id)->first();
            $serverbatch->local_code = $newtransaction->transaction_id . '_' . Auth::user()->user_id . '-T-' . strtotime("now");
            $serverbatch->save();
            DB::commit();
            Session::flash('message', 'Milling coffee successfully.');
            return redirect('admin/milling_coffee');
        } catch (\PDOException $e) {
            Session::flash('error', 'Something was wrong.');
            return redirect('admin/milling_coffee');
        }
    }
}
