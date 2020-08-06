<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//use App\User;
//
//use Spatie\Permission\Models\Role;
//use Spatie\Permission\Models\Permission;

/*
  |--------------------------------------------------------------------------
  | API Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register API routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | is assigned the "api" middleware group. Enjoy building your API!
  |
 */
//$user = User::where('user_id', 1)->first();
//$user->assignRole('Coffee Buying Manager');
//$role = Role::create(['name' => 'Coffee Buying Manager']);

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['prefix' => 'v1', 'middleware' => ['headersmid', 'checkAppKey']], function () {
    Route::post('/login', 'API\AuthController@login');
    Route::post('/login', 'API\AuthController@login');
// Logged In users
    Route::group(['middleware' => ['checkSession']], function () {
        //::governerate
        Route::post('/add_governerate', 'API\CommonController@addGovernerate');
        Route::get('/governerate', 'API\CommonController@governerate');
        //::region
        Route::post('/add_region', 'API\CommonController@addRegion');
        Route::get('/regions', 'API\CommonController@regions');
        //::village
        Route::post('/add_village', 'API\CommonController@addVillage');
        Route::get('/villages', 'API\CommonController@villages');

        //::farmer
        Route::post('/add_farmer', 'API\CommonController@addFarmer');
        Route::get('/farmers', 'API\CommonController@farmers');
        //::coffee buyer manager
        Route::get('/coffee_buyer_manager_farmer', 'API\CoffeeBuyerManager@farmer');
        //::coffee buyer
        Route::get('/coffee_buyer_farmer', 'API\CoffeeBuyer@farmer');
        //::Add container
         Route::post('/add_container', 'API\CommonController@addContainer');
         Route::get('/containers', 'API\CommonController@containers');
    });
});
