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
    Route::get('addnewgovernor', 'GovernorController@addnewgovernor');
    Route::get('editgovernor/{id}', 'GovernorController@edit');
    Route::post('updategovernor', 'GovernorController@update'); 
    Route::get('deletegovernor/{id}', 'GovernorController@delete'); 
    Route::post('addgovernor', 'GovernorController@store'); 


    //Region Controller
    Route::get('allregion', 'RegionController@index'); 
    Route::get('addnewregion', 'RegionController@addnewregion');
    Route::post('addregion', 'RegionController@store');
    Route::get('editregion/{id}', 'RegionController@edit');
     Route::get('deleteregion/{id}', 'RegionController@delete');
    Route::group(['middleware' => ['nocache', 'admin']], function () {


    });
});
