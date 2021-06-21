<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => ['headersmid', 'checkAppKey']], function () {

    Route::post('/login', 'API\AuthController@login');

    // Logged In users
    Route::middleware(['auth:sanctum'])->group(function () {

        //::Common Routes

        // Admin Panel Routes

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

        // ::Get Container Type
        Route::get('/get_container_type', 'API\CommonController@getContainerType');

        //::Add container
        Route::post('/add_container', 'API\CommonController@addContainer');
        Route::get('/containers', 'API\CommonController@containers');

        //::transactions
        Route::get('/transactions', 'API\CommonController@transactions');
        Route::get('/transactions_details', 'API\CommonController@transactionsDetails');

        //::all batches
        Route::get('/all_batches', 'API\CommonController@allBatches');

        //::Coffee buyer Routes
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

        // Method has been commented out in controller
        // Route::post('/special_processing_coffee_sent_to_drying', 'API\SpecialProcessingController@spSendCoffeeDryingCoffee');

        //::Coffee Drying Manager
        Route::get('/get_coffee_drying_coffee', 'API\CoffeeDryingController@getCoffeeDryingPendingCoffee');
        Route::post('/received_drying_coffee', 'API\CoffeeDryingController@receivedCoffeeDryingCoffee');
        Route::get('/get_received_drying_coffee', 'API\CoffeeDryingController@getReceivedCoffeeDryingCoffee');
        Route::post('/update_coffee_meta', 'API\CoffeeDryingController@updateMeta');

        // This method been commented out in controller
        // Route::post('/sent_drying_coffee', 'API\CoffeeDryingController@sendCoffeeDryingCoffee');
        // Route::post('/coffee_sent_to_yemen', 'API\CoffeeDryingController@coffeeSentToYemen');
        // Route::post('/part_dry_coffee', 'API\CoffeeDryingController@partDryCoffee');

        Route::get('/get_environment_list', 'API\CoffeeDryingController@environmentList');

        //::Yemen Operative
        Route::get('/get_yemen_operative_coffee', 'API\YemenOperativeController@getYemenOperativeCoffee');
        Route::post('/received_yemen_operative_coffee', 'API\YemenOperativeController@receivedYemenOperative');

        //::Mill Operative
        Route::get('/products', 'API\ProductController@all');
        Route::get('/products/sorting', 'API\ProductController@sorting');
        Route::get('/products/milling', 'API\ProductController@milling');
        Route::get('/products/export', 'API\ProductController@export');
        Route::get('/mill_operative_coffee', 'API\MillOperativeController@sendCoffee');
        Route::post('/mill_operative_coffee', 'API\MillOperativeController@receiveCoffee');
        //For Sorting Coffee
        Route::get('so_coffee_sorting', 'API\SOCoffeeSortingController@getCoffee');
        Route::post('so_coffee_sorting', 'API\SOCoffeeSortingController@sendCoffee');
        //For Local Market Coffee
        Route::get('yo_local_market', 'API\YOLocalMarketController@getCoffee');
        Route::get('yo_local_market/prepaired', 'API\YOLocalMarketController@prepaired');
        Route::post('yo_local_market', 'API\YOLocalMarketController@sendCoffee');


        // Yemene Operative Export Coffee
        Route::get('yo_export_coffee', 'API\YOExportController@get');
        Route::post('yo_export_coffee', 'API\YOExportController@post');

        Route::get('yo_local_inventory', 'API\InventoryController@get');

        Route::get('lm_inventory', 'API\LMInventoryController@index');

        Route::get('/packaging_op', 'API\PackagingOpController@get');
        Route::post('/packaging_op', 'API\PackagingOpController@post');

        //shipping
        Route::get('/shipping', 'API\ShipingController@get');
        Route::post('/shipping', 'API\ShipingController@post');
        Route::get('/shipping/transport', 'API\ShipingController@transport');
        Route::post('/shipping/transport', 'API\ShipingController@transportPost');

        //Uk WareHouse
        Route::get('/uk_warehouse', 'API\UkWareHouse@get');
        Route::post('/uk_warehouse', 'API\UkWareHouse@post');

        //UK Quality
        Route::get('/uk_quality', 'API\UkQuality@get');
        Route::post('/uk_quality', 'API\UkQuality@post');

        //system defination
        Route::get('/system_defination', 'API\SystemDefinationController@get');
    });
});
