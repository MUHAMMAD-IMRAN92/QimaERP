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
//::Common Routes
        //::governerate
        Route::post('/add_governerate', 'API\CommonController@addGovernerate');
        Route::get('/governerate', 'API\CommonController@governerate');
        //::region
        Route::post('/add_region', 'API\CommonController@addRegion');
        Route::get('/regions', 'API\CommonController@regions');
        //::village
        Route::post('/add_village', 'API\CommonController@addVillage');
        Route::get('/villages', 'API\CommonController@villages');

        //::Get All Farmer
        Route::get('/farmers', 'API\CommonController@farmers');

        //::Add container
        Route::post('/add_container', 'API\CommonController@addContainer');
        Route::get('/containers', 'API\CommonController@containers');
        //::transactions
        Route::get('/transactions', 'API\CommonController@transactions');
        Route::get('/transactions_details', 'API\CommonController@transactionsDetails');
        //::all batches
        Route::get('/all_batches', 'API\CommonController@allBatches');
        //::--------------------------------------------
        //::Coffee buyer Routes
        //
         Route::post('/add_batches', 'API\CoffeeBuyer@addBatchNumber');
        //::Get coffee buyer farmer
        Route::get('/coffee_buyer_farmer', 'API\CoffeeBuyer@farmer');
        //::Get all batches
        Route::get('/batches', 'API\CoffeeBuyer@batches');
        //::add farmer
        Route::post('/add_farmer', 'API\CoffeeBuyer@addFarmer');
        //::Add Coffee with batch number
        Route::post('/coffee_buyer_add_coffee', 'API\CoffeeBuyer@addCoffeeWithBatchNumber');
        Route::post('/coffee_buyer_add_coffee_without_batch_number', 'API\CoffeeBuyer@addCoffeeWithOutBatchNumber');
//::Fetch Coffee Transactions
        Route::get('/get_coffee_buyer_transactions', 'API\CoffeeBuyer@coffeeBuyerCoffee');
        //::-------------------------------------------------
        //::Coffee Buyer Manager Routes
        //::coffee buyer manager
        Route::get('/coffee_buyer_manager_farmer', 'API\CoffeeBuyerManager@farmer');
        //::Centers
        Route::get('/centers', 'API\CoffeeBuyerManager@centers');

        //::Sent Transactions To Coffee Buyer Manager
        Route::post('/sent_transaction', 'API\CoffeeBuyerManager@sentTransactions');
        Route::get('/get_coffee_buyer_manager_coffee', 'API\CoffeeBuyerManager@coffeeBuyerManagerCoffee');
        Route::get('/get_coffee_buyer_manager_sent_coffee', 'API\CoffeeBuyerManager@coffeeBuyerManagerSentCoffeeTransaction');
        Route::post('/coffee_buyer_manager_approved_farmer', 'API\CoffeeBuyerManager@approvedFarmer');
        //::-------------------------------------------------
        //::Center Manager Routes
        //::center manager received transactions
        Route::get('/get_center_manager_coffee', 'API\CenterManagerController@centerManagerCoffee');
        Route::get('/get_center_manager_received_coffee', 'API\CenterManagerController@centerManagerReceivedCoffee');
        Route::post('/centers_manager_received_transaction', 'API\CenterManagerController@receivedTransactions');
        //::Processor Manager
        Route::get('/get_processor_manager_coffee', 'API\ProcessingManagerController@getProcessingManager');
        Route::get('/get_processor_role', 'API\ProcessingManagerController@fetchProcessorRole');
        Route::post('/processor_manager_sent_to_special_and_drying', 'API\ProcessingManagerController@sentToSpecialProcessingAndCoffeeDrying');


        Route::get('/get_sent_special_processing_and_drying_coffee', 'API\ProcessingManagerController@getSendSpecialProcessingAndDryingCoffee');
        Route::get('/get_sent_coffee_drying_coffee', 'API\ProcessingManagerController@getSendCoffeeDrying');

        //::Special processing Manager
        Route::get('/get_special_processing_coffee', 'API\SpecialProcessingController@getSpeicalProcessingManagerPendingCoffee');
        Route::get('/get_processing_list', 'API\SpecialProcessingController@processList');
        Route::get('/get_yeast_list', 'API\SpecialProcessingController@yeastList');
        Route::post('/received_special_processing_coffee', 'API\SpecialProcessingController@receivedSpecialProcessingCoffee');

        //::Coffee Drying Manager
        Route::get('/get_coffee_drying_coffee', 'API\CoffeeDryingController@getCoffeeDryingPendingCoffee');
        Route::post('/received_drying_coffee', 'API\CoffeeDryingController@receivedCoffeeDryingCoffee');
        Route::get('/get_received_drying_coffee', 'API\CoffeeDryingController@getReceivedCoffeeDryingCoffee');
        Route::post('/update_coffee_meta', 'API\CoffeeDryingController@updateMeta');
        Route::post('/sent_drying_coffee', 'API\CoffeeDryingController@sendCoffeeDryingCoffee');
        Route::post('/coffee_sent_to_yemen', 'API\CoffeeDryingController@coffeeSentToYemen');
        Route::post('/part_dry_coffee', 'API\CoffeeDryingController@partDryCoffee');
    });
});
