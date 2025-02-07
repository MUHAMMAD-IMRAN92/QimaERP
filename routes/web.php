<?php

use App\Center;
use App\Http\Controllers\CropsterReportController;
use App\Transaction;
use App\Village;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
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

Route::get('/dev_test', 'DevTestController');
Route::get('login', 'AuthController@adminLogin')->name('login');
Route::get('/', function () {
    return Redirect::route('login');
    // return view('welcome');
});

Route::get('/updateFarmer', function () {

    $village = Village::get();
    foreach ($village as  $v) {
        return   $code = Str::beforeLast($v->village_code, '-');
        $v->update([
            'village_code' => $code
        ]);
    }
});


Route::group(['prefix' => 'admin'], function () {
    Route::get('login', 'AuthController@adminLogin');
    Route::get('/reset_view/{id}', 'UserController@resetView')->middleware('auth');
    Route::post('/reset_password/{id}', 'UserController@postReset')->middleware('auth');
    Route::post('login', 'AuthController@adminPostLogin');
    Route::group(['middleware' => ['CheckRole']], function () {
        Route::get('dashboard', 'AuthController@dashboard')->name('dashboard')->middleware('auth', 'CheckRole');
        Route::get('dashboardByDate', 'AuthController@dashboardByDate');
        Route::get('dashboardByDays', 'AuthController@dashboardByDays');
        Route::get('logout', 'AuthController@adminLogout');

        //dashboardAjaxCalls
        Route::get('dashboard/specialCoffee', 'AuthController@endDateAjax');
        Route::get('dashboard/nonspecialCoffee', 'AuthController@endDateAjaxNonSpecial');

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
        Route::post('regionupdate/{id}', 'RegionController@update')->middleware('auth');
        Route::get('deleteregion/{id}', 'RegionController@delete')->middleware('auth');
        Route::get('regionByDate', 'RegionController@regionByDate');
        Route::get('regionByDays', 'RegionController@regionByDays');
        Route::get('filterRegionByGovernrate', 'RegionController@filterRegionByGovernrate');
        Route::get('filterRegionByRegions', 'RegionController@filterRegionByRegions');
        Route::get('filterRegionByVillages', 'RegionController@filterRegionByVillages');



        //Village Controller
        Route::get('allvillage', 'VillageController@index')->middleware('auth');
        Route::get('getvillage', 'VillageController@getVillageAjax');
        Route::get('addnewvillage', 'VillageController@addnewvillage')->middleware('auth');
        Route::post('addvillage', 'VillageController@store')->middleware('auth');
        Route::get('editvillage/{id}', 'VillageController@edit')->middleware('auth');
        Route::post('updatevillage', 'VillageController@update');
        Route::get('village/profile/{village}', 'VillageController@villageProfile')->name('village.profile');
        Route::get('filter_village_profile/{id}', 'VillageController@filter_village_profile');
        Route::get('village_profile_days_filter/{id}', 'VillageController@village_profile_days_filter');

        //Farmer Controller
        Route::get('allfarmer', 'FarmerController@index')->middleware('auth');
        Route::get('getallFarmer', 'FarmerController@getAllFarmers')->middleware('auth');
        Route::get('getfarmer', 'FarmerController@getFarmerAjax');
        Route::get('editfarmer/{id}', 'FarmerController@edit');
        Route::post('updatefarmer', 'FarmerController@update');
        Route::get('statusupdate/{id}', 'FarmerController@updatestatus');
        Route::get('deletefarmer/{id}', 'FarmerController@delete');
        Route::get('filter_farmers', 'FarmerController@filterByDate');
        Route::get('filter_farmers_by_region', 'FarmerController@fiterByRegion');
        Route::get('filter_villages', 'FarmerController@fiterVillages');
        Route::get('farmer_by_villages', 'FarmerController@farmerByVillages');
        Route::get('farmer_by_date/{date}', 'FarmerController@famerByDate');
        Route::get('filter_farmer_profile/{id}', 'FarmerController@filter_farmer_profile');
        Route::get('farmer_by_date_profile/{date}', 'FarmerController@filter_farmer_profile_by_date');
        Route::get('add_farmer', 'FarmerController@create')->middleware('auth');
        Route::post('create_farmer', 'FarmerController@save');
        Route::get('farmer/profile/{id}', 'FarmerController@farmerProfile')->name('farmer.profile');
        Route::get('farmer_invoice/invoices/{id}', 'FarmerController@farmerInvoice');
        Route::get('farmer_id_documents/{id}', 'FarmerController@farmeridCard');
        Route::get('farmer_invoice/{id}', 'FarmerController@transaction_invoice');
        RoutE::get('farmers/download', 'FarmerController@download');

        //Coffee Buyer
        Route::get('allcoffeebuyer', 'CoffeeBuyerController@index')->middleware('auth');
        Route::get('filtercoffeebuyer', 'CoffeeBuyerController@filterByDate');
        Route::get('coffeebuyer_by_villages', 'CoffeeBuyerController@coffeebuyerByVillages');
        Route::get('coffeeBuyerByDate/{date}', 'CoffeeBuyerController@coffeeBuyerByDate');
        Route::get('coffeeBuyer/profile/{buyer}', 'CoffeeBuyerController@coffeeBuyerProfile')->name('coffeBuyer.profile');
        Route::get('filterByDateprofile/{date}', 'CoffeeBuyerController@filterByDateprofile');
        Route::get('daysFilter/{id}', 'CoffeeBuyerController@daysFilter');
        Route::get('filterBygovernrate', 'CoffeeBuyerController@filterBygovernrate');
        Route::get('filterByregions', 'CoffeeBuyerController@filterByregions');
        Route::get('filterByvillage', 'CoffeeBuyerController@filterByvillage');
        Route::get('assignVillages/{user}', 'CoffeeBuyerController@assignVillages');
        Route::post('assignVillages', 'CoffeeBuyerController@upload');
        Route::get('coffeeBuyer/reciepts/{id}', 'CoffeeBuyerController@reciepts');
        Route::get('coffeeBuyer/idcard/{id}', 'CoffeeBuyerController@idcard');
        Route::get('coffeeBuyer/profileid/{id}', 'CoffeeBuyerController@coffeeBuyerProfileByid');

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
        Route::get('del_center/{id}', function ($id) {
            $center = Center::find($id);
            if ($center) {
                $center->delete();
                return back()->with('msg', 'Center Deleted Successfully');
            }
        });


        //Transection Controller
        Route::get('alltransection', 'TransectionController@index')->middleware('auth');
        Route::get('transactiondetail/{id}', 'TransectionController@detail')->middleware('auth');
        Route::get('gettransection', 'TransectionController@getTransectionAjax');
        Route::get('rawTransactions/{id}', 'TransectionController@detail');
        Route::get('transactionByDays', 'TransectionController@transactionByDays');
        Route::get('transactionByDate', 'TransectionController@transactionByDate');



        //Container Controller
        Route::get('addcontainer', 'ContainerController@addcontainer')->middleware('auth');
        Route::post('storecontainer', 'ContainerController@store')->middleware('auth');
        Route::get('allcontainer', 'ContainerController@index')->middleware('auth');
        Route::get('container/detail/{id}', 'ContainerController@detail')->middleware('auth');


        //Season Controller
        Route::get('allseason', 'SeasonController@index')->middleware('auth');
        Route::get('getseason', 'SeasonController@getSeasonAjax');
        Route::get('addseason', 'SeasonController@addseason')->middleware('auth');
        Route::post('addseason', 'SeasonController@store')->middleware('auth');
        Route::get('editseason/{id}', 'SeasonController@edit')->middleware('auth');
        Route::post('updateseason', 'SeasonController@update');
        // Route::get('deleteseason/{id}', 'SeasonController@delete');
        Route::get('seasonclose/{id}', 'SeasonController@seasonclose');
        Route::get('endSeason/{id}', 'SeasonController@endSeason');



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
        //update user password
        Route::get('resetPasswordView/{id}', 'UserController@resetPasswordView');
        Route::post('updateUserPassword', 'UserController@updateUserPassword');

        //roles
        Route::get('roles', 'RoleController@index')->name('roles.index');
        Route::get('roles/create', 'RoleController@create')->name('roles.create');
        Route::post('roles', 'RoleController@store')->name('roles.store');


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
        //::milling
        Route::get('milling_coffee', 'MillingController@index')->middleware('auth');
        Route::get('get_milling_sessions', 'MillingController@getMillingSessionAjax');
        Route::get('milling_coffee/{id}', 'MillingController@milling')->middleware('auth');
        Route::post('milling_coffee', 'MillingController@millingCoffee')->middleware('auth');
        Route::get('new_milling_coffee', 'MillingController@newmillingCoffee')->middleware('auth');
        Route::get('milling_coffee_search', 'MillingController@newmillingCoffeeSearch')->middleware('auth');
        Route::post('newMilliing', 'MillingController@newpost')->middleware('auth');
        Route::get('/milling_export', 'MillingController@export');
        //miling filters
        Route::get('newMilling/filterByDays', 'MillingController@filterByDays');

        //packaging & export
        Route::get('/packaging/mixing', 'ExportMixingController@get')->name('mixing.index');
        Route::post('/packaging/mixing', 'ExportMixingController@post')->name('mixing.store');

        Route::get('/packaging/approval', 'PackingApprovalController@get');
        Route::post('/packaging/approval', 'PackingApprovalController@post');

        Route::get('local_market/sales', 'LocalMarketSales@get')->name('local_market.sales.index');

        Route::get('/inventory', 'InventoryController@index')->name('inventory.index');
        Route::get('/generate_inventory_excel', 'InventoryController@export');

        Route::get('/orders', 'OrderController@index')->name('orders.index');
        Route::get('/orders/create', 'OrderController@create')->name('orders.create');
        Route::get('/orders/{order}', 'OrderController@show')->name('orders.show');
        Route::post('/orders', 'OrderController@store')->name('orders.store');
        Route::post('/paidOrder', 'OrderController@paidOrder');


        Route::get('/customers', 'CustomerController@index');
        Route::get('/local_inventory', 'LocalMarketProductsController@weights');
        Route::get('/local_products', 'LocalMarketProductsController@index');
        //shipping
        Route::get('/shipping', 'ShipingController@index')->name('shipping.index');
        Route::post('/shipping', 'ShipingController@post')->name('shipping.post');
        Route::get('/search', 'ShipingController@search')->name('shipping.search');

        //system Definations
        Route::get('/system_definition', 'SystemDefinationController@index')->name('systemdefinition.index');
        Route::get('/system_definition/create', 'SystemDefinationController@create')->name('systemdefinition.create');
        Route::post('/system_definition', 'SystemDefinationController@post')->name('systemdefinition.post');
        Route::get('/system_definition/{genetic}', 'SystemDefinationController@delete')->name('systemdefinition.del');
        Route::get('/system_definition/edit/{genetic}', 'SystemDefinationController@edit')->name('systemdefinition.edit');
        Route::post('/system_definition/{genetic}', 'SystemDefinationController@update')->name('systemdefinition.update');
        Route::get('product_weights', 'LocalMarketProductsController@weights');
        Route::get('local_products', 'LocalMarketProductsController@index');

        //uk warehouse
        Route::get('/uk_warehouse/index', 'UkWareHouseController@index')->name('uk_warehouse.index');
        Route::get('/uk_warehouse/set_price/{id}', 'UkWareHouseController@prices')->name('uk.setPrice');
        Route::post('/uk_warehouse/post_price/{id}', 'UkWareHouseController@post');
        Route::post('/uk_warehouse/assignToChaina', 'UkWareHouseController@assignToChaina')->name('uk.assigntochaina');

        Route::get('/support', 'AuthController@support');
        Route::get('/view_support/{id}', 'AuthController@viewSupport');

        Route::get('/supplyChain', 'SupplyChainController@supplyChain');
        Route::get('supplyChain/{date}', 'SupplyChainController@supplyChainDays');
        Route::get('supplyChainDate', 'SupplyChainController@supplyChainDateFilter');

        //report controller
        Route::get('report', 'ReportController@index');
        Route::post('report/generate', 'ReportController@generateReport');
        Route::post('report/generateCfeDrying', 'ReportController@generateCfeDrying');
        Route::post('report/generateWarehouse', 'ReportController@generateWarehouse');

        Route::get('lot_mixing', 'LotMixingController@index')->name('lotMixing');
        Route::get('lot_mixing/filterByDays', 'LotMixingController@filterByDays');
        Route::get('lot_mixing/betweenDate', 'LotMixingController@betweenDate');
        Route::get('lot_mixing/filterLotMixingByGovernrate', 'LotMixingController@filterLotMixingByGovernrate');
        Route::get('lot_mixing/filterLotMixingByRegion', 'LotMixingController@filterLotMixingByRegion');
        Route::get('lot_mixing/filterLotMixingByvillage', 'LotMixingController@filterLotMixingByvillage');

        //Duplication
        Route::get('duplication', 'BatchNumberController@duplication')->name('duplication');

        //Cropster Report
        Route::post('add_cropster_report', 'CropsterReportController@index')->name('AddCropsterReport');

        // Route::get('importView', 'CropsterReportController@importView');
        // Route::post('importPost', 'CropsterReportController@importPost');

        Route::get('/backtrack', 'BatchNumberController@testing');
        Route::get('/farmers_upload', 'BatchNumberController@farmer');
        Route::post('/farmers_upload', 'BatchNumberController@farmerPost');
        Route::post('/villages_upload', 'BatchNumberController@villages');

        Route::get('/delBasket', 'BatchNumberController@deleteBasket');
    });
});
