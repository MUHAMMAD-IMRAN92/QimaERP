<?php

namespace App\Http\Controllers;

use App\Farmer;
use App\Governerate;
use App\Region;
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
        $coffeeBuyer = Role::with('users')->where('name', 'Coffee Buyer')->first()->users;


        return view('admin.coffeBuyer.all_coffee_buyer', [
            'coffeeBuyerMangers' =>  $coffeeBuyingManagers,
            'coffeeBuyers' => $coffeeBuyer,
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
}
