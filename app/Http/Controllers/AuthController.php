<?php

namespace App\Http\Controllers;

use App\User;
use App\Farmer;
use App\Region;
use App\Support;
use App\Village;
use App\LoginUser;
use App\FileSystem;
use App\Governerate;
use App\Transaction;
use App\TransactionDetail;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\API\UkWareHouse;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\VarDumper\Cloner\Data;

class AuthController extends Controller
{

    public function adminLogin()
    {

        //var_dump(Hash::make("123456"));exit;
        if (Auth::guard()->check()) {
            return redirect('admin/dashboard');
        }
        return view('admin.login');
    }

    public function dashboard()
    {
        $governorate = Governerate::all();
        $villages = Village::all();
        $regionWeight = collect();
        $farmerWeight = collect();

        $farmers = collect();
        $regions = collect();
        // $transactions = Transaction::where('batch_number', 'not like', '%BATCH%')->where('batch_number', 'not like', '%GR%')->where('batch_number', 'not like', '%SRG%')
        //     ->with(['details'])
        //     ->get();
        $regions = Region::all();
        $regionName = [];
        $regionQuantity = [];
        $govName = [];
        $govQuantity = [];
        foreach ($governorate as $govern) {
            $govCode = $govern->governerate_code;
            $weight = 0;
            $transactions = Transaction::where('batch_number', 'LIKE', '%' .  $govCode . '%')->where('batch_number', 'NOT LIKE', '%000%')->where('sent_to', 2)->with('details')->get();
            foreach ($transactions as $transaction) {
                $weight +=  $transaction->details->sum('container_weight');
            }
            array_push($govName, $govern->governerate_title);
            array_push($govQuantity, $weight);
        }
        foreach ($regions as $region) {
            $regionCode = $region->region_code;
            $weight = 0;
            $transactions = Transaction::where('batch_number', 'LIKE', '%' .  $regionCode . '%')->where('batch_number', 'NOT LIKE', '%000%')->where('sent_to', 2)->with('details')->get();
            foreach ($transactions as $transaction) {


                $weight +=  $transaction->details->sum('container_weight');
            }
            array_push($regionName, $region->region_title);
            array_push($regionQuantity, $weight);
            $regionWeight->push([
                'regionId' => $region->region_id,
                'weight' =>  $weight
            ]);
        }
        // return $regionWeight;
        $regionsByWeight = $regionWeight->sortBy('weight')->reverse()->values();
        $regions = $regionsByWeight->take(5)->pluck('regionId');
        $regions = Region::whereIn('region_id', $regions)->get();


        $farmers = Farmer::all();
        foreach ($farmers as $farmer) {
            $farmerCode = $farmer->farmer_code;
            $weight = 0;
            $transactions = Transaction::where('batch_number', 'LIKE', '%' .  $farmerCode . '%')->where('batch_number', 'NOT LIKE', '%000%')->where('sent_to', 2)->with('details')->get();
            foreach ($transactions as $transaction) {
                $weight +=  $transaction->details->sum('container_weight');
            }
            $farmerWeight->push([
                'farmerId' => $farmer->farmer_id,
                'weight' =>  $weight
            ]);
        }
        $farmerByWeight = $farmerWeight->sortBy('weight')->reverse()->values();
        $farmer = $farmerByWeight->take(5)->pluck('farmerId');
        $farmers = Farmer::whereIn('farmer_id', $farmer)->get();

        // foreach ($transactions as $transaction) {
        //     $ids->push([
        //         'transaction_id' => $transaction->transaction_id,
        //         'weight' =>  $transaction->details->sum('container_weight')
        //     ]);
        // }
        // $sorted = $ids->sortBy('weight');    
        // $top =   array_reverse($sorted->values()->take(-20)->toArray());
        // return array_reverse($top);
        // foreach ($top as $t) {
        //     $transaction =  Transaction::where('transaction_id', $t['transaction_id'])->first();
        //     $code =  explode('-', $transaction->batch_number)[3];
        //     // $farmers->push($code);
        //     $farmer = Farmer::where('farmer_code', 'LIKE', '%' . $code . '%')->first();
        //     if ($farmer != null) {

        //         if (!$farmers->contains($farmer)) {
        //             $farmers->push($farmer);
        //         }
        //     }
        //     $regionCode =  explode('-', $transaction->batch_number)[1];
        //     $region = Region::where('region_code', 'LIKE', '%' . $regionCode . '%')->first();
        //     if (!$regions->contains($region)) {
        //         $regions->push($region);
        //     }
        // }

        $transactions = Transaction::with('details')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->get();
        $totalWeight = 0;
        $totalPrice = 0;
        foreach ($transactions as $transaction) {
            $weight = $transaction->details->sum('container_weight');
            $price = 0;
            $farmer_code = Str::beforeLast($transaction->batch_number, '-');

            $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();
            if ($farmerPrice) {
                $farmerPrice = $farmerPrice->price_per_kg;
            }
            if (!$farmerPrice) {
                $village_code = Str::beforeLast($farmer_code, '-');
                $village = Village::where('village_code',  $village_code)->first();
                if ($village) {
                    $price = $village->price_per_kg;
                }
            } else {
                $farmer = Farmer::where('farmer_code', $farmer_code)->first();
                if ($farmer) {
                    $price = $farmer->price_per_kg;
                }
            }

            $totalPrice += $weight * $price;
            $totalWeight += $weight;
        }
        $now = Carbon::now();
        $currentYear = $now->year;
        $collection = Transaction::all();

        $transaction = Transaction::where('sent_to', 2)->orderBy('created_at', 'asc')->whereYear('created_at', $currentYear)->with('details')->get();
        // $grouped = $collection->groupBy('for');
        $grouped = $transaction->groupBy(function ($item) {
            return $item->created_at->format('m');
        });
        $createdAt = [];

        $quantity = [];


        foreach ($grouped as $key => $trans) {
            $weight = 0;
            foreach ($trans as $tran) {
                $weight += $tran->details->sum('container_weight');
            }
            $monthNum = $key;
            $monthName = date("F", mktime(0, 0, 0, $monthNum, 10));

            array_push($quantity, $weight);
            array_push($createdAt, $monthName);
        }
        $today = Carbon::today()->toDateString();
        $stocks = [];
        $YemenWarehouseTransactions =  Transaction::where('sent_to', 12)
            ->where('is_parent', 0)
            ->where('created_at', $today)
            ->where('is_special', 1)
            ->with('meta')
            ->get();
        $weight = 0;
        foreach ($YemenWarehouseTransactions as $key => $transaction) {
            $weight += $transaction->details->sum('container_weight');
        }
        array_push($stocks, ["wareHouse" => "Yemen", "today" => $weight, "end" => $weight]);

        $UKWarehouseTransactions =  Transaction::where('sent_to', 41)
            ->where('is_parent', 0)
            ->where('created_at', $today)

            ->where('is_special', 1)
            ->with('meta')
            ->get();
        $weight = 0;
        foreach ($UKWarehouseTransactions as $key => $transaction) {
            $weight += $transaction->details->sum('container_weight');
        }
        array_push($stocks, ["wareHouse" => "UK", "today" => $weight, "end" => $weight]);

        $ChinaWarehouseTransactions =  Transaction::where('sent_to', 473)
            ->where('created_at', $today)
            ->where('is_parent', 0)

            ->where('is_special', 1)
            ->with('meta')
            ->get();
        $weight = 0;
        foreach ($ChinaWarehouseTransactions as $key => $transaction) {
            $weight += $transaction->details->sum('container_weight');
        }
        array_push($stocks, ["wareHouse" => "China", "today" => $weight, "end" => $weight]);
        $nonspecialstocks = [];
        $YemenWarehouseTransactions =  Transaction::where('sent_to', 12)
            ->where('created_at', $today)
            ->where('is_parent', 0)
            ->where('is_special', 0)
            ->with('meta')
            ->get();
        $weight = 0;
        foreach ($YemenWarehouseTransactions as $key => $transaction) {
            $weight += $transaction->details->sum('container_weight');
        }
        array_push($nonspecialstocks, ["wareHouse" => "Yemen", "today" => $weight, "end" => $weight]);

        $UKWarehouseTransactions =  Transaction::where('sent_to', 41)
            ->where('created_at', $today)
            ->where('is_parent', 0)
            ->where('is_special', 0)
            ->with('meta')
            ->get();
        $weight = 0;
        foreach ($UKWarehouseTransactions as $key => $transaction) {
            $weight += $transaction->details->sum('container_weight');
        }
        array_push($nonspecialstocks, ["wareHouse" => "UK", "today" => $weight, "end" => $weight]);

        $ChinaWarehouseTransactions =  Transaction::where('sent_to', 473)
            ->where('created_at', $today)
            ->where('is_parent', 0)
            ->where('is_special', 0)
            ->with('meta')
            ->get();
        $weight = 0;
        foreach ($ChinaWarehouseTransactions as $key => $transaction) {
            $weight += $transaction->details->sum('container_weight');
        }
        array_push($nonspecialstocks, ["wareHouse" => "China", "today" => $weight, "end" => $weight]);
        return view('dashboard', [
            'governorate' => $governorate,
            'farmers' => $farmers->take(5),
            'villages' => $villages,
            'regions' => $regions->take(5),
            'totalWeight' => $totalWeight,
            'totalPrice' => $totalPrice,
            'quantity' => $quantity,
            'createdAt' => $createdAt,
            'regionName' => $regionName,
            'regionQuantity' => $regionQuantity,
            'govName' => $govName,
            'govQuantity' => $govQuantity,
            'stock' => $stocks,
            'nonspecialstock' => $nonspecialstocks,
        ]);
    }
    public function dashboardByDate(Request $request)
    {
        $governorates = Governerate::whereBetween('created_at', [$request->from, $request->to])->get();
        $regions = Region::whereBetween('created_at', [$request->from, $request->to])->get();
        $villages = Village::whereBetween('created_at', [$request->from, $request->to])->get();
        $farmers = Farmer::whereBetween('created_at', [$request->from, $request->to])->get();
        $transactions = Transaction::with('details')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->whereBetween('created_at', [$request->from, $request->to])->get();

        $totalWeight = 0;
        $totalPrice = 0;
        foreach ($transactions as $transaction) {
            $weight = $transaction->details->sum('container_weight');
            $price = 0;
            $farmer_code = Str::beforeLast($transaction->batch_number, '-');

            $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();
            if ($farmerPrice) {
                $farmerPrice = $farmerPrice->price_per_kg;
            }
            if (!$farmerPrice) {
                $village_code = Str::beforeLast($farmer_code, '-');
                if ($village_code) {
                    $price = Village::where('village_code',  $village_code)->first();
                    if ($price) {
                        $price = $price->price_per_kg;
                    }
                }
            } else {
                $farmer = Farmer::where('farmer_code', $farmer_code)->first();
                if ($farmer) {
                    $price =  $farmer->price_per_kg;
                }
            }

            $totalPrice += $weight * $price;
            $totalWeight += $weight;
        }

        return view('filter_transctions', [
            'governorates' =>   $governorates,
            'regions' => $regions,
            'villages' => $villages,
            'farmers' => $farmers,
            'total_coffee' => $totalWeight,
            'totalPrice' => $totalPrice

        ]);
    }
    public function dashboardByDays(Request $request)
    {
        $date = $request->date;

        if ($date == 'today') {
            $date = Carbon::today()->toDateString();

            $farmers = Farmer::whereDate('created_at',  $date)->get();
            $villages = Village::whereDate('created_at',  $date)->get();
            $governorates = Governerate::whereDate('created_at',  $date)->get();
            $regions = Region::whereDate('created_at',  $date)->get();
            $transactions = Transaction::with('details')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->whereDate('created_at', $date)->get();

            $totalWeight = 0;
            $totalPrice = 0;
            if ($transactions) {
                foreach ($transactions as $transaction) {
                    $weight = $transaction->details->sum('container_weight');
                    $price = 0;
                    $farmer_code = Str::beforeLast($transaction->batch_number, '-');

                    $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();
                    if ($farmerPrice) {
                        $farmerPrice = $farmerPrice->price_per_kg;
                    }
                    if (!$farmerPrice) {
                        $village_code = Str::beforeLast($farmer_code, '-');
                        if ($village_code) {
                            $price = Village::where('village_code',  $village_code)->first();
                            if ($price) {
                                $price = $price->price_per_kg;
                            }
                        }
                    } else {
                        $farmer = Farmer::where('farmer_code', $farmer_code)->first();
                        if ($farmer) {
                            $price =  $farmer->price_per_kg;
                        }
                    }

                    $totalPrice += $weight * $price;
                    $totalWeight += $weight;
                }
            }


            return view('filter_transctions', [
                'governorates' =>   $governorates,
                'regions' => $regions,
                'villages' => $villages,
                'farmers' => $farmers,
                'total_coffee' => $totalWeight,
                'totalPrice' => $totalPrice

            ])->render();
        } elseif ($date == 'yesterday') {
            $now = Carbon::now();
            $yesterday = Carbon::yesterday();

            $farmers = Farmer::whereDate('created_at',  $yesterday)->get();
            $villages = Village::whereDate('created_at',  $yesterday)->get();
            $governorates = Governerate::whereDate('created_at',  $yesterday)->get();
            $regions = Region::whereDate('created_at',  $yesterday)->get();
            $transactions  = Transaction::with('details')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->whereDate('created_at', $yesterday)->get();
            $totalWeight = 0;
            $totalPrice = 0;
            if ($transactions) {

                foreach ($transactions as $transaction) {
                    $weight = $transaction->details->sum('container_weight');
                    $price = 0;
                    $farmer_code = Str::beforeLast($transaction->batch_number, '-');

                    $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();
                    if ($farmerPrice) {
                        $farmerPrice = $farmerPrice->price_per_kg;
                    }
                    if (!$farmerPrice) {
                        $village_code = Str::beforeLast($farmer_code, '-');
                        if ($village_code) {
                            $price = Village::where('village_code',  $village_code)->first();
                            if ($price) {
                                $price = $price->price_per_kg;
                            }
                        }
                    } else {
                        $farmer = Farmer::where('farmer_code', $farmer_code)->first();
                        if ($farmer) {
                            $price =  $farmer->price_per_kg;
                        }
                    }

                    $totalPrice += $weight * $price;
                    $totalWeight += $weight;
                }
            }

            return view('filter_transctions', [
                'governorates' =>   $governorates,
                'regions' => $regions,
                'villages' => $villages,
                'farmers' => $farmers,
                'total_coffee' => $totalWeight,
                'totalPrice' => $totalPrice

            ])->render();
        } elseif ($date == 'lastmonth') {

            $date = Carbon::now();

            $lastMonth =  $date->subMonth()->format('m');
            $year = $date->year;

            $farmers = Farmer::whereMonth('created_at', $lastMonth)->whereYear('created_at', $year)->get();

            $villages = Village::whereMonth('created_at', $lastMonth)->whereYear('created_at', $year)->get();
            $governorates = Governerate::whereMonth('created_at', $lastMonth)->whereYear('created_at', $year)->get();
            $regions = Region::whereMonth('created_at', $lastMonth)->whereYear('created_at', $year)->get();
            $transactions = Transaction::with('details')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->whereMonth('created_at', $lastMonth)->whereYear('created_at', $year)->get();
            $totalWeight = 0;
            $totalPrice = 0;
            if ($transactions) {

                foreach ($transactions as $transaction) {
                    $weight = $transaction->details->sum('container_weight');
                    $price = 0;
                    $farmer_code = Str::beforeLast($transaction->batch_number, '-');

                    $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();
                    if ($farmerPrice) {
                        $farmerPrice = $farmerPrice->price_per_kg;
                    }
                    if (!$farmerPrice) {
                        $village_code = Str::beforeLast($farmer_code, '-');
                        if ($village_code) {
                            $price = Village::where('village_code',  $village_code)->first();
                            if ($price) {
                                $price = $price->price_per_kg;
                            }
                        }
                    } else {
                        $farmer = Farmer::where('farmer_code', $farmer_code)->first();
                        if ($farmer) {
                            $price =  $farmer->price_per_kg;
                        }
                    }

                    $totalPrice += $weight * $price;
                    $totalWeight += $weight;
                }
            }

            return view('filter_transctions', [
                'governorates' =>   $governorates,
                'regions' => $regions,
                'villages' => $villages,
                'farmers' => $farmers,
                'total_coffee' => $totalWeight,
                'totalPrice' => $totalPrice

            ])->render();
        } elseif ($date == 'currentyear') {

            $date = Carbon::now();


            $year = $date->year;


            $farmers = Farmer::whereYear('created_at', $year)->get();

            $villages = Village::whereYear('created_at', $year)->get();
            $governorates = Governerate::whereYear('created_at', $year)->get();
            $regions = Region::whereYear('created_at', $year)->get();
            $transactions = Transaction::with('details')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->whereYear('created_at', $year)->get();
            $totalWeight = 0;
            $totalPrice = 0;
            if ($transactions) {

                foreach ($transactions as $transaction) {
                    $weight = $transaction->details->sum('container_weight');
                    $price = 0;
                    $farmer_code = Str::beforeLast($transaction->batch_number, '-');

                    $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();
                    if ($farmerPrice) {
                        $farmerPrice = $farmerPrice->price_per_kg;
                    }
                    if (!$farmerPrice) {
                        $village_code = Str::beforeLast($farmer_code, '-');
                        if ($village_code) {
                            $price = Village::where('village_code',  $village_code)->first();
                            if ($price) {
                                $price = $price->price_per_kg;
                            }
                        }
                    } else {
                        $farmer = Farmer::where('farmer_code', $farmer_code)->first();
                        if ($farmer) {
                            $price =  $farmer->price_per_kg;
                        }
                    }

                    $totalPrice += $weight * $price;
                    $totalWeight += $weight;
                }
            }

            return view('filter_transctions', [
                'governorates' =>   $governorates,
                'regions' => $regions,
                'villages' => $villages,
                'farmers' => $farmers,
                'total_coffee' => $totalWeight,
                'totalPrice' => $totalPrice

            ])->render();
        } elseif ($date == 'lastyear') {

            $date = Carbon::now();


            $year = $date->year - 1;

            $farmers = Farmer::whereYear('created_at', $year)->get();
            $farmers = Farmer::whereYear('created_at', $year)->get();

            $villages = Village::whereYear('created_at', $year)->get();
            $governorates = Governerate::whereYear('created_at', $year)->get();
            $regions = Region::whereYear('created_at', $year)->get();
            $transactions = Transaction::with('details')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->whereYear('created_at', $year)->get();
            $totalWeight = 0;
            $totalPrice = 0;
            if ($transactions) {

                foreach ($transactions as $transaction) {
                    $weight = $transaction->details->sum('container_weight');
                    $price = 0;
                    $farmer_code = Str::beforeLast($transaction->batch_number, '-');

                    $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();
                    if ($farmerPrice) {
                        $farmerPrice = $farmerPrice->price_per_kg;
                    }
                    if (!$farmerPrice) {
                        $village_code = Str::beforeLast($farmer_code, '-');
                        if ($village_code) {
                            $price = Village::where('village_code',  $village_code)->first();
                            if ($price) {
                                $price = $price->price_per_kg;
                            }
                        }
                    } else {
                        $farmer = Farmer::where('farmer_code', $farmer_code)->first();
                        if ($farmer) {
                            $price =  $farmer->price_per_kg;
                        }
                    }

                    $totalPrice += $weight * $price;
                    $totalWeight += $weight;
                }
            }

            return view('filter_transctions', [
                'governorates' =>   $governorates,
                'regions' => $regions,
                'villages' => $villages,
                'farmers' => $farmers,
                'total_coffee' => $totalWeight,
                'totalPrice' => $totalPrice

            ])->render();
        } elseif ($date == 'weekToDate') {

            $now = Carbon::now();
            $start = $now->startOfWeek(Carbon::SUNDAY)->toDateString();
            $end = $now->endOfWeek(Carbon::SATURDAY)->toDateString();



            $farmers = Farmer::whereBetween('created_at', [$start, $end])->get();



            $villages = Village::whereBetween('created_at', [$start, $end])->get();
            $governorates = Governerate::whereBetween('created_at', [$start, $end])->get();
            $regions = Region::whereBetween('created_at', [$start, $end])->get();
            $transactions = Transaction::with('details')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->whereBetween('created_at', [$start, $end])->get();
            $totalWeight = 0;
            $totalPrice = 0;
            if ($transactions) {

                foreach ($transactions as $transaction) {
                    $weight = $transaction->details->sum('container_weight');
                    $price = 0;
                    $farmer_code = Str::beforeLast($transaction->batch_number, '-');

                    $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();
                    if ($farmerPrice) {
                        $farmerPrice = $farmerPrice->price_per_kg;
                    }
                    if (!$farmerPrice) {
                        $village_code = Str::beforeLast($farmer_code, '-');
                        if ($village_code) {
                            $price = Village::where('village_code',  $village_code)->first();
                            if ($price) {
                                $price = $price->price_per_kg;
                            }
                        }
                    } else {
                        $farmer = Farmer::where('farmer_code', $farmer_code)->first();
                        if ($farmer) {
                            $price =  $farmer->price_per_kg;
                        }
                    }

                    $totalPrice += $weight * $price;
                    $totalWeight += $weight;
                }
            }

            return view('filter_transctions', [
                'governorates' =>   $governorates,
                'regions' => $regions,
                'villages' => $villages,
                'farmers' => $farmers,
                'total_coffee' => $totalWeight,
                'totalPrice' => $totalPrice

            ])->render();
        } elseif ($date == 'monthToDate') {

            $now = Carbon::now();
            $date = Carbon::tomorrow()->toDateString();
            $start = $now->firstOfMonth();

            $farmers = Farmer::whereBetween('created_at', [$start, $date])->get();


            $villages = Village::whereBetween('created_at', [$start, $date])->get();
            $governorates = Governerate::whereBetween('created_at', [$start, $date])->get();
            $regions = Region::whereBetween('created_at', [$start, $date])->get();
            $transactions = Transaction::with('details')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->whereBetween('created_at', [$start, $date])->get();
            $totalWeight = 0;
            $totalPrice = 0;
            if ($transactions) {

                foreach ($transactions as $transaction) {
                    $weight = $transaction->details->sum('container_weight');
                    $price = 0;
                    $farmer_code = Str::beforeLast($transaction->batch_number, '-');

                    $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();
                    if ($farmerPrice) {
                        $farmerPrice = $farmerPrice->price_per_kg;
                    }
                    if (!$farmerPrice) {
                        $village_code = Str::beforeLast($farmer_code, '-');
                        if ($village_code) {
                            $price = Village::where('village_code',  $village_code)->first();
                            if ($price) {
                                $price = $price->price_per_kg;
                            }
                        }
                    } else {
                        $farmer = Farmer::where('farmer_code', $farmer_code)->first();
                        if ($farmer) {
                            $price =  $farmer->price_per_kg;
                        }
                    }

                    $totalPrice += $weight * $price;
                    $totalWeight += $weight;
                }
            }

            return view('filter_transctions', [
                'governorates' =>   $governorates,
                'regions' => $regions,
                'villages' => $villages,
                'farmers' => $farmers,
                'total_coffee' => $totalWeight,
                'totalPrice' => $totalPrice

            ])->render();
        } elseif ($date == 'yearToDate') {

            $now = Carbon::now();
            $date = Carbon::tomorrow()->toDateString();
            $start = $now->startOfYear();

            $farmers = Farmer::whereBetween('created_at', [$start, $date])->get();

            $villages = Village::whereBetween('created_at', [$start, $date])->get();
            $governorates = Governerate::whereBetween('created_at', [$start, $date])->get();
            $regions = Region::whereBetween('created_at', [$start, $date])->get();
            $transactions = Transaction::with('details')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->whereBetween('created_at', [$start, $date])->get();
            $totalWeight = 0;
            $totalPrice = 0;
            if ($transactions) {

                foreach ($transactions as $transaction) {
                    $weight = $transaction->details->sum('container_weight');
                    $price = 0;
                    $farmer_code = Str::beforeLast($transaction->batch_number, '-');

                    $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();
                    if ($farmerPrice) {
                        $farmerPrice = $farmerPrice->price_per_kg;
                    }
                    if (!$farmerPrice) {
                        $village_code = Str::beforeLast($farmer_code, '-');
                        if ($village_code) {
                            $price = Village::where('village_code',  $village_code)->first();
                            if ($price) {
                                $price = $price->price_per_kg;
                            }
                        }
                    } else {
                        $farmer = Farmer::where('farmer_code', $farmer_code)->first();
                        if ($farmer) {
                            $price =  $farmer->price_per_kg;
                        }
                    }

                    $totalPrice += $weight * $price;
                    $totalWeight += $weight;
                }
            }

            return view('filter_transctions', [
                'governorates' =>   $governorates,
                'regions' => $regions,
                'villages' => $villages,
                'farmers' => $farmers,
                'total_coffee' => $totalWeight,
                'totalPrice' => $totalPrice

            ])->render();
        }
    }
    public function adminPostLogin(Request $request, $remember = true)
    {
        $email = $request->email;
        $password = $request->password;
        if (Auth::guard()->attempt(['email' => $email, 'password' => $password], $remember)) {
            return redirect('admin/dashboard');
        }
        return redirect()->back()->with('error', 'Invalid email or password');
    }

    public function adminLogout()
    {
        Auth::logout();
        return redirect('admin/login');
    }
    public function byDate(Request $request)
    {
        $data = $request->from;
        dd($data);
    }
    public function endDateAjax(Request $request)
    {
        $today = Carbon::today()->toDateString();
        $endDate = $request->endDate;
        $stocks = [];
        $YemenWarehouseTransactions =  Transaction::where('sent_to', 12)
            ->where('created_at',  $today)
            ->where('is_parent', 0)
            ->where('is_special', 1)
            ->with('meta')
            ->get();
        $todayweight = 0;
        foreach ($YemenWarehouseTransactions as $key => $transaction) {
            $todayweight += $transaction->details->sum('container_weight');
        }
        $YemenWarehouseTransactions =  Transaction::where('sent_to', 12)
            ->whereBetween('created_at', [$endDate, $today])
            ->where('is_parent', 0)
            ->where('is_special', 1)
            ->with('meta')
            ->get();
        $weight = 0;
        foreach ($YemenWarehouseTransactions as $key => $transaction) {
            $weight += $transaction->details->sum('container_weight');
        }
        array_push($stocks, ["wareHouse" => "Yemen", "today" => $todayweight, "end" => $weight]);
        $UKWarehouseTransactions =  Transaction::where('sent_to', 41)
            ->where('is_parent', 0)
            ->where('created_at', $today)
            ->where('is_special', 1)
            ->with('meta')
            ->get();
        $todayweight = 0;
        foreach ($UKWarehouseTransactions as $key => $transaction) {
            $todayweight += $transaction->details->sum('container_weight');
        }
        $UKWarehouseTransactions =  Transaction::where('sent_to', 41)
            ->whereBetween('created_at', [$endDate, $today])
            ->where('is_parent', 0)
            ->where('is_special', 1)
            ->with('meta')
            ->get();
        $weight = 0;
        foreach ($UKWarehouseTransactions as $key => $transaction) {
            $weight += $transaction->details->sum('container_weight');
        }
        array_push($stocks, ["wareHouse" => "UK", "today" => $todayweight, "end" => $weight]);
        $ChinaWarehouseTransactions =  Transaction::where('sent_to', 473)
            ->where('created_at', $today)
            ->where('is_parent', 0)
            ->where('is_special', 1)
            ->with('meta')
            ->get();
        $todayweight = 0;
        foreach ($ChinaWarehouseTransactions as $key => $transaction) {
            $todayweight += $transaction->details->sum('container_weight');
        }
        $ChinaWarehouseTransactions =  Transaction::where('sent_to', 473)
            ->whereBetween('created_at', [$endDate, $today])
            ->where('is_parent', 0)
            ->where('is_special', 1)
            ->with('meta')
            ->get();
        $weight = 0;
        foreach ($ChinaWarehouseTransactions as $key => $transaction) {
            $weight += $transaction->details->sum('container_weight');
        }

        array_push($stocks, ["wareHouse" => "China", "today" => $todayweight, "end" => $weight]);
        return view('admin.special_stock_view', [
            'stock' => $stocks
        ])->render();
    }

    public function endDateAjaxNonSpecial(Request $request)

    {
        $today = Carbon::today()->toDateString();
        $endDate = $request->endDate;
        $nonspecialstock = [];
        $YemenWarehouseTransactions =  Transaction::where('sent_to', 12)
            ->where('created_at',  $today)
            ->where('is_parent', 0)
            ->where('is_special', 0)
            ->with('meta')
            ->get();
        $todayweight = 0;
        foreach ($YemenWarehouseTransactions as $key => $transaction) {
            $todayweight += $transaction->details->sum('container_weight');
        }
        $YemenWarehouseTransactions =  Transaction::where('sent_to', 12)
            ->whereBetween('created_at', [$endDate, $today])
            ->where('is_parent', 0)
            ->where('is_special', 0)
            ->with('meta')
            ->get();
        $weight = 0;
        foreach ($YemenWarehouseTransactions as $key => $transaction) {
            $weight += $transaction->details->sum('container_weight');
        }
        array_push($nonspecialstock, ["wareHouse" => "Yemen", "today" => $todayweight, "end" => $weight]);
        $UKWarehouseTransactions =  Transaction::where('sent_to', 41)
            ->where('is_parent', 0)
            ->where('created_at', $today)
            ->where('is_special', 0)
            ->with('meta')
            ->get();
        $todayweight = 0;
        foreach ($UKWarehouseTransactions as $key => $transaction) {
            $todayweight += $transaction->details->sum('container_weight');
        }
        $UKWarehouseTransactions =  Transaction::where('sent_to', 41)
            ->whereBetween('created_at', [$endDate, $today])
            ->where('is_parent', 0)
            ->where('is_special', 0)
            ->with('meta')
            ->get();
        $weight = 0;
        foreach ($UKWarehouseTransactions as $key => $transaction) {
            $weight += $transaction->details->sum('container_weight');
        }
        array_push($nonspecialstock, ["wareHouse" => "UK", "today" => $todayweight, "end" => $weight]);
        $ChinaWarehouseTransactions =  Transaction::where('sent_to', 473)
            ->where('created_at', $today)
            ->where('is_special', 0)
            ->where('is_parent', 0)
            ->with('meta')
            ->get();
        $todayweight = 0;
        foreach ($ChinaWarehouseTransactions as $key => $transaction) {
            $todayweight += $transaction->details->sum('container_weight');
        }
        $ChinaWarehouseTransactions =  Transaction::where('sent_to', 473)
            ->whereBetween('created_at', [$endDate, $today])
            ->where('is_parent', 0)
            ->where('is_special', 0)
            ->with('meta')
            ->get();
        $weight = 0;
        foreach ($ChinaWarehouseTransactions as $key => $transaction) {
            $weight += $transaction->details->sum('container_weight');
        }

        array_push($nonspecialstock, ["wareHouse" => "China", "today" => $todayweight, "end" => $weight]);
        return view('admin.nonspecial_stock_view', [
            'nonspecialstock' => $nonspecialstock
        ])->render();
    }
    public function support()
    {
        $support = Support::all();

        return view('admin.support', [
            'support' => $support,
        ]);
    }
    public function viewSupport($id)
    {
        $support = Support::find($id);
        if ($file = FileSystem::where('file_id', $support->file_id)->first()) {
            $support->image = $file->user_file_name;
        }
        return view('admin.single_suport', [
            'support' => $support,
        ]);
    }
}
