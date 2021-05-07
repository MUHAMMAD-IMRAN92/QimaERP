<?php

namespace App\Http\Controllers;

use App\User;
use App\Farmer;
use App\Region;
use App\Village;
use App\LoginUser;
use App\Governerate;
use App\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        $farmers = Farmer::all();
        $regions = Region::all();
        $transactions = Transaction::with('details')->where('sent_to', 2)->get();
        $totalWeight = 0;
        $totalPrice = 0;
        foreach ($transactions as $transaction) {
            $weight = $transaction->details->sum('container_weight');
            $price = 0;
            $farmer_code = Str::beforeLast($transaction->batch_number, '-');

            $farmerPrice = optional(Farmer::where('farmer_code', $farmer_code)->first())->price_per_kg;
            if (!$farmerPrice) {
                $village_code = Str::beforeLast($farmer_code, '-');
                $price = Village::where('village_code',  $village_code)->first()->price_per_kg;
            } else {
                $price = Farmer::where('farmer_code', $farmer_code)->first()->price_per_kg;
            }

            $totalPrice += $weight * $price;
            $totalWeight += $weight;
        }

        return view('dashboard', [
            'governorate' => $governorate,
            'farmers' => $farmers,
            'villages' => $villages,
            'regions' => $regions,
            'totalWeight' => $totalWeight,
            'totalPrice' => $totalPrice
        ]);
    }
    public function regionByDate(Request $request)
    {
        $governorates = Governerate::whereBetween('created_at', [$request->from, $request->to])->get();
        $regions = Region::whereBetween('created_at', [$request->from, $request->to])->get();
        $villages = Village::whereBetween('created_at', [$request->from, $request->to])->get();
        $farmers = Farmer::whereBetween('created_at', [$request->from, $request->to])->get();
        $transactions = Transaction::with('details')->where('sent_to', 2)->whereBetween('created_at', [$request->from, $request->to])->get();

        $totalWeight = 0;
        $totalPrice = 0;
        foreach ($transactions as $transaction) {
            $weight = $transaction->details->sum('container_weight');
            $price = 0;
            $farmer_code = Str::beforeLast($transaction->batch_number, '-');

            $farmerPrice = optional(Farmer::where('farmer_code', $farmer_code)->first())->price_per_kg;
            if (!$farmerPrice) {
                $village_code = Str::beforeLast($farmer_code, '-');
                if ($village_code) {
                    $price = Village::where('village_code',  $village_code)->first()->price_per_kg;
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

        return view('admin.region.views.filter_transctions', [
            'governorates' =>   $governorates,
            'regions' => $regions,
            'villages' => $villages,
            'total_coffee' => $totalWeight,
            'totalPrice' => $totalPrice

        ]);
    }
    public function regionByDays(Request $request)
    {
        $date = $request->date;

        if ($date == 'today') {
            $date = Carbon::today()->toDateString();

            $farmers = Farmer::whereDate('created_at',  $date)->get();
            $villages = Village::whereDate('created_at',  $date)->get();
            $governorates = Governerate::whereDate('created_at',  $date)->get();
            $regions = Region::whereDate('created_at',  $date)->get();
            $transactions = Transaction::with('details')->where('sent_to', 2)->whereDate('created_at', $date)->get();

            $totalWeight = 0;
            $totalPrice = 0;
            if ($transactions) {
                foreach ($transactions as $transaction) {
                    $weight = $transaction->details->sum('container_weight');
                    $price = 0;
                    $farmer_code = Str::beforeLast($transaction->batch_number, '-');

                    $farmerPrice = optional(Farmer::where('farmer_code', $farmer_code)->first())->price_per_kg;
                    if (!$farmerPrice) {
                        $village_code = Str::beforeLast($farmer_code, '-');
                        if ($village_code) {
                            $price = Village::where('village_code',  $village_code)->first()->price_per_kg;
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


            return view('admin.region.views.filter_transctions', [
                'governorates' =>   $governorates,
                'regions' => $regions,
                'villages' => $villages,
                'total_coffee' => $totalWeight,
                'totalPrice' => $totalPrice

            ]);
        } elseif ($date == 'yesterday') {
            $now = Carbon::now();
            $yesterday = Carbon::yesterday();

            $farmers = Farmer::whereDate('created_at',  $yesterday)->get();
            $villages = Village::whereDate('created_at',  $yesterday)->get();
            $governorates = Governerate::whereDate('created_at',  $yesterday)->get();
            $regions = Region::whereDate('created_at',  $yesterday)->get();
            $transactions  = Transaction::with('details')->where('sent_to', 2)->whereDate('created_at', $yesterday)->get();
            $totalWeight = 0;
            $totalPrice = 0;
            if ($transactions) {

                foreach ($transactions as $transaction) {
                    $weight = $transaction->details->sum('container_weight');
                    $price = 0;
                    $farmer_code = Str::beforeLast($transaction->batch_number, '-');

                    $farmerPrice = optional(Farmer::where('farmer_code', $farmer_code)->first())->price_per_kg;
                    if (!$farmerPrice) {
                        $village_code = Str::beforeLast($farmer_code, '-');
                        if ($village_code) {
                            $price = Village::where('village_code',  $village_code)->first()->price_per_kg;
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

            return view('admin.region.views.filter_transctions', [
                'governorates' =>   $governorates,
                'regions' => $regions,
                'villages' => $villages,
                'total_coffee' => $totalWeight,
                'totalPrice' => $totalPrice

            ]);
        } elseif ($date == 'lastmonth') {

            $date = Carbon::now();

            $lastMonth =  $date->subMonth()->format('m');
            $year = $date->year;

            $farmers = Farmer::whereMonth('created_at', $lastMonth)->whereYear('created_at', $year)->get();

            $villages = Village::whereMonth('created_at', $lastMonth)->whereYear('created_at', $year)->get();
            $governorates = Governerate::whereMonth('created_at', $lastMonth)->whereYear('created_at', $year)->get();
            $regions = Region::whereMonth('created_at', $lastMonth)->whereYear('created_at', $year)->get();
            $transactions = Transaction::with('details')->where('sent_to', 2)->whereMonth('created_at', $lastMonth)->whereYear('created_at', $year)->get();
            $totalWeight = 0;
            $totalPrice = 0;
            if ($transactions) {

                foreach ($transactions as $transaction) {
                    $weight = $transaction->details->sum('container_weight');
                    $price = 0;
                    $farmer_code = Str::beforeLast($transaction->batch_number, '-');

                    $farmerPrice = optional(Farmer::where('farmer_code', $farmer_code)->first())->price_per_kg;
                    if (!$farmerPrice) {
                        $village_code = Str::beforeLast($farmer_code, '-');
                        if ($village_code) {
                            $price = Village::where('village_code',  $village_code)->first()->price_per_kg;
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

            return view('admin.region.views.filter_transctions', [
                'governorates' =>   $governorates,
                'regions' => $regions,
                'villages' => $villages,
                'total_coffee' => $totalWeight,
                'totalPrice' => $totalPrice

            ]);
        } elseif ($date == 'currentyear') {

            $date = Carbon::now();


            $year = $date->year;


            $farmers = Farmer::whereYear('created_at', $year)->get();

            $villages = Village::whereYear('created_at', $year)->get();
            $governorates = Governerate::whereYear('created_at', $year)->get();
            $regions = Region::whereYear('created_at', $year)->get();
            $transactions = Transaction::with('details')->where('sent_to', 2)->whereYear('created_at', $year)->get();
            $totalWeight = 0;
            $totalPrice = 0;
            if ($transactions) {

                foreach ($transactions as $transaction) {
                    $weight = $transaction->details->sum('container_weight');
                    $price = 0;
                    $farmer_code = Str::beforeLast($transaction->batch_number, '-');

                    $farmerPrice = optional(Farmer::where('farmer_code', $farmer_code)->first())->price_per_kg;
                    if (!$farmerPrice) {
                        $village_code = Str::beforeLast($farmer_code, '-');
                        if ($village_code) {
                            $price = Village::where('village_code',  $village_code)->first()->price_per_kg;
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

            return view('admin.region.views.filter_transctions', [
                'governorates' =>   $governorates,
                'regions' => $regions,
                'villages' => $villages,
                'total_coffee' => $totalWeight,
                'totalPrice' => $totalPrice

            ]);
        } elseif ($date == 'lastyear') {

            $date = Carbon::now();


            $year = $date->year - 1;

            $farmers = Farmer::whereYear('created_at', $year)->get();
            $farmers = Farmer::whereYear('created_at', $year)->get();

            $villages = Village::whereYear('created_at', $year)->get();
            $governorates = Governerate::whereYear('created_at', $year)->get();
            $regions = Region::whereYear('created_at', $year)->get();
            $transactions = Transaction::with('details')->where('sent_to', 2)->whereYear('created_at', $year)->get();
            $totalWeight = 0;
            $totalPrice = 0;
            if ($transactions) {

                foreach ($transactions as $transaction) {
                    $weight = $transaction->details->sum('container_weight');
                    $price = 0;
                    $farmer_code = Str::beforeLast($transaction->batch_number, '-');

                    $farmerPrice = optional(Farmer::where('farmer_code', $farmer_code)->first())->price_per_kg;
                    if (!$farmerPrice) {
                        $village_code = Str::beforeLast($farmer_code, '-');
                        if ($village_code) {
                            $price = Village::where('village_code',  $village_code)->first()->price_per_kg;
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

            return view('admin.region.views.filter_transctions', [
                'governorates' =>   $governorates,
                'regions' => $regions,
                'villages' => $villages,
                'total_coffee' => $totalWeight,
                'totalPrice' => $totalPrice

            ]);
        } elseif ($date == 'weekToDate') {

            $now = Carbon::now();
            $start = $now->startOfWeek(Carbon::SUNDAY);
            $end = $now->endOfWeek(Carbon::SATURDAY);



            $farmers = Farmer::whereBetween('created_at', [$start, $end])->get();



            $villages = Village::whereBetween('created_at', [$start, $end])->get();
            $governorates = Governerate::whereBetween('created_at', [$start, $end])->get();
            $regions = Region::whereBetween('created_at', [$start, $end])->get();
            $transactions = Transaction::with('details')->where('sent_to', 2)->whereBetween('created_at', [$start, $end])->get();
            $totalWeight = 0;
            $totalPrice = 0;
            if ($transactions) {

                foreach ($transactions as $transaction) {
                    $weight = $transaction->details->sum('container_weight');
                    $price = 0;
                    $farmer_code = Str::beforeLast($transaction->batch_number, '-');

                    $farmerPrice = optional(Farmer::where('farmer_code', $farmer_code)->first())->price_per_kg;
                    if (!$farmerPrice) {
                        $village_code = Str::beforeLast($farmer_code, '-');
                        if ($village_code) {
                            $price = Village::where('village_code',  $village_code)->first()->price_per_kg;
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

            return view('admin.region.views.filter_transctions', [
                'governorates' =>   $governorates,
                'regions' => $regions,
                'villages' => $villages,
                'total_coffee' => $totalWeight,
                'totalPrice' => $totalPrice

            ]);
        } elseif ($date == 'monthToDate') {

            $now = Carbon::now();
            $date = Carbon::today()->toDateString();
            $start = $now->firstOfMonth();

            $farmers = Farmer::whereBetween('created_at', [$start, $date])->get();


            $villages = Village::whereBetween('created_at', [$start, $date])->get();
            $governorates = Governerate::whereBetween('created_at', [$start, $date])->get();
            $regions = Region::whereBetween('created_at', [$start, $date])->get();
            $transactions = Transaction::with('details')->where('sent_to', 2)->whereBetween('created_at', [$start, $date])->get();
            $totalWeight = 0;
            $totalPrice = 0;
            if ($transactions) {

                foreach ($transactions as $transaction) {
                    $weight = $transaction->details->sum('container_weight');
                    $price = 0;
                    $farmer_code = Str::beforeLast($transaction->batch_number, '-');

                    $farmerPrice = optional(Farmer::where('farmer_code', $farmer_code)->first())->price_per_kg;
                    if (!$farmerPrice) {
                        $village_code = Str::beforeLast($farmer_code, '-');
                        if ($village_code) {
                            $price = Village::where('village_code',  $village_code)->first()->price_per_kg;
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

            return view('admin.region.views.filter_transctions', [
                'governorates' =>   $governorates,
                'regions' => $regions,
                'villages' => $villages,
                'total_coffee' => $totalWeight,
                'totalPrice' => $totalPrice

            ]);
        } elseif ($date == 'yearToDate') {

            $now = Carbon::now();
            $date = Carbon::today()->toDateString();
            $start = $now->startOfYear();

            $farmers = Farmer::whereBetween('created_at', [$start, $date])->get();



            $villages = Village::whereBetween('created_at', [$start, $date])->get();
            $governorates = Governerate::whereBetween('created_at', [$start, $date])->get();
            $regions = Region::whereBetween('created_at', [$start, $date])->get();
            $transactions = Transaction::with('details')->where('sent_to', 2)->whereBetween('created_at', [$start, $date])->get();
            $totalWeight = 0;
            $totalPrice = 0;
            if ($transactions) {

                foreach ($transactions as $transaction) {
                    $weight = $transaction->details->sum('container_weight');
                    $price = 0;
                    $farmer_code = Str::beforeLast($transaction->batch_number, '-');

                    $farmerPrice = optional(Farmer::where('farmer_code', $farmer_code)->first())->price_per_kg;
                    if (!$farmerPrice) {
                        $village_code = Str::beforeLast($farmer_code, '-');
                        if ($village_code) {
                            $price = Village::where('village_code',  $village_code)->first()->price_per_kg;
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

            return view('admin.region.views.filter_transctions', [
                'governorates' =>   $governorates,
                'regions' => $regions,
                'villages' => $villages,
                'total_coffee' => $totalWeight,
                'totalPrice' => $totalPrice

            ]);
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
}
