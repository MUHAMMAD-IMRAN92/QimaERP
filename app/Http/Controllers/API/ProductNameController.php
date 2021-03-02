<?php

namespace App\Http\Controllers\API;

use App\ProductName;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductNameController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $productNames = ProductName::all(['id', 'name']);

        return response()->json($productNames);
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
     * @param  \App\ProductName  $productName
     * @return \Illuminate\Http\Response
     */
    public function show(ProductName $productName)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ProductName  $productName
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductName $productName)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ProductName  $productName
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductName $productName)
    {
        //
    }
}
