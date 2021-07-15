<?php

namespace App\Http\Controllers;

use App\User;
use App\Farmer;
use App\Region;
use App\Village;
use App\Governerate;
use App\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Role;

class CoffeeBuyerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $governorates = Governerate::all();
        $regions = Region::all();
        $villages = Village::all();

        $coffeeBuyingManagers = Role::with(['users'])->where('name', 'Coffee Buying Manager')->first()->users;
        $coffeeBuyers = Role::with('users')->where('name', 'Coffee Buyer')->first()->users;
        $coffeeBuyingManagers = $coffeeBuyingManagers->map(function ($coffeeBuyingManager) {
            $coffeeBuyingManager->image = $coffeeBuyingManager->getImage();
            $coffeeBuyingManager->first_purchase = $coffeeBuyingManager->firstPurchase();
            $coffeeBuyingManager->last_purchase = $coffeeBuyingManager->lastPurchase();
            $coffeeBuyingManager = $coffeeBuyingManager->nonSpecialPrice();
            $coffeeBuyingManager = $coffeeBuyingManager->specialPrice();
            return   $coffeeBuyingManager;
        });
        $coffeeBuyers = $coffeeBuyers->map(function ($coffeeBuyer) {
            $coffeeBuyer->image = $coffeeBuyer->getImage();
            $coffeeBuyer->first_purchase = $coffeeBuyer->firstPurchase();
            $coffeeBuyer->last_purchase = $coffeeBuyer->lastPurchase();
            $coffeeBuyer = $coffeeBuyer->nonSpecialPrice();
            $coffeeBuyer = $coffeeBuyer->specialPrice();
            return   $coffeeBuyer;
        });

        // return $coffeeBuyingManagers;
        return view('admin.coffeBuyer.all_coffee_buyer', [
            'coffeeBuyerMangers' =>  $coffeeBuyingManagers,
            'coffeeBuyers' => $coffeeBuyers,
            'governorates' => $governorates,
            'regions' => $regions,
            'villages' => $villages,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function filterByDate(Request $request)
    {
        $from = Carbon::parse($request->from)->format('Y-m-d  H:i:s');
        $to = Carbon::parse($request->to)->format('Y-m-d  H:i:s');

        $governorates = Governerate::all();
        $regions = Region::all();
        $villages = Village::all();
        $coffeeBuyingManagers = Role::with('users')->where('name', 'Coffee Buying Manager')->first()->users;

        $coffeeBuyers = Role::with('users')->where('name', 'Coffee Buyer')->first()->users;

        $coffeeBuyingManagers =  $coffeeBuyingManagers->whereBetween('created_at', [$from, $to]);
        $coffeeBuyers =  $coffeeBuyers->whereBetween('created_at', [$from,  $to]);
        $coffeeBuyingManagers = $coffeeBuyingManagers->map(function ($coffeeBuyingManager) {
            $coffeeBuyingManager->image = $coffeeBuyingManager->getImage();
            $coffeeBuyingManager->first_purchase = $coffeeBuyingManager->firstPurchase();
            $coffeeBuyingManager->last_purchase = $coffeeBuyingManager->lastPurchase();
            $coffeeBuyingManager->specialcoffee = $coffeeBuyingManager->special();
            $coffeeBuyingManager = $coffeeBuyingManager->nonSpecialPrice();
            $coffeeBuyingManager = $coffeeBuyingManager->specialPrice();
            return   $coffeeBuyingManager;
        });
        $coffeeBuyers = $coffeeBuyers->map(function ($coffeeBuyer) {
            $coffeeBuyer->image = $coffeeBuyer->getImage();
            $coffeeBuyer->first_purchase = $coffeeBuyer->firstPurchase();
            $coffeeBuyer->last_purchase = $coffeeBuyer->lastPurchase();
            $coffeeBuyer->specialcoffee = $coffeeBuyer->special();
            $coffeeBuyer = $coffeeBuyer->nonSpecialPrice();
            $coffeeBuyer = $coffeeBuyer->specialPrice();
            return   $coffeeBuyer;
        });
        return view('admin.coffeBuyer.views.index', [
            'coffeeBuyerMangers' =>  $coffeeBuyingManagers,
            'coffeeBuyers' => $coffeeBuyers,
            'governorates' => $governorates,
            'regions' => $regions,
            'villages' => $villages,
        ]);
    }

    public function coffeeBuyerByDate(Request $request)
    {
        $date = $request->date;
        if ($date == 'today') {
            $date = Carbon::today()->toDateString();
            $governorates = Governerate::all();
            $regions = Region::all();
            $villages = Village::all();
            $coffeeBuyingManagers = Role::with(['users' => function ($query) use ($date) {
                $query->whereDate('created_at', $date);
            }])->where('name', 'Coffee Buying Manager')->first()->users;

            $coffeeBuyers = Role::with(['users' => function ($query) use ($date) {
                $query->whereDate('created_at', $date);
            }])->where('name', 'Coffee Buyer')->first()->users;



            $coffeeBuyingManagers = $coffeeBuyingManagers->map(function ($coffeeBuyingManager) {
                $coffeeBuyingManager->image = $coffeeBuyingManager->getImage();
                $coffeeBuyingManager->first_purchase = $coffeeBuyingManager->firstPurchase();
                $coffeeBuyingManager->last_purchase = $coffeeBuyingManager->lastPurchase();
                $coffeeBuyingManager->specialcoffee = $coffeeBuyingManager->special();
                $coffeeBuyingManager = $coffeeBuyingManager->nonSpecialPrice();
                $coffeeBuyingManager = $coffeeBuyingManager->specialPrice();
                return   $coffeeBuyingManager;
            });
            $coffeeBuyers = $coffeeBuyers->map(function ($coffeeBuyer) {
                $coffeeBuyer->image = $coffeeBuyer->getImage();
                $coffeeBuyer->first_purchase = $coffeeBuyer->firstPurchase();
                $coffeeBuyer->last_purchase = $coffeeBuyer->lastPurchase();
                $coffeeBuyer->specialcoffee = $coffeeBuyer->special();
                $coffeeBuyer = $coffeeBuyer->nonSpecialPrice();
                $coffeeBuyer = $coffeeBuyer->specialPrice();
                return   $coffeeBuyer;
            });
            return view('admin.coffeBuyer.all_coffee_buyer', [
                'coffeeBuyerMangers' =>  $coffeeBuyingManagers,
                'coffeeBuyers' => $coffeeBuyers,
                'governorates' => $governorates,
                'regions' => $regions,
                'villages' => $villages,
            ]);
        } elseif ($date == 'yesterday') {
            $now = Carbon::now();
            $date = Carbon::yesterday()->toDateString();

            $coffeeBuyingManagers = Role::with(['users' => function ($query) use ($date) {
                $query->whereDate('created_at', $date);
            }])->where('name', 'Coffee Buying Manager')->first()->users;

            $coffeeBuyers = Role::with(['users' => function ($query) use ($date) {
                $query->whereDate('created_at', $date);
            }])->where('name', 'Coffee Buyer')->first()->users;
            $coffeeBuyingManagers = $coffeeBuyingManagers->map(function ($coffeeBuyingManager) {
                $coffeeBuyingManager->image = $coffeeBuyingManager->getImage();
                $coffeeBuyingManager->first_purchase = $coffeeBuyingManager->firstPurchase();
                $coffeeBuyingManager->last_purchase = $coffeeBuyingManager->lastPurchase();
                $coffeeBuyingManager->specialcoffee = $coffeeBuyingManager->special();
                $coffeeBuyingManager = $coffeeBuyingManager->nonSpecialPrice();
                $coffeeBuyingManager = $coffeeBuyingManager->specialPrice();
                return   $coffeeBuyingManager;
            });
            $coffeeBuyers = $coffeeBuyers->map(function ($coffeeBuyer) {
                $coffeeBuyer->image = $coffeeBuyer->getImage();
                $coffeeBuyer->first_purchase = $coffeeBuyer->firstPurchase();
                $coffeeBuyer->last_purchase = $coffeeBuyer->lastPurchase();
                $coffeeBuyer->specialcoffee = $coffeeBuyer->special();
                $coffeeBuyer = $coffeeBuyer->nonSpecialPrice();
                $coffeeBuyer = $coffeeBuyer->specialPrice();
                return   $coffeeBuyer;
            });
            $governorates = Governerate::all();
            $regions = Region::all();
            $villages = Village::all();


            return view('admin.coffeBuyer.all_coffee_buyer', [
                'coffeeBuyerMangers' =>  $coffeeBuyingManagers,
                'coffeeBuyers' => $coffeeBuyers,
                'governorates' => $governorates,
                'regions' => $regions,
                'villages' => $villages,
            ]);
        } elseif ($date == 'lastmonth') {

            $date = Carbon::now();

            $lastMonth =  $date->subMonth()->format('m');

            $year = $date->year;


            $coffeeBuyingManagers = Role::with(['users' => function ($query) use ($lastMonth, $year) {
                $query->whereMonth('created_at', $lastMonth)->whereYear('created_at',  $year);
            }])->where('name', 'Coffee Buying Manager')->first()->users;

            $coffeeBuyers = Role::with(['users' => function ($query) use ($lastMonth, $year) {
                $query->whereMonth('created_at', $lastMonth)->whereYear('created_at',  $year);
            }])->where('name', 'Coffee Buyer')->first()->users;

            $coffeeBuyingManagers = $coffeeBuyingManagers->map(function ($coffeeBuyingManager) {
                $coffeeBuyingManager->image = $coffeeBuyingManager->getImage();
                $coffeeBuyingManager->first_purchase = $coffeeBuyingManager->firstPurchase();
                $coffeeBuyingManager->last_purchase = $coffeeBuyingManager->lastPurchase();
                $coffeeBuyingManager->specialcoffee = $coffeeBuyingManager->special();
                $coffeeBuyingManager = $coffeeBuyingManager->nonSpecialPrice();
                $coffeeBuyingManager = $coffeeBuyingManager->specialPrice();
                return   $coffeeBuyingManager;
            });
            $coffeeBuyers = $coffeeBuyers->map(function ($coffeeBuyer) {
                $coffeeBuyer->image = $coffeeBuyer->getImage();
                $coffeeBuyer->first_purchase = $coffeeBuyer->firstPurchase();
                $coffeeBuyer->last_purchase = $coffeeBuyer->lastPurchase();
                $coffeeBuyer->specialcoffee = $coffeeBuyer->special();
                $coffeeBuyer = $coffeeBuyer->nonSpecialPrice();
                $coffeeBuyer = $coffeeBuyer->specialPrice();
                return   $coffeeBuyer;
            });
            $governorates = Governerate::all();
            $regions = Region::all();
            $villages = Village::all();

            return view('admin.coffeBuyer.all_coffee_buyer', [
                'coffeeBuyerMangers' =>  $coffeeBuyingManagers,
                'coffeeBuyers' => $coffeeBuyers,
                'governorates' => $governorates,
                'regions' => $regions,
                'villages' => $villages,
            ]);
        } elseif ($date == 'currentyear') {

            $date = Carbon::now();

            $year = $date->year;

            $governorates = Governerate::all();
            $regions = Region::all();
            $villages = Village::all();
            $role = Role::with(['users' => function ($query) use ($year) {
                $query->whereYear('created_at', $year);
            }])->where('name', 'Coffee Buying Manager')->first();
            $coffeeBuyingManagers = $role->users;
            $role = Role::with(['users' => function ($query) use ($year) {
                $query->whereYear('created_at', $year);
            }])->where('name', 'Coffee Buyer')->first();
            $coffeeBuyers = $role->users;

            $coffeeBuyingManagers = $coffeeBuyingManagers->map(function ($coffeeBuyingManager) {
                $coffeeBuyingManager->image = $coffeeBuyingManager->getImage();
                $coffeeBuyingManager->first_purchase = $coffeeBuyingManager->firstPurchase();
                $coffeeBuyingManager->last_purchase = $coffeeBuyingManager->lastPurchase();
                $coffeeBuyingManager->specialcoffee = $coffeeBuyingManager->special();
                $coffeeBuyingManager = $coffeeBuyingManager->nonSpecialPrice();
                $coffeeBuyingManager = $coffeeBuyingManager->specialPrice();
                return   $coffeeBuyingManager;
            });
            $coffeeBuyers = $coffeeBuyers->map(function ($coffeeBuyer) {
                $coffeeBuyer->image = $coffeeBuyer->getImage();
                $coffeeBuyer->first_purchase = $coffeeBuyer->firstPurchase();
                $coffeeBuyer->last_purchase = $coffeeBuyer->lastPurchase();
                $coffeeBuyer->specialcoffee = $coffeeBuyer->special();
                $coffeeBuyer = $coffeeBuyer->nonSpecialPrice();
                $coffeeBuyer = $coffeeBuyer->specialPrice();
                return   $coffeeBuyer;
            });



            return view('admin.coffeBuyer.all_coffee_buyer', [
                'coffeeBuyerMangers' =>  $coffeeBuyingManagers,
                'coffeeBuyers' => $coffeeBuyers,
                'governorates' => $governorates,
                'regions' => $regions,
                'villages' => $villages,
            ]);
        } elseif ($date == 'lastyear') {

            $date = Carbon::now();


            $year = $date->year - 1;

            $role = Role::with(['users' => function ($query) use ($year) {
                $query->whereYear('created_at', $year);
            }])->where('name', 'Coffee Buying Manager')->first();
            $coffeeBuyingManagers = $role->users;
            $role = Role::with(['users' => function ($query) use ($year) {
                $query->whereYear('created_at', $year);
            }])->where('name', 'Coffee Buyer')->first();
            $coffeeBuyers = $role->users;
            $coffeeBuyingManagers = $coffeeBuyingManagers->map(function ($coffeeBuyingManager) {
                $coffeeBuyingManager->image = $coffeeBuyingManager->getImage();
                $coffeeBuyingManager->first_purchase = $coffeeBuyingManager->firstPurchase();
                $coffeeBuyingManager->last_purchase = $coffeeBuyingManager->lastPurchase();
                $coffeeBuyingManager->specialcoffee = $coffeeBuyingManager->special();
                $coffeeBuyingManager = $coffeeBuyingManager->nonSpecialPrice();
                $coffeeBuyingManager = $coffeeBuyingManager->specialPrice();
                return   $coffeeBuyingManager;
            });
            $coffeeBuyers = $coffeeBuyers->map(function ($coffeeBuyer) {
                $coffeeBuyer->image = $coffeeBuyer->getImage();
                $coffeeBuyer->first_purchase = $coffeeBuyer->firstPurchase();
                $coffeeBuyer->last_purchase = $coffeeBuyer->lastPurchase();
                $coffeeBuyer->specialcoffee = $coffeeBuyer->special();
                $coffeeBuyer = $coffeeBuyer->nonSpecialPrice();
                $coffeeBuyer = $coffeeBuyer->specialPrice();
                return   $coffeeBuyer;
            });
            $governorates = Governerate::all();
            $regions = Region::all();
            $villages = Village::all();

            return view('admin.coffeBuyer.all_coffee_buyer', [
                'coffeeBuyerMangers' =>  $coffeeBuyingManagers,
                'coffeeBuyers' => $coffeeBuyers,
                'governorates' => $governorates,
                'regions' => $regions,
                'villages' => $villages,
            ]);
        } elseif ($date == 'weekToDate') {

            $now = Carbon::now();
            $start = $now->startOfWeek(Carbon::SUNDAY)->toDateString();

            $end = $now->endOfWeek(Carbon::SATURDAY)->toDateString();

            $coffeeBuyingManagers = Role::with(['users' => function ($query) use ($start, $end) {
                $query->whereBetween('created_at', [$start, $end]);
            }])->where('name', 'Coffee Buying Manager')->first()->users;

            $coffeeBuyers = Role::with(['users' => function ($query) use ($start, $end) {
                $query->whereBetween('created_at', [$start, $end]);
            }])->where('name', 'Coffee Buyer')->first()->users;

            $coffeeBuyingManagers = $coffeeBuyingManagers->map(function ($coffeeBuyingManager) {
                $coffeeBuyingManager->image = $coffeeBuyingManager->getImage();
                $coffeeBuyingManager->first_purchase = $coffeeBuyingManager->firstPurchase();
                $coffeeBuyingManager->last_purchase = $coffeeBuyingManager->lastPurchase();
                $coffeeBuyingManager->specialcoffee = $coffeeBuyingManager->special();
                $coffeeBuyingManager = $coffeeBuyingManager->nonSpecialPrice();
                $coffeeBuyingManager = $coffeeBuyingManager->specialPrice();
                return   $coffeeBuyingManager;
            });
            $coffeeBuyers = $coffeeBuyers->map(function ($coffeeBuyer) {
                $coffeeBuyer->image = $coffeeBuyer->getImage();
                $coffeeBuyer->first_purchase = $coffeeBuyer->firstPurchase();
                $coffeeBuyer->last_purchase = $coffeeBuyer->lastPurchase();
                $coffeeBuyer->specialcoffee = $coffeeBuyer->special();
                $coffeeBuyer = $coffeeBuyer->nonSpecialPrice();
                $coffeeBuyer = $coffeeBuyer->specialPrice();
                return   $coffeeBuyer;
            });
            $governorates = Governerate::all();
            $regions = Region::all();
            $villages = Village::all();

            return view('admin.coffeBuyer.all_coffee_buyer', [
                'coffeeBuyerMangers' =>  $coffeeBuyingManagers,
                'coffeeBuyers' => $coffeeBuyers,
                'governorates' => $governorates,
                'regions' => $regions,
                'villages' => $villages,
            ]);
        } elseif ($date == 'monthToDate') {

            $now = Carbon::now();
            $date = Carbon::now();
            $start = Carbon::now()->startOfMonth();

            $coffeeBuyingManagers = Role::with(['users' => function ($query) use ($start, $date) {
                $query->whereBetween('created_at', [$start, $date]);
            }])->where('name', 'Coffee Buying Manager')->first()->users;

            $coffeeBuyers = Role::with(['users' => function ($query) use ($start, $date) {
                $query->whereBetween('created_at', [$start, $date]);
            }])->where('name', 'Coffee Buyer')->first()->users;


            $coffeeBuyingManagers = $coffeeBuyingManagers->map(function ($coffeeBuyingManager) {
                $coffeeBuyingManager->image = $coffeeBuyingManager->getImage();
                $coffeeBuyingManager->first_purchase = $coffeeBuyingManager->firstPurchase();
                $coffeeBuyingManager->last_purchase = $coffeeBuyingManager->lastPurchase();
                $coffeeBuyingManager->specialcoffee = $coffeeBuyingManager->special();
                $coffeeBuyingManager = $coffeeBuyingManager->nonSpecialPrice();
                $coffeeBuyingManager = $coffeeBuyingManager->specialPrice();
                return   $coffeeBuyingManager;
            });
            $coffeeBuyers = $coffeeBuyers->map(function ($coffeeBuyer) {
                $coffeeBuyer->image = $coffeeBuyer->getImage();
                $coffeeBuyer->first_purchase = $coffeeBuyer->firstPurchase();
                $coffeeBuyer->last_purchase = $coffeeBuyer->lastPurchase();
                $coffeeBuyer->specialcoffee = $coffeeBuyer->special();
                $coffeeBuyer = $coffeeBuyer->nonSpecialPrice();
                $coffeeBuyer = $coffeeBuyer->specialPrice();
                return   $coffeeBuyer;
            });

            $governorates = Governerate::all();
            $regions = Region::all();
            $villages = Village::all();

            return view('admin.coffeBuyer.all_coffee_buyer', [
                'coffeeBuyerMangers' =>  $coffeeBuyingManagers,
                'coffeeBuyers' => $coffeeBuyers,
                'governorates' => $governorates,
                'regions' => $regions,
                'villages' => $villages,
            ]);
        } elseif ($date == 'yearToDate') {


            $date = Carbon::now();
            $start =  Carbon::now()->startOfYear();

            $governorates = Governerate::all();
            $regions = Region::all();
            $villages = Village::all();
            $coffeeBuyingManagers = Role::with(['users' => function ($query) use ($start, $date) {
                $query->whereBetween('created_at', [$start, $date]);
            }])->where('name', 'Coffee Buying Manager')->first()->users;

            $coffeeBuyers = Role::with(['users' => function ($query) use ($start, $date) {
                $query->whereBetween('created_at', [$start, $date]);
            }])->where('name', 'Coffee Buyer')->first()->users;



            $coffeeBuyingManagers = $coffeeBuyingManagers->map(function ($coffeeBuyingManager) {
                $coffeeBuyingManager->image = $coffeeBuyingManager->getImage();
                $coffeeBuyingManager->first_purchase = $coffeeBuyingManager->firstPurchase();
                $coffeeBuyingManager->last_purchase = $coffeeBuyingManager->lastPurchase();
                $coffeeBuyingManager->specialcoffee = $coffeeBuyingManager->special();
                $coffeeBuyingManager = $coffeeBuyingManager->nonSpecialPrice();
                $coffeeBuyingManager = $coffeeBuyingManager->specialPrice();
                return   $coffeeBuyingManager;
            });
            $coffeeBuyers = $coffeeBuyers->map(function ($coffeeBuyer) {
                $coffeeBuyer->image = $coffeeBuyer->getImage();
                $coffeeBuyer->first_purchase = $coffeeBuyer->firstPurchase();
                $coffeeBuyer->last_purchase = $coffeeBuyer->lastPurchase();
                $coffeeBuyer->specialcoffee = $coffeeBuyer->special();
                $coffeeBuyer = $coffeeBuyer->nonSpecialPrice();
                $coffeeBuyer = $coffeeBuyer->specialPrice();
                return   $coffeeBuyer;
            });
            return view('admin.coffeBuyer.all_coffee_buyer', [
                'coffeeBuyerMangers' =>  $coffeeBuyingManagers,
                'coffeeBuyers' => $coffeeBuyers,
                'governorates' => $governorates,
                'regions' => $regions,
                'villages' => $villages,
            ]);
        }
    }
    public function coffeeBuyerProfile(User $buyer)
    {

        $buyer->farmers = $buyer->getFarmers();
        $buyer->transactions = $buyer->getTransactions();
        $buyer->image = $buyer->getImage();
        $buyer->villages = $buyer->getVillages();
        $buyer->first_purchase = $buyer->firstPurchase();
        $buyer->last_purchase = $buyer->lastPurchase();
        $sum = 0;
        foreach ($buyer->transactions as $transaction) {
            $sum += $transaction->details->sum('container_weight');
        }
        $buyer->sum = $sum;
        $price = 0;
        foreach ($buyer->transactions as $transaction) {
            $farmerCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2] . '-' . explode('-', $transaction->batch_number)[3];

            $farmerPrice = Farmer::where('farmer_code', $farmerCode)->first();
            if ($farmerPrice) {
                $farmerPrice = $farmerPrice['price_per_kg'];
            }
            if (!$farmerPrice) {
                $villageCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2];
                $village = Village::where('village_code', $villageCode)->first();
                $vilagePrice = 0;
                if ($village) {
                    $vilagePrice = $village->price_per_kg;
                }

                foreach ($buyer->transactions as $transaction) {
                    $quantity = $transaction->details->sum('container_weight');
                    $price +=  $quantity * $vilagePrice;
                }
            } else {
                foreach ($buyer->transactions as $transaction) {
                    $quantity = $transaction->details->sum('container_weight');
                    $price +=  $quantity * $farmerPrice;
                }
            }
        }
        $buyer->price = $price;
        return   view('admin.coffeBuyer.coffeebuyer_profile', [
            'buyer' =>  $buyer,
        ])->render();
    }
    public function filterByDateprofile(Request $request, $id)
    {

        $buyer = User::find($id);

        $buyer->transactions = Transaction::with('details')->where('created_by', $buyer->user_id)->whereBetween('created_at', [$request->from, $request->to])->where('sent_to', 2)->get();
        $buyer->first_purchase = $buyer->firstPurchase();
        $buyer->last_purchase = $buyer->lastPurchase();
        $sum = 0;
        foreach ($buyer->transactions as $transaction) {
            $sum += $transaction->details->sum('container_weight');
        }
        $buyer->sum = $sum;
        $price = 0;
        foreach ($buyer->transactions as $transaction) {
            $farmerCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2] . '-' . explode('-', $transaction->batch_number)[3];

            $farmerPrice = Farmer::where('farmer_code', $farmerCode)->first();
            if ($farmerPrice) {
                $farmerPrice = $farmerPrice['price_per_kg'];
            }
            if (!$farmerPrice) {

                $villageCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2];
                $vilagePrice = Village::where('village_code', $villageCode)->first();

                if ($vilagePrice) {
                    $vilagePrice = $vilagePrice->price_per_kg;
                }
                foreach ($buyer->transactions as $transaction) {
                    $quantity = $transaction->details->sum('container_weight');
                    $price +=  $quantity * $vilagePrice;
                }
            } else {
                foreach ($buyer->transactions as $transaction) {
                    $quantity = $transaction->details->sum('container_weight');
                    $price +=  $quantity * $farmerPrice;
                }
            }
        }
        $buyer->price = $price;
        return   view('admin.coffeBuyer.views.filter_transctions', [
            'buyer' =>  $buyer,
        ])->render();
    }

    public function daysFilter(Request $request, $id)
    {
        if ($request->date == 'today') {
            $date = Carbon::today()->toDateString();

            $buyer = User::find($id);

            $buyer->transactions = Transaction::with('details')->where('created_by', $buyer->user_id)->where('created_at',  $date)->where('sent_to', 2)->get();
            $buyer->first_purchase = $buyer->firstPurchase();
            $buyer->last_purchase = $buyer->lastPurchase();
            $sum = 0;
            foreach ($buyer->transactions as $transaction) {
                $sum += $transaction->details->sum('container_weight');
            }
            $buyer->sum = $sum;
            $price = 0;
            foreach ($buyer->transactions as $transaction) {
                $farmerCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2] . '-' . explode('-', $transaction->batch_number)[3];

                $farmerPrice = Farmer::where('farmer_code', $farmerCode)->first();
                if ($farmerPrice) {
                    $farmerPrice = $farmerPrice['price_per_kg'];
                }
                if (!$farmerPrice) {

                    $villageCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2];
                    $vilagePrice = Village::where('village_code', $villageCode)->first();
                    if ($vilagePrice) {
                        $vilagePrice = $vilagePrice->price_per_kg;
                    }
                    foreach ($buyer->transactions as $transaction) {
                        $quantity = $transaction->details->sum('container_weight');
                        $price +=  $quantity * $vilagePrice;
                    }
                } else {
                    foreach ($buyer->transactions as $transaction) {
                        $quantity = $transaction->details->sum('container_weight');
                        $price +=  $quantity * $farmerPrice;
                    }
                }
            }
            $buyer->price = $price;
            return   view('admin.coffeBuyer.views.filter_transctions', [
                'buyer' =>  $buyer,
            ])->render();
        } elseif ($request->date == 'yesterday') {
            $now = Carbon::now();
            $yesterday = Carbon::yesterday();
            $buyer = User::find($id);

            $buyer->transactions = Transaction::with('details')->where('created_by', $buyer->user_id)->where('created_at',  $yesterday)->where('sent_to', 2)->get();
            $buyer->first_purchase = $buyer->firstPurchase();
            $buyer->last_purchase = $buyer->lastPurchase();
            $sum = 0;
            foreach ($buyer->transactions as $transaction) {
                $sum += $transaction->details->sum('container_weight');
            }
            $buyer->sum = $sum;
            $price = 0;
            foreach ($buyer->transactions as $transaction) {
                $farmerCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2] . '-' . explode('-', $transaction->batch_number)[3];

                $farmerPrice = Farmer::where('farmer_code', $farmerCode)->first();
                if ($farmerPrice) {
                    $farmerPrice = $farmerPrice['price_per_kg'];
                }
                if (!$farmerPrice) {

                    $villageCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2];
                    $vilagePrice = Village::where('village_code', $villageCode)->first();
                    if ($vilagePrice) {
                        $vilagePrice = $vilagePrice->price_per_kg;
                    }
                    foreach ($buyer->transactions as $transaction) {
                        $quantity = $transaction->details->sum('container_weight');
                        $price +=  $quantity * $vilagePrice;
                    }
                } else {
                    foreach ($buyer->transactions as $transaction) {
                        $quantity = $transaction->details->sum('container_weight');
                        $price +=  $quantity * $farmerPrice;
                    }
                }
            }
            $buyer->price = $price;
            return   view('admin.coffeBuyer.views.filter_transctions', [
                'buyer' =>  $buyer,
            ])->render();
        } elseif ($request->date == 'weekToDate') {
            $now = Carbon::now();
            $start = $now->startOfWeek(Carbon::SUNDAY);
            $end = $now->endOfWeek(Carbon::SATURDAY);

            $buyer = User::find($id);

            $buyer->transactions = Transaction::with('details')->where('created_by', $buyer->user_id)->where('created_at', [$start,   $end])->where('sent_to', 2)->get();
            $buyer->first_purchase = $buyer->firstPurchase();
            $buyer->last_purchase = $buyer->lastPurchase();
            $sum = 0;
            foreach ($buyer->transactions as $transaction) {
                $sum += $transaction->details->sum('container_weight');
            }
            $buyer->sum = $sum;
            $price = 0;
            foreach ($buyer->transactions as $transaction) {
                $farmerCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2] . '-' . explode('-', $transaction->batch_number)[3];

                $farmerPrice = Farmer::where('farmer_code', $farmerCode)->first();
                if ($farmerPrice) {
                    $farmerPrice = $farmerPrice['price_per_kg'];
                }
                if (!$farmerPrice) {

                    $villageCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2];
                    $vilagePrice = Village::where('village_code', $villageCode)->first();
                    if ($vilagePrice) {
                        $vilagePrice = $vilagePrice->price_per_kg;
                    }
                    foreach ($buyer->transactions as $transaction) {
                        $quantity = $transaction->details->sum('container_weight');
                        $price +=  $quantity * $vilagePrice;
                    }
                } else {
                    foreach ($buyer->transactions as $transaction) {
                        $quantity = $transaction->details->sum('container_weight');
                        $price +=  $quantity * $farmerPrice;
                    }
                }
            }
            $buyer->price = $price;
            return   view('admin.coffeBuyer.views.filter_transctions', [
                'buyer' =>  $buyer,
            ])->render();
        } elseif ($request->date == 'monthToDate') {
            $now = Carbon::now();
            $date = Carbon::today()->toDateString();
            $start = $now->firstOfMonth();
            $buyer = User::find($id);
            $buyer->transactions = Transaction::with('details')->where('created_by', $buyer->user_id)->whereBetween('created_at', [$start, $date])->where('sent_to', 2)->get();
            $buyer->first_purchase = $buyer->firstPurchase();
            $buyer->last_purchase = $buyer->lastPurchase();
            $sum = 0;
            foreach ($buyer->transactions as $transaction) {
                $sum += $transaction->details->sum('container_weight');
            }
            $buyer->sum = $sum;
            $price = 0;
            foreach ($buyer->transactions as $transaction) {
                $farmerCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2] . '-' . explode('-', $transaction->batch_number)[3];

                $farmerPrice = Farmer::where('farmer_code', $farmerCode)->first();
                if ($farmerPrice) {
                    $farmerPrice = $farmerPrice['price_per_kg'];
                }
                if (!$farmerPrice) {

                    $villageCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2];
                    $vilagePrice = Village::where('village_code', $villageCode)->first();
                    if ($vilagePrice) {
                        $vilagePrice = $vilagePrice->price_per_kg;
                    }
                    foreach ($buyer->transactions as $transaction) {
                        $quantity = $transaction->details->sum('container_weight');
                        $price +=  $quantity * $vilagePrice;
                    }
                } else {
                    foreach ($buyer->transactions as $transaction) {
                        $quantity = $transaction->details->sum('container_weight');
                        $price +=  $quantity * $farmerPrice;
                    }
                }
            }
            $buyer->price = $price;
            return   view('admin.coffeBuyer.views.filter_transctions', [
                'buyer' =>  $buyer,
            ])->render();
        } elseif ($request->date == 'lastmonth') {
            $date = Carbon::now();

            $lastMonth =  $date->subMonth()->format('m');
            $year = $date->year;
            $buyer = User::find($id);

            $buyer->transactions = Transaction::with('details')->where('created_by', $buyer->user_id)->whereMonth('created_at', $lastMonth)->whereYear('created_at', $year)->where('sent_to', 2)->get();
            $buyer->first_purchase = $buyer->firstPurchase();
            $buyer->last_purchase = $buyer->lastPurchase();
            $sum = 0;
            foreach ($buyer->transactions as $transaction) {
                $sum += $transaction->details->sum('container_weight');
            }
            $buyer->sum = $sum;
            $price = 0;
            foreach ($buyer->transactions as $transaction) {
                $farmerCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2] . '-' . explode('-', $transaction->batch_number)[3];

                $farmerPrice = Farmer::where('farmer_code', $farmerCode)->first();
                if ($farmerPrice) {
                    $farmerPrice = $farmerPrice['price_per_kg'];
                }
                if (!$farmerPrice) {

                    $villageCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2];
                    $vilagePrice = Village::where('village_code', $villageCode)->first();
                    if ($vilagePrice) {
                        $vilagePrice = $vilagePrice->price_per_kg;
                    }
                    foreach ($buyer->transactions as $transaction) {
                        $quantity = $transaction->details->sum('container_weight');
                        $price +=  $quantity * $vilagePrice;
                    }
                } else {
                    foreach ($buyer->transactions as $transaction) {
                        $quantity = $transaction->details->sum('container_weight');
                        $price +=  $quantity * $farmerPrice;
                    }
                }
            }
            $buyer->price = $price;
            return   view('admin.coffeBuyer.views.filter_transctions', [
                'buyer' =>  $buyer,
            ])->render();
        } elseif ($request->date == 'yearToDate') {
            $now = Carbon::now();
            $date = Carbon::today()->toDateString();
            $start = $now->startOfYear();
            $buyer = User::find($id);

            $buyer->transactions = Transaction::with('details')->where('created_by', $buyer->user_id)->whereBetween('created_at', [$start, $date])->where('sent_to', 2)->get();
            $buyer->first_purchase = $buyer->firstPurchase();
            $buyer->last_purchase = $buyer->lastPurchase();
            $sum = 0;
            foreach ($buyer->transactions as $transaction) {
                $sum += $transaction->details->sum('container_weight');
            }
            $buyer->sum = $sum;
            $price = 0;
            foreach ($buyer->transactions as $transaction) {
                $farmerCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2] . '-' . explode('-', $transaction->batch_number)[3];

                $farmerPrice = Farmer::where('farmer_code', $farmerCode)->first();
                if ($farmerPrice) {
                    $farmerPrice = $farmerPrice['price_per_kg'];
                }
                if (!$farmerPrice) {

                    $villageCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2];
                    $vilagePrice = Village::where('village_code', $villageCode)->first();
                    if ($vilagePrice) {
                        $vilagePrice = $vilagePrice->price_per_kg;
                    }
                    foreach ($buyer->transactions as $transaction) {
                        $quantity = $transaction->details->sum('container_weight');
                        $price +=  $quantity * $vilagePrice;
                    }
                } else {
                    foreach ($buyer->transactions as $transaction) {
                        $quantity = $transaction->details->sum('container_weight');
                        $price +=  $quantity * $farmerPrice;
                    }
                }
            }
            $buyer->price = $price;
            return   view('admin.coffeBuyer.views.filter_transctions', [
                'buyer' =>  $buyer,
            ])->render();
        } elseif ($request->date == 'currentyear') {
            $date = Carbon::now();

            $year = $date->year;
            $buyer = User::find($id);

            $buyer->transactions = Transaction::with('details')->where('created_by', $buyer->user_id)->whereYear('created_at',  $year)->where('sent_to', 2)->get();
            $buyer->first_purchase = $buyer->firstPurchase();
            $buyer->last_purchase = $buyer->lastPurchase();
            $sum = 0;
            foreach ($buyer->transactions as $transaction) {
                $sum += $transaction->details->sum('container_weight');
            }
            $buyer->sum = $sum;
            $price = 0;
            foreach ($buyer->transactions as $transaction) {
                $farmerCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2] . '-' . explode('-', $transaction->batch_number)[3];

                $farmerPrice = Farmer::where('farmer_code', $farmerCode)->first();
                if ($farmerPrice) {
                    $farmerPrice = $farmerPrice['price_per_kg'];
                }
                if (!$farmerPrice) {

                    $villageCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2];
                    $vilagePrice = Village::where('village_code', $villageCode)->first();
                    if ($vilagePrice) {
                        $vilagePrice = $vilagePrice->price_per_kg;
                    }
                    foreach ($buyer->transactions as $transaction) {
                        $quantity = $transaction->details->sum('container_weight');
                        $price +=  $quantity * $vilagePrice;
                    }
                } else {
                    foreach ($buyer->transactions as $transaction) {
                        $quantity = $transaction->details->sum('container_weight');
                        $price +=  $quantity * $farmerPrice;
                    }
                }
            }
            $buyer->price = $price;
            return   view('admin.coffeBuyer.views.filter_transctions', [
                'buyer' =>  $buyer,
            ])->render();
        } elseif ($request->date == 'lastyear') {
            $date = Carbon::now();


            $year = $date->year - 1;
            $buyer = User::find($id);

            $buyer->transactions = Transaction::with('details')->where('created_by', $buyer->user_id)->whereYear('created_at',  $year)->where('sent_to', 2)->get();
            $buyer->first_purchase = $buyer->firstPurchase();
            $buyer->last_purchase = $buyer->lastPurchase();
            $sum = 0;
            foreach ($buyer->transactions as $transaction) {
                $sum += $transaction->details->sum('container_weight');
            }
            $buyer->sum = $sum;
            $price = 0;
            foreach ($buyer->transactions as $transaction) {
                $farmerCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2] . '-' . explode('-', $transaction->batch_number)[3];

                $farmerPrice = Farmer::where('farmer_code', $farmerCode)->first();
                if ($farmerPrice) {
                    $farmerPrice = $farmerPrice['price_per_kg'];
                }
                if (!$farmerPrice) {

                    $villageCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2];
                    $vilagePrice = Village::where('village_code', $villageCode)->first();
                    if ($vilagePrice) {
                        $vilagePrice = $vilagePrice->price_per_kg;
                    }
                    foreach ($buyer->transactions as $transaction) {
                        $quantity = $transaction->details->sum('container_weight');
                        $price +=  $quantity * $vilagePrice;
                    }
                } else {
                    foreach ($buyer->transactions as $transaction) {
                        $quantity = $transaction->details->sum('container_weight');
                        $price +=  $quantity * $farmerPrice;
                    }
                }
            }
            $buyer->price = $price;
            return   view('admin.coffeBuyer.views.filter_transctions', [
                'buyer' =>  $buyer,
            ])->render();
        }
    }
    public function filterBygovernrate(Request $request)
    {
        $governorates = Governerate::all();

        $villages = Village::all();
        $id = $request->from;
        $governorateCode = Governerate::where('governerate_id', $id)->first()->governerate_code;
        $regions = Region::where('region_code', 'LIKE', $governorateCode . '%')->get();

        $coffeeBuyingManagers = Role::with(['users'])->where('name', 'Coffee Buying Manager')->first()->users;
        $coffeeBuyers = Role::with('users')->where('name', 'Coffee Buyer')->first()->users;
        $coffeeBuyingManagers = $coffeeBuyingManagers->map(function ($coffeeBuyingManager)  use ($governorateCode) {
            $coffeeBuyingManager->image = $coffeeBuyingManager->getImage();
            $coffeeBuyingManager->first_purchase = $coffeeBuyingManager->firstPurchase();
            $coffeeBuyingManager->last_purchase = $coffeeBuyingManager->lastPurchase();
            $transactions = Transaction::with('details')->where(['created_by' =>   $coffeeBuyingManager->user_id, 'is_special' => 0])->where('batch_number', 'LIKE', $governorateCode . '%')->where('sent_to', 2)->get();
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

            $coffeeBuyingManager->non_special_price = $totalPrice;
            $coffeeBuyingManager->non_special_weight = $totalWeight;
            $transactions = Transaction::with('details')->where(['created_by' =>   $coffeeBuyingManager->user_id, 'is_special' => 1])->where('batch_number', 'LIKE', $governorateCode . '%')->where('sent_to', 2)->get();
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

            $coffeeBuyingManager->special_price = $totalPrice;
            $coffeeBuyingManager->special_weight = $totalWeight;
            return   $coffeeBuyingManager;
        });

        $coffeeBuyers = $coffeeBuyers->map(function ($coffeeBuyer) use ($governorateCode) {
            $coffeeBuyer->image = $coffeeBuyer->getImage();
            $coffeeBuyer->first_purchase = $coffeeBuyer->firstPurchase();
            $coffeeBuyer->last_purchase = $coffeeBuyer->lastPurchase();
            $transactions = Transaction::with('details')->where(['created_by' =>   $coffeeBuyer->user_id, 'is_special' => 0])->where('batch_number', 'LIKE', $governorateCode . '%')->get();
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

            $coffeeBuyer->non_special_price = $totalPrice;
            $coffeeBuyer->non_special_weight = $totalWeight;

            $transactions = Transaction::with('details')->where(['created_by' =>   $coffeeBuyer->user_id, 'is_special' => 1])->where('batch_number', 'LIKE', $governorateCode . '%')->get();
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

            $coffeeBuyer->special_price = $totalPrice;
            $coffeeBuyer->special_weight = $totalWeight;
            return   $coffeeBuyer;
        });



        return response()->json([
            'regions' => $regions,
            'view' => view('admin.coffeBuyer.views.index', [
                'coffeeBuyerMangers' => $coffeeBuyingManagers,
                'coffeeBuyers' => $coffeeBuyers
            ])->render()
        ]);
    }
    public function filterByregions(Request $request)
    {

        $id = $request->from;
        $regionCode = Region::where('region_id', $id)->first()->region_code;
        $villages = Village::where('village_code', 'LIKE', $regionCode . '%')->get();

        $coffeeBuyingManagers = Role::with('users')->where('name', 'Coffee Buying Manager')->first()->users;
        $coffeeBuyers = Role::with('users')->where('name', 'Coffee Buyer')->first()->users;
        $coffeeBuyingManagers = $coffeeBuyingManagers->map(function ($coffeeBuyingManager) use ($regionCode) {
            $coffeeBuyingManager->image = $coffeeBuyingManager->getImage();
            $coffeeBuyingManager->first_purchase = $coffeeBuyingManager->firstPurchase();
            $coffeeBuyingManager->last_purchase = $coffeeBuyingManager->lastPurchase();
            $totalWeight = 0;
            $totalPrice = 0;
            $transactions = Transaction::with('details')->where(['created_by' =>   $coffeeBuyingManager->user_id, 'is_special' => 0])->where('batch_number', 'LIKE', $regionCode . '%')->where('sent_to', 2)->get();
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

            $coffeeBuyingManager->non_special_price = $totalPrice;
            $coffeeBuyingManager->non_special_weight = $totalWeight;

            $transactions = Transaction::with('details')->where(['created_by' =>   $coffeeBuyingManager->user_id, 'is_special' => 1])->where('batch_number', 'LIKE', $regionCode . '%')->where('sent_to', 2)->get();
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

            $coffeeBuyingManager->special_price = $totalPrice;
            $coffeeBuyingManager->special_weight = $totalWeight;
            return   $coffeeBuyingManager;
        });

        $coffeeBuyers = $coffeeBuyers->map(function ($coffeeBuyer) use ($regionCode) {
            $coffeeBuyer->image = $coffeeBuyer->getImage();
            $coffeeBuyer->first_purchase = $coffeeBuyer->firstPurchase();
            $coffeeBuyer->last_purchase = $coffeeBuyer->lastPurchase();
            $totalWeight = 0;
            $totalPrice = 0;
            $transactions = Transaction::with('details')->where(['created_by' =>   $coffeeBuyer->user_id, 'is_special' => 0])->where('batch_number', 'LIKE', $regionCode . '%')->where('sent_to', 2)->get();

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

            $coffeeBuyer->non_special_price = $totalPrice;
            $coffeeBuyer->non_special_weight = $totalWeight;

            $transactions = Transaction::with('details')->where(['created_by' =>   $coffeeBuyer->user_id, 'is_special' => 1])->where('batch_number', 'LIKE', $regionCode . '%')->where('sent_to', 2)->get();
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

            $coffeeBuyer->special_price = $totalPrice;
            $coffeeBuyer->special_weight = $totalWeight;
            return   $coffeeBuyer;
        });



        return response()->json([
            'villages' => $villages,
            'view' => view('admin.coffeBuyer.views.index', [
                'coffeeBuyerMangers' => $coffeeBuyingManagers,
                'coffeeBuyers' => $coffeeBuyers
            ])->render()
        ]);
    }
    public function filterByvillage(Request $request)
    {

        $id = $request->from;
        $villageCode = Village::where('village_id', $id)->first()->village_code;


        $coffeeBuyingManagers = Role::with(['users'])->where('name', 'Coffee Buying Manager')->first()->users;
        $coffeeBuyers = Role::with('users')->where('name', 'Coffee Buyer')->first()->users;
        $coffeeBuyingManagers = $coffeeBuyingManagers->map(function ($coffeeBuyingManager)  use ($villageCode) {
            $coffeeBuyingManager->image = $coffeeBuyingManager->getImage();
            $coffeeBuyingManager->first_purchase = $coffeeBuyingManager->firstPurchase();
            $coffeeBuyingManager->last_purchase = $coffeeBuyingManager->lastPurchase();
            $transactions = Transaction::with('details')->where('created_by',   $coffeeBuyingManager->user_id)->where('is_special', 0)->where('batch_number', 'LIKE', $villageCode . '%')->where('sent_to', 2)->get();
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

            $coffeeBuyingManager->non_special_price = $totalPrice;
            $coffeeBuyingManager->non_special_weight = $totalWeight;
            $transactions = Transaction::with('details')->where('created_by',   $coffeeBuyingManager->user_id)->where('is_special', 0)->where('batch_number', 'LIKE', $villageCode . '%')->where('sent_to', 2)->get();
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

            $coffeeBuyingManager->special_price = $totalPrice;
            $coffeeBuyingManager->special_weight = $totalWeight;
            return   $coffeeBuyingManager;
        });

        $coffeeBuyers = $coffeeBuyers->map(function ($coffeeBuyer) use ($villageCode) {
            $coffeeBuyer->image = $coffeeBuyer->getImage();
            $coffeeBuyer->first_purchase = $coffeeBuyer->firstPurchase();
            $coffeeBuyer->last_purchase = $coffeeBuyer->lastPurchase();
            $transactions = Transaction::with('details')->where('created_by',  $coffeeBuyer->user_id)->where('is_special', 0)->where('batch_number', 'LIKE', $villageCode . '%')->where('sent_to', 2)->get();
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

            $coffeeBuyer->non_special_price = $totalPrice;
            $coffeeBuyer->non_special_weight = $totalWeight;

            $transactions = Transaction::with('details')->where('created_by', $coffeeBuyer->user_id)->where('is_special', 1)->where('batch_number', 'LIKE', $villageCode . '%')->where('sent_to', 2)->get();
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

            $coffeeBuyer->special_price = $totalPrice;
            $coffeeBuyer->special_weight = $totalWeight;
            return   $coffeeBuyer;
        });





        return response()->json([
            'view' => view('admin.coffeBuyer.views.index', [
                'coffeeBuyerMangers' => $coffeeBuyingManagers,
                'coffeeBuyers' => $coffeeBuyers
            ])->render()
        ]);
    }
}
