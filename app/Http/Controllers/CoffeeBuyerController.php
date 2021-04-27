<?php

namespace App\Http\Controllers;

use App\User;
use App\Farmer;
use App\Region;
use App\Village;
use App\Governerate;
use App\Transaction;
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
        $governorates = Governerate::all();
        $regions = Region::all();
        $villages = Village::all();
        $coffeeBuyingManagers = Role::with('users')->where('name', 'Coffee Buying Manager')->first()->users;

        $coffeeBuyers = Role::with('users')->where('name', 'Coffee Buyer')->first()->users;

        $coffeeBuyingManagers =  $coffeeBuyingManagers->whereBetween('created_at', [$request->from, $request->to]);
        $coffeeBuyers =  $coffeeBuyers->whereBetween('created_at', [$request->from, $request->to]);
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
            $coffeeBuyingManagers = Role::with('users')->where('name', 'Coffee Buying Manager')->first()->users;

            $coffeeBuyers = Role::with('users')->where('name', 'Coffee Buyer')->first()->users;

            $coffeeBuyingManagers =  $coffeeBuyingManagers->where('created_at', $date);
            $coffeeBuyers =  $coffeeBuyers->where('created_at', $date);
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
            $yesterday = Carbon::yesterday();

            $coffeeBuyingManagers = Role::with('users')->where('name', 'Coffee Buying Manager')->first()->users;

            $coffeeBuyers = Role::with('users')->where('name', 'Coffee Buyer')->first()->users;

            $coffeeBuyingManagers =  $coffeeBuyingManagers->where('created_at', $yesterday);
            $coffeeBuyers =  $coffeeBuyers->where('created_at', $yesterday);
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
            $coffeeBuyingManagers = Role::with('users')->where('name', 'Coffee Buying Manager')->first()->users;

            $role = Role::with(['users' => function ($query) use ($lastMonth, $year) {
                $query->whereMonth('created_at', $lastMonth)->whereYear('created_at', $year);
            }])->where('name', 'Coffee Buying Manager')->first();

            $coffeeBuyingManagers = $role->users;

            $role = Role::with(['users' => function ($query) use ($lastMonth, $year) {
                $query->whereMonth('created_at', $lastMonth)->whereYear('created_at', $year);
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
            $start = $now->startOfWeek(Carbon::SUNDAY);

            $end = $now->endOfWeek(Carbon::SATURDAY);
            $coffeeBuyingManagers = Role::with('users')->where('name', 'Coffee Buying Manager')->first()->users;

            $coffeeBuyers = Role::with('users')->where('name', 'Coffee Buyer')->first()->users;

            $coffeeBuyingManagers =  $coffeeBuyingManagers->whereBetween('created_at', [$start, $end]);
            $coffeeBuyers =  $coffeeBuyers->whereBetween('created_at', [$start, $end]);
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
            $date = Carbon::today()->toDateString();
            $start = $now->firstOfMonth();
            $coffeeBuyingManagers = Role::with('users')->where('name', 'Coffee Buying Manager')->first()->users;

            $coffeeBuyers = Role::with('users')->where('name', 'Coffee Buyer')->first()->users;

            $coffeeBuyingManagers =  $coffeeBuyingManagers->whereBetween('created_at', [$start, $date]);
            $coffeeBuyers =  $coffeeBuyers->whereBetween('created_at', [$start, $date]);

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

            $now = Carbon::now();
            $date = Carbon::today()->toDateString();
            $start = $now->startOfYear();

            $governorates = Governerate::all();
            $regions = Region::all();
            $villages = Village::all();
            $coffeeBuyingManagers = Role::with('users')->where('name', 'Coffee Buying Manager')->first()->users;

            $coffeeBuyers = Role::with('users')->where('name', 'Coffee Buyer')->first()->users;

            $coffeeBuyingManagers =  $coffeeBuyingManagers->whereBetween('created_at', [$start, $date]);

            $coffeeBuyers =  $coffeeBuyers->whereBetween('created_at', [$start, $date]);


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

            $farmerPrice = Farmer::where('farmer_code', $farmerCode)->first()['price_per_kg'];
            if (!$farmerPrice) {

                $villageCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2];
                $vilagePrice = Village::where('village_code', $villageCode)->first()->price_per_kg;
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
        ]);
    }
    public function filterByDateprofile(Request $request, $id)
    {

        $buyer = User::find($id);

        $buyer->transactions = Transaction::with('details')->where('created_by', $buyer->user_id)->whereBetween('created_at', [$request->from, $request->to])->get();
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

            $farmerPrice = Farmer::where('farmer_code', $farmerCode)->first()['price_per_kg'];
            if (!$farmerPrice) {

                $villageCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2];
                $vilagePrice = Village::where('village_code', $villageCode)->first()->price_per_kg;
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

            $buyer->transactions = Transaction::with('details')->where('created_by', $buyer->user_id)->where('created_at',  $date)->get();
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

                $farmerPrice = Farmer::where('farmer_code', $farmerCode)->first()['price_per_kg'];
                if (!$farmerPrice) {

                    $villageCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2];
                    $vilagePrice = Village::where('village_code', $villageCode)->first()->price_per_kg;
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

            $buyer->transactions = Transaction::with('details')->where('created_by', $buyer->user_id)->where('created_at',  $yesterday)->get();
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

                $farmerPrice = Farmer::where('farmer_code', $farmerCode)->first()['price_per_kg'];
                if (!$farmerPrice) {

                    $villageCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2];
                    $vilagePrice = Village::where('village_code', $villageCode)->first()->price_per_kg;
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

            $buyer->transactions = Transaction::with('details')->where('created_by', $buyer->user_id)->where('created_at', [$start,   $end])->get();
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

                $farmerPrice = Farmer::where('farmer_code', $farmerCode)->first()['price_per_kg'];
                if (!$farmerPrice) {

                    $villageCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2];
                    $vilagePrice = Village::where('village_code', $villageCode)->first()->price_per_kg;
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
            $buyer->transactions = Transaction::with('details')->where('created_by', $buyer->user_id)->whereBetween('created_at', [$start, $date])->get();
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

                $farmerPrice = Farmer::where('farmer_code', $farmerCode)->first()['price_per_kg'];
                if (!$farmerPrice) {

                    $villageCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2];
                    $vilagePrice = Village::where('village_code', $villageCode)->first()->price_per_kg;
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

            $buyer->transactions = Transaction::with('details')->where('created_by', $buyer->user_id)->whereMonth('created_at', $lastMonth)->whereYear('created_at', $year)->get();
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

                $farmerPrice = Farmer::where('farmer_code', $farmerCode)->first()['price_per_kg'];
                if (!$farmerPrice) {

                    $villageCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2];
                    $vilagePrice = Village::where('village_code', $villageCode)->first()->price_per_kg;
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

            $buyer->transactions = Transaction::with('details')->where('created_by', $buyer->user_id)->whereBetween('created_at', [$start, $date])->get();
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

                $farmerPrice = Farmer::where('farmer_code', $farmerCode)->first()['price_per_kg'];
                if (!$farmerPrice) {

                    $villageCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2];
                    $vilagePrice = Village::where('village_code', $villageCode)->first()->price_per_kg;
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

            $buyer->transactions = Transaction::with('details')->where('created_by', $buyer->user_id)->whereYear('created_at',  $year)->get();
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

                $farmerPrice = Farmer::where('farmer_code', $farmerCode)->first()['price_per_kg'];
                if (!$farmerPrice) {

                    $villageCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2];
                    $vilagePrice = Village::where('village_code', $villageCode)->first()->price_per_kg;
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

            $buyer->transactions = Transaction::with('details')->where('created_by', $buyer->user_id)->whereYear('created_at',  $year)->get();
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

                $farmerPrice = Farmer::where('farmer_code', $farmerCode)->first()['price_per_kg'];
                if (!$farmerPrice) {

                    $villageCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2];
                    $vilagePrice = Village::where('village_code', $villageCode)->first()->price_per_kg;
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
}
