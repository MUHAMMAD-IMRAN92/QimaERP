<?php

use Illuminate\Support\Facades\Route;

/*
  |--------------------------------------------------------------------------
  | Web Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register web routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | contains the "web" middleware group. Now create something great!
  |
 */
Route::get('login', 'AuthController@adminLogin')->name('login');
Route::get('/', function () {
    return Redirect::route('login');
   // return view('welcome');
});


Route::group(['prefix' => 'admin'], function () {
    Route::get('dashboard', 'AuthController@dashboard');
    Route::get('logout', 'AuthController@adminLogout');
    Route::get('login', 'AuthController@adminLogin');
    Route::post('login', 'AuthController@adminPostLogin');

    //Governor Controller
    Route::get('allgovernor', 'GovernorController@allgovernor');
    Route::get('getgovernors', 'GovernorController@getGovernorAjax'); 
    Route::get('addnewgovernor', 'GovernorController@addnewgovernor');
    Route::get('editgovernor/{id}', 'GovernorController@edit');
    Route::post('updategovernor', 'GovernorController@update'); 
    Route::get('deletegovernor/{id}', 'GovernorController@delete'); 
    Route::post('addgovernor', 'GovernorController@store'); 


    //Region Controller
    Route::get('allregion', 'RegionController@index'); 
    Route::get('getregion', 'RegionController@getRegionAjax');
    Route::get('addnewregion', 'RegionController@addnewregion');
    Route::post('addregion', 'RegionController@store');
    Route::get('editregion/{id}', 'RegionController@edit');
     Route::get('deleteregion/{id}', 'RegionController@delete');

    //Village Controller
    Route::get('allvillage', 'VillageController@index');
    Route::get('getvillage', 'VillageController@getVillageAjax');

    //Farmer Controller
    Route::get('allfarmer', 'FarmerController@index');
    Route::get('getfarmer', 'FarmerController@getFarmerAjax');

    //BatchNumber Controller
    Route::get('allbatchnumber', 'BatchNumberController@index');
    Route::get('getbatch', 'BatchNumberController@getbatchAjax');

    //Center Controller
    Route::get('allcenter', 'CenterController@index');
    Route::get('addcenter', 'CenterController@addnewcenter');
    Route::post('storecenter', 'CenterController@storecenter');
    Route::get('getcenter', 'CenterController@getCenterAjax');

    //Transection Controller
    Route::get('alltransection', 'TransectionController@index');
    Route::get('transactiondetail/{id}', 'TransectionController@detail');
    Route::get('gettransection', 'TransectionController@getTransectionAjax');

    //Container Controller
    Route::get('allcontainer', 'ContainerController@index');
    Route::get('addcontainer', 'ContainerController@addcontainer');
    



    Route::group(['middleware' => ['nocache', 'admin']], function () {



    });
});
