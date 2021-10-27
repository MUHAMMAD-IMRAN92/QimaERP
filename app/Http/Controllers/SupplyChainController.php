<?php

namespace App\Http\Controllers;

use App\Transaction;
use Illuminate\Http\Request;

class SupplyChainController extends Controller
{
    public function supplyChain()
    {
        $sentTo = ['Coffe Buyer' => 2, 'Coffee Buyer Manager' => 3, 'Center Manager' => 4, 'Processing Manager' => 5, 'Special processing' =>  7, 'Coffee drying' => 10, 'Yemen operative' => 13, 'Mill operative' => 17,   'Coffee Sorting' => 22,  'Yemen Pack Operative ' => 33, 'Yemen Local Market' => 193, 'Shipping' => 39, 'Uk Warehouse' => 43, 'China Warehouse' => 474];
        $weightLabel = [];
        $managerName = [];
        foreach ($sentTo as $key => $sent) {
            if ($sent == 2) {
                $transactions = Transaction::with('details')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->get();
            } elseif ($sent == 193) {
                $transactions = Transaction::where('is_parent', 0)
                    ->whereIn('sent_to', [20, 193, 194, 195, 201])
                    ->whereIn('transaction_type', [3, 5, 6])
                    ->whereHas(
                        'details',
                        function ($q) {
                            $q->where('container_status', 0);
                        },
                        '>',
                        0
                    )->with(['details' => function ($query) {
                        $query->where('container_status', 0)->with('metas');
                    }])->with(['meta', 'child'])
                    ->orderBy('transaction_id', 'desc')
                    ->get();
            } elseif ($sent == 3) {
                $transactions = Transaction::where('is_parent', 0)->where('transaction_status', 'sent')->whereHas('transactionDetail', function ($q) {
                    $q->where('container_status', 0);
                }, '>', 0)->with(['transactionDetail' => function ($query) {
                    $query->where('container_status', 0);
                }])->with('log')->orderBy('transaction_id', 'desc')->get();
            } elseif ($sent == 4) {
                $transactions = Transaction::where('is_parent', 0)->where('transaction_status', 'received')->whereHas('transactionLog', function ($q) {
                    $q->where('action', 'received')->where('type', 'center');
                })->with('transactionDetail')->with('log')->orderBy('transaction_id', 'desc')->get();
            } elseif ($sent == 10) {
                $transactions = Transaction::where('is_parent', 0)->whereHas('log', function ($q) {
                    $q->where('action', 'sent')->whereIn('type', ['coffee_drying', 'coffee_drying_received', 'coffee_drying_send', 'sent_to_yemen']);
                })->whereHas('transactionDetail', function ($q) {
                    $q->where('container_status', 0);
                }, '>', 0)
                    //->doesntHave('isReference')
                    ->with(['transactionDetail' => function ($query) {
                        $query->where('container_status', 0);
                    }])->with('meta')->orderBy('transaction_id', 'desc')->get();
            } elseif ($sent == 5) {
                $transactions = Transaction::where('transaction_status', 'sent')->whereHas('log', function ($q) {
                    $q->where('action', 'sent')->whereIn('type', ['special_processing', 'coffee_drying']);
                })->with('transactionDetail')->orderBy('transaction_id', 'desc')->get();
            } elseif ($sent == 7) {
                $transactions = Transaction::where('is_parent', 0)->whereHas('log', function ($q) {
                    // $q->where('action', 'sent')->whereIn('type', ['special_processing', 'coffee_drying'])->where('entity_id', $centerId);
                    $q->where('action', 'sent')->where('type', 'special_processing');
                })->whereHas('transactionDetail', function ($q) {
                    $q->where('container_status', 0);
                }, '>', 0)->with(['transactionDetail' => function ($query) {
                    $query->where('container_status', 0);
                }])->with('meta')->orderBy('transaction_id', 'desc')->get();
            } elseif ($sent == 13) {
                $transactions = Transaction::where('is_parent', 0)
                    ->whereHas('log', function ($q) {
                        $q->whereIn('action', ['sent', 'received'])
                            ->whereIn('type', ['sent_to_yemen', 'received_by_yemen', 'milling_coffee', 'sent_to_mill']);
                    })->whereHas(
                        'transactionDetail',
                        function ($q) {
                            $q->where('container_status', 0);
                        },
                        '>',
                        0
                    )->with(['transactionDetail' => function ($query) {
                        $query->where('container_status', 0);
                    }])->with('meta', 'child')
                    ->orderBy('transaction_id', 'desc')
                    ->get();
            } elseif ($sent == 17) {
                $transactions = Transaction::where('is_parent', 0)->where('transaction_type', 1)
                    // ->whereHas('log', function ($q) {
                    //     $q->whereIn('action', ['sent', 'received'])
                    //         ->whereIn('type', ['received_by_mill', 'sent_to_mill', 'sent_to_market', 'sent_to_sorting']);
                    // })
                    ->whereIn('sent_to', [15, 17])
                    ->whereHas(
                        'details',
                        function ($q) {
                            $q->where('container_status', 0);
                        },
                        '>',
                        0
                    )->with(['details' => function ($query) {
                        $query->where('container_status', 0)->with('metas');
                    }])->with(['meta', 'child'])
                    ->orderBy('transaction_id', 'desc')
                    ->get();
            } elseif ($sent == 22) {
                $transactions = Transaction::selectRaw('transactions.*')->where('is_parent', 0)
                    ->where('sent_to', 21)
                    ->orWhere(function ($query) {
                        $query->whereIn('sent_to', [22])
                            ->where('transaction_type', 1);
                    })
                    ->whereHas(
                        'details',
                        function ($q) {
                            $q->where('container_status', 0);
                        },
                        '>',
                        0
                    )->with(['details' => function ($query) {
                        $query->where('container_status', 0)->with('metas');
                    }])->with(['meta', 'child'])
                    ->orderBy('transaction_id', 'desc')
                    ->get();
            } elseif ($sent == 33) {
                $transactions = Transaction::where('is_parent', 0)
                    // ->whereHas('log', function ($q) {
                    //     $q->whereIn('action', ['sent'])
                    //         ->whereIn(
                    //             'type',
                    //             [
                    //                 'sent_to_po_packaging'
                    //             ]
                    //         );
                    // })
                    ->whereIn('sent_to', [31, 33, 34, 36])
                    ->whereHas(
                        'details',
                        function ($q) {
                            $q->where('container_status', 0);
                        },
                        '>',
                        0
                    )->with(['details' => function ($query) {
                        $query->where('container_status', 0)->with('metas');
                    }])->with(['meta', 'child'])
                    ->orderBy('transaction_id', 'desc')
                    ->get();
            } elseif ($sent == 39) {
                $transactions = Transaction::where('is_parent', 0)
                    // ->whereHas('log', function ($q) {
                    //     $q->whereIn('action', ['sent'])
                    //         ->whereIn(
                    //             'type',
                    //             [
                    //                 'sent_to_po_packaging'
                    //             ]
                    //         );
                    // })
                    ->whereIn('sent_to', [36, 39])
                    ->whereHas(
                        'details',
                        function ($q) {
                            $q->where('container_status', 0);
                        },
                        '>',
                        0
                    )->with(['details' => function ($query) {
                        $query->where('container_status', 0)->with('metas');
                    }])->with(['meta', 'child'])
                    ->orderBy('transaction_id', 'desc')
                    ->get();
            } elseif ($sent == 43) {
                $transactions = Transaction::where('is_parent', 0)
                    ->whereIn('sent_to', [41, 43, 472, 473])
                    ->whereHas(
                        'details',
                        function ($q) {
                            $q->where('container_status', 0);
                        },
                        '>',
                        0
                    )->with(['details' => function ($query) {
                        $query->where('container_status', 0)->with('metas');
                    }])->with(['meta', 'child'])
                    ->orderBy('transaction_id', 'desc')
                    ->get();
            } elseif ($sent == 474) {
                $transactions = Transaction::where('is_parent', 0)
                    ->whereIn('sent_to', [473, 474])
                    ->whereHas(
                        'details',
                        function ($q) {
                            $q->where('container_status', 0);
                        },
                        '>',
                        0
                    )->with(['details' => function ($query) {
                        $query->where('container_status', 0)->with('metas');
                    }])->with(['meta', 'child'])
                    ->orderBy('transaction_id', 'desc')
                    ->get();
            } else {
                $transactions = Transaction::with(['details' => function ($query) {
                    $query->where('container_status', 0);
                }])->where('sent_to', $sent)->where('is_parent', 0)->get();
            }

            $weight = 0;
            foreach ($transactions as $transaction) {
                $weight += $transaction->details->sum('container_weight');
            }
            array_push($weightLabel, $weight);
            array_push($managerName, $key);
        }
        return view('admin.supplyChain.index', [
            'weightLabel' => $weightLabel,
            'managerName' => $managerName,
        ]);
    }
}
