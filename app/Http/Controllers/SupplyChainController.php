<?php

namespace App\Http\Controllers;

use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Laravel\Ui\Presets\React;

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
                $transactions =  Transaction::whereYear('created_at', $year)->whereDate('created_at', $today)->whereHas('meta', function ($q) {
                    $q->where('key', 'drying_start_date');
                })->whereHas('log', function ($q) {
                    $q->where('action', 'sent')->whereIn('type', ['coffee_drying', 'coffee_drying_received', 'coffee_drying_send', 'sent_to_yemen']);
                })->where('sent_to', 10)->orderBy('transaction_id', 'desc')->get();
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
            'barLabel' => 'today'
        ]);
    }
    public function supplyChainDateFilter(Request $request)
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
                $transactions = Transaction::with('details')->whereBetween('created_at', [$request->from, $request->to])->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->get();
            } elseif ($sent == 10) {
                $transactions =  Transaction::whereBetween('created_at', [$request->from, $request->to])->whereHas('meta', function ($q) {
                    $q->where('key', 'drying_start_date');
                })->whereHas('log', function ($q) {
                    $q->where('action', 'sent')->whereIn('type', ['coffee_drying', 'coffee_drying_received', 'coffee_drying_send', 'sent_to_yemen']);
                })->where('sent_to', 10)->orderBy('transaction_id', 'desc')->get();
            } elseif ($sent == 7) {
                $transactions = Transaction::whereBetween('created_at', [$request->from, $request->to])->whereHas('log', function ($q) {
                    // $q->where('action', 'sent')->whereIn('type', ['special_processing', 'coffee_drying'])->where('entity_id', $centerId);
                    $q->where('action', 'sent')->where('type', 'special_processing');
                })->with('meta')->orderBy('transaction_id', 'desc')->get();
            } elseif ($sent == 14) {
                $transactions = Transaction::whereBetween('created_at', [$request->from, $request->to])->where('sent_to', 14)->where('ready_to_milled')
                    // ->whereHas('log', function ($q) {
                    // $q->whereIn('action', ['sent', 'received'])
                    // ->whereIn('type', ['sent_to_yemen', 'received_by_yemen', 'milling_coffee', 'sent_to_mill']);
                    // })
                    ->with('meta', 'child')
                    ->orderBy('transaction_id', 'desc')
                    ->get();
            } elseif ($sent == 21) {
                $transactions = Transaction::whereIn('sent_to', [20])
                    ->with(['meta', 'child'])->whereBetween('created_at', [$request->from, $request->to])
                    ->orderBy('transaction_id', 'desc')
                    ->get();
            } else {
                $transactions = Transaction::with(['details' => function ($query) {
                    $query->where('container_status', 0);
                }])->whereBetween('created_at', [$request->from, $request->to])->where('sent_to', $sent)->where('is_parent', 0)->get();
            }

            $weight = 0;
            foreach ($transactions as $transaction) {
                $weight += $transaction->details->sum('container_weight');
            }
            array_push($weightToday, $weight);
        }

        return view('admin.supplyChain.supply_chain_view', [
            'weightLabel' => $weightLabel,
            'managerName' => $managerName,
            'weightToday' => $weightToday,
            'barLabel' => 'Selected Interval'
        ]);
    }
    public function supplyChainDays(Request $request)
    {
        $weightLabel = [];
        $managerName = [];
        $weightToday = [];
        $carbon = Carbon::now();
        $year = $carbon->year;
        $date = $request->date;
        $sentTo = ['Coffe Buyer' => 2, 'On Drying Beds' => 10, 'Special processing' => 7, 'Ready To Be Milled' => 14, 'Milled' => 21, 'Export Green' => 0, 'Cascara' => 0, 'Local Coffee' => 0, 'In Transit' => 0, 'London' => 0, 'China Recieved' => 0];

        if ($date == 'today') {
            $date = Carbon::today()->toDateString();

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
                    $transactions =  Transaction::whereYear('created_at', $year)->whereDate('created_at', $today)->whereHas('meta', function ($q) {
                        $q->where('key', 'drying_start_date');
                    })->whereHas('log', function ($q) {
                        $q->where('action', 'sent')->whereIn('type', ['coffee_drying', 'coffee_drying_received', 'coffee_drying_send', 'sent_to_yemen']);
                    })->where('sent_to', 10)->orderBy('transaction_id', 'desc')->get();
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
                'barLabel' => 'Today'
            ]);
        } elseif ($date == 'yesterday') {
            $now = Carbon::now();
            $yesterday = Carbon::yesterday();
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
            $today = $carbon->toDateString();
            foreach ($sentTo as $key => $sent) {
                if ($sent == 2) {
                    $transactions = Transaction::with('details')->whereDate('created_at', $yesterday)->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->get();
                } elseif ($sent == 10) {
                    $transactions =  Transaction::whereYear('created_at', $year)->whereDate('created_at', $yesterday)->whereHas('meta', function ($q) {
                        $q->where('key', 'drying_start_date');
                    })->whereHas('log', function ($q) {
                        $q->where('action', 'sent')->whereIn('type', ['coffee_drying', 'coffee_drying_received', 'coffee_drying_send', 'sent_to_yemen']);
                    })->where('sent_to', 10)->orderBy('transaction_id', 'desc')->get();
                } elseif ($sent == 7) {
                    $transactions = Transaction::whereYear('created_at', $year)->whereDate('created_at', $yesterday)->whereHas('log', function ($q) {
                        // $q->where('action', 'sent')->whereIn('type', ['special_processing', 'coffee_drying'])->where('entity_id', $centerId);
                        $q->where('action', 'sent')->where('type', 'special_processing');
                    })->with('meta')->orderBy('transaction_id', 'desc')->get();
                } elseif ($sent == 14) {
                    $transactions = Transaction::whereYear('created_at', $year)->whereDate('created_at', $yesterday)->where('sent_to', 14)->where('ready_to_milled')
                        // ->whereHas('log', function ($q) {
                        // $q->whereIn('action', ['sent', 'received'])
                        // ->whereIn('type', ['sent_to_yemen', 'received_by_yemen', 'milling_coffee', 'sent_to_mill']);
                        // })
                        ->with('meta', 'child')
                        ->orderBy('transaction_id', 'desc')
                        ->get();
                } elseif ($sent == 21) {
                    $transactions = Transaction::whereIn('sent_to', [20])
                        ->with(['meta', 'child'])->whereDate('created_at', $yesterday)
                        ->orderBy('transaction_id', 'desc')
                        ->get();
                } else {
                    $transactions = Transaction::with(['details' => function ($query) {
                        $query->where('container_status', 0);
                    }])->whereDate('created_at', $yesterday)->where('sent_to', $sent)->where('is_parent', 0)->whereYear('created_at', $year)->get();
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
                'barLabel' => 'Yesterday'
            ]);
        } elseif ($date == 'lastmonth') {

            $date = Carbon::now();

            $lastMonth =  $date->subMonth()->format('m');
            $year = $date->year;

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
                    $transactions = Transaction::with('details')->whereMonth('created_at', $lastMonth)->whereYear('created_at', $year)->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->get();
                } elseif ($sent == 10) {
                    $transactions =  Transaction::whereYear('created_at', $year)->whereMonth('created_at', $lastMonth)->whereYear('created_at', $year)->whereHas('meta', function ($q) {
                        $q->where('key', 'drying_start_date');
                    })->whereHas('log', function ($q) {
                        $q->where('action', 'sent')->whereIn('type', ['coffee_drying', 'coffee_drying_received', 'coffee_drying_send', 'sent_to_yemen']);
                    })->where('sent_to', 10)->orderBy('transaction_id', 'desc')->get();
                } elseif ($sent == 7) {
                    $transactions = Transaction::whereYear('created_at', $year)->whereMonth('created_at', $lastMonth)->whereYear('created_at', $year)->whereHas('log', function ($q) {
                        // $q->where('action', 'sent')->whereIn('type', ['special_processing', 'coffee_drying'])->where('entity_id', $centerId);
                        $q->where('action', 'sent')->where('type', 'special_processing');
                    })->with('meta')->orderBy('transaction_id', 'desc')->get();
                } elseif ($sent == 14) {
                    $transactions = Transaction::whereYear('created_at', $year)->whereMonth('created_at', $lastMonth)->whereYear('created_at', $year)->where('sent_to', 14)->where('ready_to_milled')
                        // ->whereHas('log', function ($q) {
                        // $q->whereIn('action', ['sent', 'received'])
                        // ->whereIn('type', ['sent_to_yemen', 'received_by_yemen', 'milling_coffee', 'sent_to_mill']);
                        // })
                        ->with('meta', 'child')
                        ->orderBy('transaction_id', 'desc')
                        ->get();
                } elseif ($sent == 21) {
                    $transactions = Transaction::whereIn('sent_to', [20])
                        ->with(['meta', 'child'])->whereMonth('created_at', $lastMonth)->whereYear('created_at', $year)
                        ->orderBy('transaction_id', 'desc')
                        ->get();
                } else {
                    $transactions = Transaction::with(['details' => function ($query) {
                        $query->where('container_status', 0);
                    }])->whereMonth('created_at', $lastMonth)->whereYear('created_at', $year)->where('sent_to', $sent)->where('is_parent', 0)->whereYear('created_at', $year)->get();
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
                'barLabel' => 'Last Month'
            ]);
        } elseif ($date == 'currentyear') {

            $date = Carbon::now();


            $year = $date->year;

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
                    $transactions = Transaction::whereYear('created_at', $year)->with('details')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->get();
                } elseif ($sent == 10) {
                    $transactions =  Transaction::whereYear('created_at', $year)->whereHas('meta', function ($q) {
                        $q->where('key', 'drying_start_date');
                    })->whereHas('log', function ($q) {
                        $q->where('action', 'sent')->whereIn('type', ['coffee_drying', 'coffee_drying_received', 'coffee_drying_send', 'sent_to_yemen']);
                    })->where('sent_to', 10)->orderBy('transaction_id', 'desc')->get();
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
                array_push($weightToday, $weight);
            }

            return view('admin.supplyChain.index', [
                'weightLabel' => $weightLabel,
                'managerName' => $managerName,
                'weightToday' => $weightToday,
                'barLabel' => 'Current Year'
            ]);
        } elseif ($date == 'lastyear') {

            $date = Carbon::now();


            $year = $date->year;
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
                $year = $date->year - 1;
                if ($sent == 2) {
                    $transactions = Transaction::with('details')->whereYear('created_at', $year)->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->get();
                } elseif ($sent == 10) {
                    $transactions =  Transaction::whereYear('created_at', $year)->whereHas('meta', function ($q) {
                        $q->where('key', 'drying_start_date');
                    })->whereHas('log', function ($q) {
                        $q->where('action', 'sent')->whereIn('type', ['coffee_drying', 'coffee_drying_received', 'coffee_drying_send', 'sent_to_yemen']);
                    })->where('sent_to', 10)->orderBy('transaction_id', 'desc')->get();
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
                        ->with(['meta', 'child'])->whereYear('created_at', $year)
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
                array_push($weightToday, $weight);
            }

            return view('admin.supplyChain.index', [
                'weightLabel' => $weightLabel,
                'managerName' => $managerName,
                'weightToday' => $weightToday,
                'barLabel' => 'Last year'
            ]);
        } elseif ($date == 'weekToDate') {

            $now = Carbon::now();
            $start = $now->startOfWeek(Carbon::SUNDAY)->toDateString();
            $end = $now->endOfWeek(Carbon::SATURDAY)->toDateString();


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
                    $transactions = Transaction::with('details')->whereBetween('created_at', [$start, $end])->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->get();
                } elseif ($sent == 10) {
                    $transactions =  Transaction::whereYear('created_at', $year)->whereBetween('created_at', [$start, $end])->whereHas('meta', function ($q) {
                        $q->where('key', 'drying_start_date');
                    })->whereHas('log', function ($q) {
                        $q->where('action', 'sent')->whereIn('type', ['coffee_drying', 'coffee_drying_received', 'coffee_drying_send', 'sent_to_yemen']);
                    })->where('sent_to', 10)->orderBy('transaction_id', 'desc')->get();
                } elseif ($sent == 7) {
                    $transactions = Transaction::whereYear('created_at', $year)->whereBetween('created_at', [$start, $end])->whereHas('log', function ($q) {
                        // $q->where('action', 'sent')->whereIn('type', ['special_processing', 'coffee_drying'])->where('entity_id', $centerId);
                        $q->where('action', 'sent')->where('type', 'special_processing');
                    })->with('meta')->orderBy('transaction_id', 'desc')->get();
                } elseif ($sent == 14) {
                    $transactions = Transaction::whereYear('created_at', $year)->whereBetween('created_at', [$start, $end])->where('sent_to', 14)->where('ready_to_milled')
                        // ->whereHas('log', function ($q) {
                        // $q->whereIn('action', ['sent', 'received'])
                        // ->whereIn('type', ['sent_to_yemen', 'received_by_yemen', 'milling_coffee', 'sent_to_mill']);
                        // })
                        ->with('meta', 'child')
                        ->orderBy('transaction_id', 'desc')
                        ->get();
                } elseif ($sent == 21) {
                    $transactions = Transaction::whereIn('sent_to', [20])
                        ->with(['meta', 'child'])->whereBetween('created_at', [$start, $end])
                        ->orderBy('transaction_id', 'desc')
                        ->get();
                } else {
                    $transactions = Transaction::with(['details' => function ($query) {
                        $query->where('container_status', 0);
                    }])->whereBetween('created_at', [$start, $end])->where('sent_to', $sent)->where('is_parent', 0)->whereYear('created_at', $year)->get();
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
                'barLabel' => 'Week TO Date'
            ]);
        } elseif ($date == 'monthToDate') {


            $now = Carbon::now();
            $start = $now->startOfWeek(Carbon::SUNDAY)->toDateString();
            $end = $now->endOfWeek(Carbon::SATURDAY)->toDateString();


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
                    $transactions = Transaction::with('details')->whereBetween('created_at', [$start, $end])->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->get();
                } elseif ($sent == 10) {
                    $transactions =  Transaction::whereYear('created_at', $year)->whereBetween('created_at', [$start, $end])->whereHas('meta', function ($q) {
                        $q->where('key', 'drying_start_date');
                    })->whereHas('log', function ($q) {
                        $q->where('action', 'sent')->whereIn('type', ['coffee_drying', 'coffee_drying_received', 'coffee_drying_send', 'sent_to_yemen']);
                    })->where('sent_to', 10)->orderBy('transaction_id', 'desc')->get();
                } elseif ($sent == 7) {
                    $transactions = Transaction::whereYear('created_at', $year)->whereBetween('created_at', [$start, $end])->whereHas('log', function ($q) {
                        // $q->where('action', 'sent')->whereIn('type', ['special_processing', 'coffee_drying'])->where('entity_id', $centerId);
                        $q->where('action', 'sent')->where('type', 'special_processing');
                    })->with('meta')->orderBy('transaction_id', 'desc')->get();
                } elseif ($sent == 14) {
                    $transactions = Transaction::whereYear('created_at', $year)->whereBetween('created_at', [$start, $end])->where('sent_to', 14)->where('ready_to_milled')
                        // ->whereHas('log', function ($q) {
                        // $q->whereIn('action', ['sent', 'received'])
                        // ->whereIn('type', ['sent_to_yemen', 'received_by_yemen', 'milling_coffee', 'sent_to_mill']);
                        // })
                        ->with('meta', 'child')
                        ->orderBy('transaction_id', 'desc')
                        ->get();
                } elseif ($sent == 21) {
                    $transactions = Transaction::whereIn('sent_to', [20])
                        ->with(['meta', 'child'])->whereBetween('created_at', [$start, $end])
                        ->orderBy('transaction_id', 'desc')
                        ->get();
                } else {
                    $transactions = Transaction::with(['details' => function ($query) {
                        $query->where('container_status', 0);
                    }])->whereBetween('created_at', [$start, $end])->where('sent_to', $sent)->where('is_parent', 0)->whereYear('created_at', $year)->get();
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
                'barLabel' => 'Month To Date'
            ]);
        } elseif ($date == 'yearToDate') {

            $now = Carbon::now();
            $end = Carbon::tomorrow()->toDateString();
            $start = $now->startOfYear();


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
                    $transactions = Transaction::with('details')->whereBetween('created_at', [$start, $end])->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->get();
                } elseif ($sent == 10) {
                    $transactions =  Transaction::whereYear('created_at', $year)->whereBetween('created_at', [$start, $end])->whereHas('meta', function ($q) {
                        $q->where('key', 'drying_start_date');
                    })->whereHas('log', function ($q) {
                        $q->where('action', 'sent')->whereIn('type', ['coffee_drying', 'coffee_drying_received', 'coffee_drying_send', 'sent_to_yemen']);
                    })->where('sent_to', 10)->orderBy('transaction_id', 'desc')->get();
                } elseif ($sent == 7) {
                    $transactions = Transaction::whereYear('created_at', $year)->whereBetween('created_at', [$start, $end])->whereHas('log', function ($q) {
                        // $q->where('action', 'sent')->whereIn('type', ['special_processing', 'coffee_drying'])->where('entity_id', $centerId);
                        $q->where('action', 'sent')->where('type', 'special_processing');
                    })->with('meta')->orderBy('transaction_id', 'desc')->get();
                } elseif ($sent == 14) {
                    $transactions = Transaction::whereYear('created_at', $year)->whereBetween('created_at', [$start, $end])->where('sent_to', 14)->where('ready_to_milled')
                        // ->whereHas('log', function ($q) {
                        // $q->whereIn('action', ['sent', 'received'])
                        // ->whereIn('type', ['sent_to_yemen', 'received_by_yemen', 'milling_coffee', 'sent_to_mill']);
                        // })
                        ->with('meta', 'child')
                        ->orderBy('transaction_id', 'desc')
                        ->get();
                } elseif ($sent == 21) {
                    $transactions = Transaction::whereIn('sent_to', [20])
                        ->with(['meta', 'child'])->whereBetween('created_at', [$start, $end])
                        ->orderBy('transaction_id', 'desc')
                        ->get();
                } else {
                    $transactions = Transaction::with(['details' => function ($query) {
                        $query->where('container_status', 0);
                    }])->whereBetween('created_at', [$start, $end])->where('sent_to', $sent)->where('is_parent', 0)->whereYear('created_at', $year)->get();
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
                'barLabel' => 'Year To Date'
            ]);
        }
    }
}
