<?php

namespace App\Http\Controllers;

use App\BatchNumber;
use App\Imports\ImportFarmer;
use App\Imports\ImportVillage;
use App\Transaction;
use App\TransactionDetail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class BatchNumberController extends Controller
{

    public function index()
    {

        $data['batch'] = BatchNumber::where('is_parent', '0')->get();

        return view('admin.batch.allbatchnumber', $data);
    }

    function getbatchAjax(Request $request)
    {
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
        $members = $members->where('is_parent', '0')->whereHas('latestTransation')->with('latestTransation')->skip($start)->take($length)->orderBy($column, $orderby)->get();
        $data = array(
            'draw' => $draw,
            'recordsTotal' => $total_members,
            'recordsFiltered' => $total_members,
            'data' => $members,
        );
        //:: return json
        return json_encode($data);
    }

    public function show(Request $request, $id)
    {
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
        if (Str::contains($id, 'BATCH')) {
            $data['transations_data1'] = Transaction::where('batch_number', $id)
                // ->where('is_parent', 0)
                // ->where('transaction_status', 'created')
                ->with('childTransation.transactionDetail', 'transactionDetail')

                ->first();
        }

        return view('admin.batch.batchdetail', $data);
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


    public function duplication()
    {
        // $data = \DB::table('transactions')->select(\DB::raw('count(*) as duplicate'), 'batch_number', 'sent_to', 'created_by', 'local_code', 'transaction_status')
        //     ->groupBy('batch_number', 'sent_to', 'created_by', 'local_code', 'transaction_status')->orderBy(\DB::raw('1'), 'desc')->get();

        // $data->map(function ($tran) {

        //     $details = collect();
        //     $batchNumber = $tran->batch_number;
        //     // $dryTransactionDetails=   Transaction::where('batch_number',  $batchNumber)->where('sent_to', 10)->with('details')->get();
        //     $dryTransactionDetails = TransactionDetail::whereHas('transaction', function ($q) use ($batchNumber, $tran) {
        //         $q->where('batch_number',  $batchNumber)->where('sent_to', $tran->sent_to);
        //     })->get();

        //     foreach ($dryTransactionDetails as  $d) {
        //         $details->push($d);
        //     }
        //     $tran->details = $details;
        //     return $tran;
        // });
        $data = Transaction::with('details')->where('sent_to', 2)->where('is_parent', '!=',  0)->where('batch_number', 'NOT LIKE', '%000%')->orderBy('batch_number')->get();
        // return $data;
        return view('duplication', [
            'data' => $data,
        ]);
    }

    public function testing($id = 27589)
    {
        // if ($id != 27589) {
        //     return $id;
        // }
        $transactions = collect();
        // $farmers = collect();
        // $transaction = Transaction::where('transaction_id', $id)->first();
        // if ($transaction) {
        //     if ($transaction->sent_to == 2 && !Str::contains($transaction->batch_number, '000')) {
        //         $farmer = \Farmer::where('farmer_code', Str::beforeLast($transaction->batch_number, '-'))->first();
        //         $farmers->push($farmer);
        //     } else {
        //         // return  explode(',', $transaction->reference_id);
        //         $childTransaction =  Transaction::whereIn('transaction_id',  explode(',', $transaction->reference_id))->get();

        //         foreach ($childTransaction as $childTran) {

        //             if ($childTran) {
        //                 $transactions->push($childTran);
        //                 $farmer = $this->testing($childTran['reference_id']);
        //             }
        //         }
        //     }
        // }

        $transaction = Transaction::where('transaction_id', $id)->first();
        $transactions->push($transaction);
        if ($transaction) {
            $transaction = $this->testing($transaction['reference_id']);
            // $transactions->push($transaction);
        }
        return $transactions;
    }
    public function farmer(Request $request)
    {
        return view('form');
    }
    public function farmerPost(Request $request)
    {
        Excel::import(new ImportFarmer,  $request->file('file')->store('files'));
        return redirect()->back();
    }
    public function villages(Request $request)
    {
        Excel::import(new ImportVillage,  $request->file('file')->store('files'));
        return redirect()->back();
    }
    public function deleteBasket(Request $request)
    {
        $detail =  TransactionDetail::where('transaction_detail_id', $request->id)->first();

        $detail->metas()->delete();

        $detail->delete();
        return 1;
    }
}
