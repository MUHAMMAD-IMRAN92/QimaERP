<?php

namespace App\Http\Controllers;

use App\Farmer;
use App\Governerate;
use App\Region;
use App\Transaction;
use Illuminate\Http\Request;
use App\User;
use App\Village;
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
        $farmers = Farmer::all();
        $coffeeBuyingManagers = Role::with('users')->where('name', 'Coffee Buying Manager')->first()->users;
        $coffeeBuyers = Role::with('users')->where('name', 'Coffee Buyer')->first()->users;
        $transactions = collect();
        $transaction_id = collect();
        // foreach ($coffeeBuyers as $coffeeBuyer) {

        //     $transaction = Transaction::where('created_by',  $coffeeBuyer->user_id)->get();
        //     $transactions->push($transaction);
        // }

        // return $transactions;

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
    // public function farmerByVillages(Request $request)
    // {
    //     $id = $request->from;
    //     $villageCode = Village::where('village_id', $id)->first()->village_code;

    //     $farmers = Farmer::where('village_code', $villageCode)->get();


    //     $farmers =  $farmers->map(function ($farmer) {
    //         $farmer->governerate_title = $farmer->getgovernerate()->governerate_title;
    //         return $farmer;
    //     });
    //     $farmers = $farmers->map(function ($farmer) {
    //         $farmer->region_title = $farmer->getRegion()->region_title;
    //         return $farmer;
    //     });
    //     $farmers = $farmers->map(function ($farmer) {
    //         $farmer->village_title = $farmer->getVillage()->village_title;
    //         return $farmer;
    //     });
    //     $farmers = $farmers->map(function ($farmer) {
    //         $farmer->image = $farmer->getImage();
    //         return $farmer;
    //     });
    //     return view('admin.farmer.views.index', compact('farmers'))->render();
    // }
}
