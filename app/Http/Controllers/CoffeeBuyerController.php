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
        $image = collect();
        $coffeeBuyingManagers = Role::with(['users'])->where('name', 'Coffee Buying Manager')->first()->users;
        $coffeeBuyers = Role::with('users')->where('name', 'Coffee Buyer')->first()->users;
        $coffeeBuyingManagers = $coffeeBuyingManagers->map(function ($coffeeBuyingManager) {
            $coffeeBuyingManager->image = $coffeeBuyingManager->getImage();
            return   $coffeeBuyingManager;
        });
        $coffeeBuyers = $coffeeBuyers->map(function ($coffeeBuyer) {
            $coffeeBuyer->image = $coffeeBuyer->getImage();
            return   $coffeeBuyer;
        });
        // $transactions = Transaction::where('created_by',  $coffeeBuyer->user_id)->get();

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


        $buyer->image = $buyer->getImage();
  
        return   view('admin.coffeBuyer.coffeebuyer_profile', [
            'buyer' =>  $buyer,
        ]);
    }
}
