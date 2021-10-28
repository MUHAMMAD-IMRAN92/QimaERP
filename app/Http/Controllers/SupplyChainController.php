<?php

namespace App\Http\Controllers;

use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SupplyChainController extends Controller
{
    public function supplyChain()
    {
        $sentTo = ['Coffe Buyer' => 2, 'On Drying Beds' => 10, 'Special processing' => 7, 'Ready To Be Milled' => 14, 'Milled' => 21, 'Export Green' => 0, 'Cascara' => 0, 'Local Coffee' => 0, 'In Transit' => 0, 'London' => 0, 'China Recieved' => 0];
        $weightLabel = [];
        $managerName = [];
        $carbon = Carbon::now();
        $year = $carbon->year;
        foreach ($sentTo as $key => $sent) {
            if ($sent == 2) {
                $transactions = Transaction::with('details')->whereYear('created_at', $year)->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->get();
            } elseif ($sent == 10) {
                $transactions = Transaction::whereHas('meta', function ($q) {
                    $q->where('key', 'drying_start_date');
                })->whereHas('log', function ($q) {
                    $q->where('action', 'sent')->whereIn('type', ['coffee_drying', 'coffee_drying_received', 'coffee_drying_send', 'sent_to_yemen']);
                })->where('sent_to', 10)->whereYear('created_at', $year)->orderBy('transaction_id', 'desc')->get();
            } elseif ($sent == 7) {
                $transactions = Transaction::whereYear('created_at', $year)->whereHas('log', function ($q) {
                    // $q->where('action', 'sent')->whereIn('type', ['special_processing', 'coffee_drying'])->where('entity_id', $centerId);
                    $q->where('action', 'sent')->where('type', 'special_processing');
                })->with('meta')->orderBy('transaction_id', 'desc')->get();
            } elseif ($sent == 14) {
                $transactions = Transaction::whereYear('created_at', $year)->where('sent_to', 14)->where('ready_to_milled')
                    // ->whereHas('log', function ($q) {
                    // $q->whereIn('action', ['sent', 'received'])
                    // ->whereIn('type', ['sent_to_yemen', 'received_by_yemen', 'milling_coffee', 'sent_to_mill']);
                    // })
                    ->with('meta', 'child')
                    ->orderBy('transaction_id', 'desc')
                    ->get();
            } elseif ($sent == 21) {
                $transactions = Transaction::whereIn('sent_to', [20])
                    ->with(['meta', 'child'])
                    ->orderBy('transaction_id', 'desc')
                    ->get();
            } else {
                $transactions = Transaction::with(['details' => function ($query) {
                    $query->where('container_status', 0);
                }])->where('sent_to', $sent)->where('is_parent', 0)->whereYear('created_at', $year)->get();
            }

            $weight = 0;
            foreach ($transactions as $transaction) {
                $weight += $transaction->details->sum('container_weight');
            }
            array_push($weightLabel, $weight);
            array_push($managerName, $key);
        }
        $weightToday = [];
        $carbon = Carbon::today();
        $today = $carbon->toDateString();
        foreach ($sentTo as $key => $sent) {
            if ($sent == 2) {
                $transactions = Transaction::with('details')->whereDate('created_at', $today)->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->get();
            } elseif ($sent == 10) {
                $transactions = Transaction::whereYear('created_at', $year)->whereDate('created_at', $today)->whereHas('log', function ($q) {
                    $q->where('action', 'sent')->whereIn('type', ['coffee_drying', 'coffee_drying_received', 'coffee_drying_send', 'sent_to_yemen']);
                })->with(['meta' => function ($q) {
                    $q->where('key', 'drying_start_date');
                }])->orderBy('transaction_id', 'desc')->get();
            } elseif ($sent == 7) {
                $transactions = Transaction::whereYear('created_at', $year)->whereDate('created_at', $today)->whereHas('log', function ($q) {
                    // $q->where('action', 'sent')->whereIn('type', ['special_processing', 'coffee_drying'])->where('entity_id', $centerId);
                    $q->where('action', 'sent')->where('type', 'special_processing');
                })->with('meta')->orderBy('transaction_id', 'desc')->get();
            } elseif ($sent == 14) {
                $transactions = Transaction::whereYear('created_at', $year)->whereDate('created_at', $today)->where('sent_to', 14)->where('ready_to_milled')
                    // ->whereHas('log', function ($q) {
                    // $q->whereIn('action', ['sent', 'received'])
                    // ->whereIn('type', ['sent_to_yemen', 'received_by_yemen', 'milling_coffee', 'sent_to_mill']);
                    // })
                    ->with('meta', 'child')
                    ->orderBy('transaction_id', 'desc')
                    ->get();
            } elseif ($sent == 21) {
                $transactions = Transaction::whereIn('sent_to', [20])
                    ->with(['meta', 'child'])->whereDate('created_at', $today)
                    ->orderBy('transaction_id', 'desc')
                    ->get();
            } else {
                $transactions = Transaction::with(['details' => function ($query) {
                    $query->where('container_status', 0);
                }])->whereDate('created_at', $today)->where('sent_to', $sent)->where('is_parent', 0)->whereYear('created_at', $year)->get();
            }

            $weight = 0;
            foreach ($transactions as $transaction) {
                $weight += $transaction->details->sum('container_weight');
            }
            array_push($weightToday, $weight);
        }

        return view('admin.supplyChain.index', [
            'weightLabel' => $weightLabel,
            'managerName' => $managerName,
            'weightToday' => $weightToday,
        ]);
    }
}
