<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\User;

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
$role = Role::create(['name' => 'Coffee Buyer']);

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['prefix' => 'v1', 'middleware' => ['headersmid', 'checkAppKey']], function () {
    Route::post('/login', 'API\AuthController@login');
    Route::post('/login', 'API\AuthController@login');
    Route::get('/villages', 'API\AuthController@villages');
    Route::get('/provinces', 'API\AuthController@provinces');



// Logged In users
    Route::group(['middleware' => ['checkSession']], function () {
        Route::post('/add_governerate', 'API\AuthController@addGovernerate');
        Route::get('/governerate', 'API\AuthController@governerate');
    });
});
