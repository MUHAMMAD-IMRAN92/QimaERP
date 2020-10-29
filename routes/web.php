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
    Route::get('login', 'AuthController@adminLogin');
    Route::post('login', 'AuthController@adminPostLogin');
    Route::group(['middleware' => ['CheckRole']], function () {
        Route::get('dashboard', 'AuthController@dashboard')->middleware('auth', 'CheckRole');
        Route::get('logout', 'AuthController@adminLogout');


        //Governor Controller
        Route::get('allgovernor', 'GovernorController@allgovernor')->middleware('auth');
        Route::get('getgovernors', 'GovernorController@getGovernorAjax');
        Route::get('addnewgovernor', 'GovernorController@addnewgovernor')->middleware('auth');
        Route::get('editgovernor/{id}', 'GovernorController@edit')->middleware('auth');
        Route::post('updategovernor', 'GovernorController@update');
        Route::get('deletegovernor/{id}', 'GovernorController@delete')->middleware('auth');
        Route::post('addgovernor', 'GovernorController@store')->middleware('auth');


        //Region Controller
        Route::get('allregion', 'RegionController@index')->middleware('auth');
        Route::get('getregion', 'RegionController@getRegionAjax');
        Route::get('addnewregion', 'RegionController@addnewregion')->middleware('auth');
        Route::post('addregion', 'RegionController@store')->middleware('auth');
        Route::get('editregion/{id}', 'RegionController@edit')->middleware('auth');
        Route::get('deleteregion/{id}', 'RegionController@delete')->middleware('auth');


        //Village Controller
        Route::get('allvillage', 'VillageController@index')->middleware('auth');
        Route::get('getvillage', 'VillageController@getVillageAjax');
        Route::get('addnewvillage', 'VillageController@addnewvillage')->middleware('auth');
        Route::post('addvillage', 'VillageController@store')->middleware('auth');
        Route::get('editvillage/{id}', 'VillageController@edit')->middleware('auth');
        Route::post('updatevillage', 'VillageController@update');

        //Farmer Controller
        Route::get('allfarmer', 'FarmerController@index')->middleware('auth');
        Route::get('getfarmer', 'FarmerController@getFarmerAjax');
        Route::get('editfarmer/{id}', 'FarmerController@edit');
        Route::post('updatefarmer', 'FarmerController@update');

        Route::get('statusupdate/{id}', 'FarmerController@updatestatus');


        Route::get('add_farmer', 'FarmerController@create')->middleware('auth');
        Route::post('create_farmer', 'FarmerController@save');
        //BatchNumber Controller
        Route::get('allbatchnumber', 'BatchNumberController@index')->middleware('auth');
        Route::get('getbatch', 'BatchNumberController@getbatchAjax');
        Route::get('batchdetail/{id}', 'BatchNumberController@show')->middleware('auth');

        //Center Controller
        Route::get('allcenter', 'CenterController@index')->middleware('auth');
        Route::get('addcenter', 'CenterController@addnewcenter')->middleware('auth');
        Route::post('updatecenterrole', 'CenterController@updatecenterrole')->middleware('auth');
        Route::get('Addcenterdetail', 'CenterController@Addcenterdetail')->middleware('auth');
        Route::post('storecenter', 'CenterController@storecenter')->middleware('auth');
        Route::get('centerdetail/{id}', 'CenterController@centerdetail')->middleware('auth');
        Route::get('getcenter', 'CenterController@getCenterAjax');
        Route::get('editcenter/{id}', 'CenterController@edit')->middleware('auth');
        Route::post('updatecenter', 'CenterController@update');


        //Transection Controller
        Route::get('alltransection', 'TransectionController@index')->middleware('auth');
        Route::get('transactiondetail/{id}', 'TransectionController@detail')->middleware('auth');
        Route::get('gettransection', 'TransectionController@getTransectionAjax');

        //Container Controller
        Route::get('allcontainer', 'ContainerController@index')->middleware('auth');
        Route::get('addcontainer', 'ContainerController@addcontainer')->middleware('auth');
        Route::post('storecontainer', 'ContainerController@store')->middleware('auth');

        //Season Controller
        Route::get('allseason', 'SeasonController@index')->middleware('auth');
        Route::get('getseason', 'SeasonController@getSeasonAjax');
        Route::get('addseason', 'SeasonController@addseason')->middleware('auth');
        Route::post('addseason', 'SeasonController@store')->middleware('auth');
        Route::get('editseason/{id}', 'SeasonController@edit')->middleware('auth');
        Route::post('updateseason', 'SeasonController@update');
        // Route::get('deleteseason/{id}', 'SeasonController@delete');
        Route::get('seasonclose/{id}', 'SeasonController@seasonclose');



        //Weight Controller
        Route::get('governorweight', 'WeightController@governorweight');
        Route::get('governorweightcode/{id}', 'WeightController@governorweightcode');
        Route::get('regionweightcode/{id}', 'WeightController@regionweightcode');
        Route::get('villageweightcode/{id}', 'WeightController@villageweightcode');

        //User Controller
        Route::get('allusers', 'UserController@index');
        Route::get('getuser', 'UserController@getUserAjax');
        Route::get('adduser', 'UserController@adduser');
        Route::post('storeuser', 'UserController@store');
        Route::get('edituser/{id}', 'UserController@edit');
        Route::post('updateuser', 'UserController@update');
        Route::get('deleteuser/{id}', 'UserController@delete');
        //adminpasswordreset
        Route::get('resetpassword/{id}', 'UserController@resetpassword');
        Route::post('updatepassword', 'UserController@updatepassword');

        //::Session
        Route::get('sessions', 'SessionController@index');
        Route::get('getsessions', 'SessionController@getSessionAjax');
        Route::get('sessions/{id}', 'SessionController@sessionDetail');


        //Environment Controller
        Route::get('environments', 'EnvironmentsController@index')->middleware('auth');
        Route::get('getenvironments', 'EnvironmentsController@getEnvironmentsAjax');
        Route::get('environments/create', 'EnvironmentsController@create')->middleware('auth');
        Route::post('environments', 'EnvironmentsController@store')->middleware('auth');
        Route::get('environments/edit/{id}', 'EnvironmentsController@edit')->middleware('auth');
        Route::post('environments/{id}', 'EnvironmentsController@update')->middleware('auth');
    });
});
