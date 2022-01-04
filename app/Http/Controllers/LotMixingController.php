<?php

namespace App\Http\Controllers;

use App\Governerate;
use App\Region;
use Illuminate\Http\Request;
use App\Transaction;
use App\Village;
use Carbon\Carbon;

class LotMixingController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('details')
            ->where('is_parent', 0)
            ->where('sent_to', 24)
            ->orderBy('transaction_id', 'desc')
            ->get();
        // $allTransactions = array();

        // foreach ($transactions as $key => $tran) {
        //     // if ($tran->sent_to == 13) {
        //     $childTransaction = array();
        //     $tran->makeHidden('log');
        //     $removeLocalId = explode("-", $tran->batch_number);
        //     if ($removeLocalId[3] == '000') {
        //         $FindParentTransactions = Transaction::where('is_parent', 0)->where('batch_number', $tran->batch_number)->first();
        //         if ($FindParentTransactions) {
        //             $childTransaction = Transaction::where('is_parent', $FindParentTransactions->transaction_id)->get();
        //         }
        //     }
        //     $transaction = ['transaction' => $tran, 'child_transactions' => $childTransaction];
        //     // array_push($allTransactions, $tran);
        //     array_push($allTransactions, $transaction);
        //     // }
        //     // array_push($allTransactions, $tran);
        // }
        // return $transactions;
        return view('admin.lotMixing.index', [
            'transactions' => $transactions,
        ]);
    }
    public function filterByDays(Request $request)
    {
        $date = $request->date;
        if ($date == 'today') {
            $date = Carbon::today()->toDateString();

            $transactions = Transaction::with('details')
                ->where('is_parent', 0)
                ->where('sent_to', 24)
                ->whereDate('created_at', $date)
                ->orderBy('transaction_id', 'desc')
                ->get();
            return view('admin.lotMixing.view', [
                'transactions' => $transactions,
            ]);
        } elseif ($date == 'yesterday') {
            $now = Carbon::now();
            $yesterday = Carbon::yesterday();

            $transactions = Transaction::with('details')
                ->where('is_parent', 0)
                ->where('sent_to', 24)->whereDate('created_at', $yesterday)
                ->orderBy('transaction_id', 'desc')
                ->get();
            return view('admin.lotMixing.view', [
                'transactions' => $transactions,
            ]);
        } elseif ($date == 'lastmonth') {

            $date = Carbon::now();

            $lastMonth =  $date->subMonth()->format('m');
            $year = $date->year;
            $transactions = Transaction::with('details')
                ->where('is_parent', 0)
                ->where('sent_to', 24)->whereMonth('created_at', $lastMonth)->whereYear('created_at', $year)
                ->orderBy('transaction_id', 'desc')
                ->get();
            return view('admin.lotMixing.view', [
                'transactions' => $transactions,
            ]);
        } elseif ($date == 'currentyear') {

            $date = Carbon::now();


            $year = $date->year;

            $transactions = Transaction::with('details')
                ->where('is_parent', 0)
                ->where('sent_to', 24)->whereYear('created_at', $year)
                ->orderBy('transaction_id', 'desc')
                ->get();
            return view('admin.lotMixing.view', [
                'transactions' => $transactions,
            ]);
        } elseif ($date == 'weekToDate') {

            $now = Carbon::now();
            $start = $now->startOfWeek(Carbon::SUNDAY)->toDateString();
            $end = $now->endOfWeek(Carbon::SATURDAY)->toDateString();


            $transactions = Transaction::with('details')
                ->where('is_parent', 0)
                ->where('sent_to', 24)->whereBetween('created_at', [$start, $end])
                ->orderBy('transaction_id', 'desc')
                ->get();
            return view('admin.lotMixing.view', [
                'transactions' => $transactions,
            ]);
        } elseif ($date == 'monthToDate') {

            $now = Carbon::now();
            $date = Carbon::tomorrow()->toDateString();
            $start = $now->firstOfMonth();


            $transactions = Transaction::with('details')
                ->where('is_parent', 0)
                ->where('sent_to', 24)->whereBetween('created_at', [$start, $date])
                ->orderBy('transaction_id', 'desc')
                ->get();
            return view('admin.lotMixing.view', [
                'transactions' => $transactions,
            ]);
        } elseif ($date == 'yearToDate') {

            $now = Carbon::now();
            $date = Carbon::tomorrow()->toDateString();
            $start = $now->startOfYear();

            $transactions = Transaction::with('details')
                ->where('is_parent', 0)->whereBetween('created_at', [$start, $date])
                ->where('sent_to', 24)
                ->orderBy('transaction_id', 'desc')
                ->get();
            return view('admin.lotMixing.view', [
                'transactions' => $transactions,
            ]);
        } elseif ($date == 'lastyear') {

            $date = Carbon::now();


            $year = $date->year - 1;

            $transactions = Transaction::with('details')
                ->where('is_parent', 0)
                ->where('sent_to', 24)->whereYear('created_at', $year)
                ->orderBy('transaction_id', 'desc')
                ->get();
            return view('admin.lotMixing.view', [
                'transactions' => $transactions,
            ]);
        }
    }
    public function betweenDate(Request $request)
    {
        $transactions = Transaction::with('details')
            ->where('is_parent', 0)
            ->where('sent_to', 24)
            ->whereBetween('created_at', [$request->from, $request->to])
            ->orderBy('transaction_id', 'desc')
            ->get();
        return view('admin.lotMixing.view', [
            'transactions' => $transactions,
        ]);
    }
    public function filterLotMixingByGovernrate(Request $request)
    {
        $id = $request->from;

        $governorate = Governerate::find($id);
        $governorateCode = $governorate->governerate_code;
        $regions = Region::where('status', 1)->where('region_code', 'LIKE', $governorateCode . '%')->get();
        $transactions = Transaction::with('details')
            ->where('batch_number', 'LIKE', $governorateCode . '%')
            ->where('is_parent', 0)
            ->where('sent_to', 24)
            ->orderBy('transaction_id', 'desc')
            ->get();
        return response()->json([
            'view' => view('admin.lotMixing.view', [
                'transactions' => $transactions,
            ])->render(),
            'regions' => $regions
        ]);
    }
    public function filterLotMixingByRegion(Request $request)
    {
        $id = $request->from;
        $region = Region::find($id);
        $regionCode = $region->region_code;
        $villages = Village::where('status', 1)->where('village_code', 'LIKE', $regionCode . '%')->get();
        $transactions = Transaction::with('details')
            ->where('batch_number', 'LIKE', '%' . $regionCode . '%')
            ->where('is_parent', 0)
            ->where('sent_to', 24)
            ->orderBy('transaction_id', 'desc')
            ->get();
        return response()->json([
            'view' => view('admin.lotMixing.view', [
                'transactions' => $transactions,
            ])->render(),
            'villages' => $villages
        ]);
    }
    public function filterLotMixingByvillage(Request $request)
    {
        $id = $request->from;
        $village = Village::find($id);
        $villageCode = $village->village_code;
        $transactions = Transaction::with('details')
            ->where('batch_number', 'LIKE', '%' . $villageCode . '%')
            ->where('is_parent', 0)
            ->where('sent_to', 24)
            ->orderBy('transaction_id', 'desc')
            ->get();
        return response()->json([
            'view' => view('admin.lotMixing.view', [
                'transactions' => $transactions,
            ])->render()
        ]);
    }
}
