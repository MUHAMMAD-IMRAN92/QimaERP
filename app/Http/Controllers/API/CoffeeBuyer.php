<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\TransactionDetail;
use App\TransactionInvoice;
use App\TransactionLog;
use App\BatchNumber;
use App\Transaction;
use App\FileSystem;
use App\LoginUser;
use App\Village;
use App\Farmer;
use App\User;
use App\Season;
use App\CoffeeSession;
use Storage;
use App\Jobs\TransactionInvoices;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CoffeeBuyer extends Controller
{

    private $userId;
    private $user;
    private $app_lang;

    public function __construct()
    {
        set_time_limit(0);
        $headers = getallheaders();
        $checksession = LoginUser::where('session_key', $headers['session_token'])->first();
        if (isset($headers['app_lang'])) {
            $this->app_lang = $headers['app_lang'];
        } else {
            $this->app_lang = 'en';
        }
        if ($checksession) {
            $user = User::where('user_id', $checksession->user_id)->with('roles')->first();
            if ($user) {
                $this->user = $user;
                $this->userId = $user->user_id;
            } else {
                return sendError(Config("statuscodes." . $this->app_lang . ".error_messages.SESSION_EXPIRED"), 404);
            }
        } else {
            return sendError(Config("statuscodes." . $this->app_lang . ".error_messages.SESSION_EXPIRED"), 404);
        }
    }

    function farmer(Request $request)
    {
        $farmerName = $request->farmer_name;
        $villageCode = $request->village_code;
        $farmerCode = $request->farmer_code;
        $farmerNicn = $request->farmer_nicn;
        $user_image = asset('storage/app/images/demo_user_image.png');
        $user_image_path = asset('storage/app/images/');
        $farmers = Farmer::when($farmerName, function ($q) use ($farmerName) {
            $q->where(function ($q) use ($farmerName) {
                $q->where('farmer_name', 'like', "%$farmerName%");
            });
        })->when($villageCode, function ($q) use ($villageCode) {
            $q->where(function ($q) use ($villageCode) {
                $q->where('village_code', 'like', "%$villageCode%");
            });
        })->when($farmerCode, function ($q) use ($farmerCode) {
            $q->where(function ($q) use ($farmerCode) {
                $q->where('farmer_code', 'like', "%$farmerCode%");
            });
        })->when($farmerNicn, function ($q) use ($farmerNicn) {
            $q->where(function ($q) use ($farmerNicn) {
                $q->where('farmer_nicn', 'like', "%$farmerNicn%");
            });
        })->where('is_status', 1)->with(['profileImage' => function ($query) use ($user_image, $user_image_path) {
            $query->select('file_id', 'user_file_name', \DB::raw("IFNULL(CONCAT('" . $user_image_path . "/',`user_file_name`),IFNULL(`user_file_name`,'" . $user_image . "')) as user_file_name"));
        }])->with(['idcardImage' => function ($query) use ($user_image, $user_image_path) {
            $query->select('file_id', 'user_file_name', \DB::raw("IFNULL(CONCAT('" . $user_image_path . "/',`user_file_name`),IFNULL(`user_file_name`,'" . $user_image . "')) as user_file_name"));
        }])->orderBy('farmer_name')->get();

        foreach ($farmers as $key => $farmer) {
            $farmer->farmer_id_card_picture = '';
            $farmer->farmer_picture = '';
            if (isset($farmer->idcardImage) && isset($farmer->idcardImage->user_file_name)) {
                $farmer->farmer_id_card_picture = $farmer->idcardImage->user_file_name;
            }
            if (isset($farmer->profileImage) && isset($farmer->profileImage->user_file_name)) {
                $farmer->farmer_picture = $farmer->profileImage->user_file_name;
            }
            $farmer->farmer_village = $farmer->village_code;
            $farmer->farmer_id_card_no = $farmer->farmer_nicn;
            $farmer->makeHidden('idcardImage');
            $farmer->makeHidden('profileImage');
            $farmer->makeHidden('village_code');
            $farmer->makeHidden('farmer_nicn');
            $farmer->makeHidden('idcard_picture_id');
            $farmer->makeHidden('picture_id');
        }
        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.RETRIEVED_FARMER"), $farmers);
    }

    function batches(Request $request)
    {
        $search = $request->search;
        $batches = BatchNumber::when($search, function ($q) use ($search) {
            $q->where(function ($q) use ($search) {
                $q->where('batch_number', 'like', "%$search%");
            });
        })->where('is_parent', 0)->where('created_by', $this->userId)->get();
        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.RETRIEVED_BATCHES"), $batches);
    }

    function addFarmer(Request $request)
    {
        //::validation
        $validator = Validator::make($request->all(), [
            'farmers' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            return sendError($errors, 400);
        }
        $formaersId = array();
        $farmers = json_decode($request['farmers']);
        $i = 1;
        $x = 500;
        foreach ($farmers as $key => $farmer) {
            $alreadyFarmer = Farmer::where('farmer_nicn', $farmer->farmer_id_card_no)->first();
            if (!$alreadyFarmer) {
                $profileImageId = null;
                $idcardImageId = null;
                if ($farmer->farmer_picture) {
                    $destinationPath = 'storage/app/images/';
                    $file = base64_decode($farmer->farmer_picture);
                    $file_name = time() . $i . getFileExtensionForBase64($file);
                    file_put_contents($destinationPath . $file_name, $file);
                    $userProfileImage = FileSystem::create([
                        'user_file_name' => $file_name,
                    ]);
                    $profileImageId = $userProfileImage->file_id;
                }

                if ($farmer->farmer_id_card_picture) {
                    $destinationPath = 'storage/app/images/';
                    $idfile = base64_decode($farmer->farmer_id_card_picture);
                    $id_card_file_name = time() . $x . getFileExtensionForBase64($idfile);
                    file_put_contents($destinationPath . $id_card_file_name, $idfile);
                    $userIdCardImage = FileSystem::create([
                        'user_file_name' => $id_card_file_name,
                    ]);
                    $idcardImageId = $userIdCardImage->file_id;
                }
                $lastFarmer = Farmer::orderBy('farmer_id', 'desc')->first();
                $currentFarmerCode = 1;
                if (isset($lastFarmer) && $lastFarmer) {
                    $currentFarmerCode = ($lastFarmer->farmer_id + 1);
                }
                $currentFarmerCode = sprintf("%03d", $currentFarmerCode);
                $village = Village::where('village_code', 'like', "%$farmer->farmer_village%")->first();
                //::create new
                $alreadyFarmer = Farmer::create([
                    'farmer_code' => $village->village_code . '-' . $currentFarmerCode,
                    'farmer_name' => $farmer->farmer_name,
                    'village_code' => $farmer->farmer_village,
                    'picture_id' => $profileImageId,
                    'idcard_picture_id' => $idcardImageId,
                    'farmer_nicn' => $farmer->farmer_id_card_no,
                    'local_code' => $farmer->local_code,
                    'is_local' => 0,
                    'created_by' => $farmer->created_id,
                    'center_id' => $farmer->center_id,
                ]);
            } else {
                $alreadyFarmer->local_code = $alreadyFarmer->local_code . ',' . $farmer->local_code;
                $alreadyFarmer->save();
            }
            array_push($formaersId, $alreadyFarmer->farmer_id);
            $i++;
            $x++;
        }
        $user_image = asset('storage/app/images/demo_user_image.png');
        $user_image_path = asset('storage/app/images/');
        $farmers = Farmer::whereIn('farmer_id', $formaersId)->with(['profileImage' => function ($query) use ($user_image, $user_image_path) {
            $query->select('file_id', 'user_file_name', \DB::raw("IFNULL(CONCAT('" . $user_image_path . "/',`user_file_name`),IFNULL(`user_file_name`,'" . $user_image . "')) as user_file_name"));
        }])->with(['idcardImage' => function ($query) use ($user_image, $user_image_path) {
            $query->select('file_id', 'user_file_name', \DB::raw("IFNULL(CONCAT('" . $user_image_path . "/',`user_file_name`),IFNULL(`user_file_name`,'" . $user_image . "')) as user_file_name"));
        }])->get();

        foreach ($farmers as $key => $farmer) {
            $farmer->farmer_id_card_picture = '';
            $farmer->farmer_picture = '';
            if (isset($farmer->idcardImage) && isset($farmer->idcardImage->user_file_name)) {
                $farmer->farmer_id_card_picture = $farmer->idcardImage->user_file_name;
            }
            if (isset($farmer->profileImage) && isset($farmer->profileImage->user_file_name)) {
                $farmer->farmer_picture = $farmer->profileImage->user_file_name;
            }
            $farmer->farmer_village = $farmer->village_code;
            $farmer->farmer_id_card_no = $farmer->farmer_nicn;
            $farmer->makeHidden('idcardImage');
            $farmer->makeHidden('profileImage');
            $farmer->makeHidden('village_code');
            $farmer->makeHidden('farmer_nicn');
            $farmer->makeHidden('idcard_picture_id');
            $farmer->makeHidden('picture_id');
        }

        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.ADD_FARMER"), $farmers);
    }

    function addCoffeeWithBatchNumber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'batch_number' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            return sendError($errors, 400);
        }

        // $lastBID = 0;
        $lastTID = 0;
        //::last batch number id
        //  $lastBatchNumber = BatchNumber::orderBy('created_at', 'desc')->first();
        //::last transaction id
        $lastTransactionNumber = Transaction::orderBy('created_at', 'desc')->first();
        //        if ($lastBatchNumber) {
        //            $lastBID = $lastBatchNumber->batch_id;
        //        }
        if ($lastTransactionNumber) {
            $lastTID = $lastTransactionNumber->transaction_id;
        }

        $batches_numbers = json_decode(str_replace('&quot;', '"', $request->input('batch_number')));

        // foreach($batches_numbers as $batch){
        //     return response()->json([
        //         'local_updated_at_raw' => $batch->batch->transactions[0]->transactions->local_updated_at,
        //         'local_updated_at_carbon' => Carbon::parse($batch->batch->transactions[0]->transactions->local_updated_at)->toDateTimeString(),
        //         'local_updated_at_legacy' => date("Y-m-d H:i:s", strtotime($batch->batch->transactions[0]->transactions->local_updated_at))
        //     ]);
        // }

        $season = Season::where('status', 0)->first();
        $batchesArray = array();
        $sessiondata = 0;
        $sessionNo = Transaction::orderBy('session_no', 'desc')->first();
        if ($sessionNo) {
            $sessiondata = $sessionNo->session_no;
        }
        foreach ($batches_numbers as $key => $batch_numbers) {
            //::insert child batches id
            $childBatchNumberArray = array();
            //::insert child transactions id
            $childTransactionArray = array();
            //::Add child batch number
            foreach ($batch_numbers->child_batch as $key => $childBatch) {
                $newLastBID = 1;
                $lastBatchNumber = BatchNumber::orderBy('batch_id', 'desc')->first();
                if ($lastBatchNumber) {
                    $newLastBID = ($lastBatchNumber->batch_id + 1);
                }
                $removeLocalId = explode("-", $childBatch->batch->batch_number);
                //::remove last index of array
                array_pop($removeLocalId);
                // $farmerCode = implode("-", $removeLocalId) . '_' . $childBatch->batch->created_by;
                $farmerCode = implode("-", $removeLocalId);
                if ($childBatch->batch->is_server_id == 1) {
                    $farmer = Farmer::where('farmer_code', $farmerCode)->first();
                } else {
                    $farmer = Farmer::where('local_code', 'like', "%$farmerCode%")->first();
                }
                $newBatch = BatchNumber::create([
                    'batch_number' => $farmer->farmer_code . '-' . $newLastBID,
                    'is_parent' => 0,
                    'is_mixed' => 0,
                    'created_by' => $childBatch->batch->created_by,
                    'is_local' => FALSE,
                    'is_mixed' => 0,
                    'local_code' => $childBatch->batch->local_code,
                    'is_server_id' => $childBatch->batch->is_server_id,
                    'season_id' => $season->season_id,
                    'season_status' => $season->status,
                ]);
                //::child transactions
                if (isset($childBatch->transactions) && isset($childBatch->transactions[0]->transactions) && $childBatch->transactions[0]->transactions) {

                    $checkSession = CoffeeSession::where('user_id', $childBatch->transactions[0]->transactions->created_by)->where('local_session_id', $childBatch->transactions[0]->transactions->session_no)->first();
                    if ($checkSession) {
                        $childSession = $checkSession->server_session_id;
                    } else {
                        $sessiondata = $sessiondata + 1;
                        CoffeeSession::create([
                            'user_id' => $childBatch->transactions[0]->transactions->created_by,
                            'local_session_id' => $childBatch->transactions[0]->transactions->session_no,
                            'server_session_id' => $sessiondata,
                        ]);
                        $childSession = $sessiondata;
                    }

                    $newTransaction = Transaction::create([
                        'batch_number' => $newBatch->batch_number,
                        'is_parent' => 0,
                        'is_mixed' => 0,
                        'created_by' => $childBatch->transactions[0]->transactions->created_by,
                        'is_local' => FALSE,
                        'transaction_type' => $childBatch->transactions[0]->transactions->transaction_type,
                        'is_mixed' => 0,
                        'local_code' => $childBatch->transactions[0]->transactions->local_code,
                        'transaction_status' => 'created',
                        'is_server_id' => $childBatch->transactions[0]->transactions->is_server_id,
                        'is_new' => $childBatch->transactions[0]->transactions->is_new,
                        'sent_to' => 2,
                        'session_no' => $childSession,
                        'local_session_no' => $childBatch->transactions[0]->transactions->session_no,
                        'local_created_at' => Carbon::parse($childBatch->transactions[0]->transactions->local_created_at)->toDateTimeString(),
                        'local_updated_at' => Carbon::parse($childBatch->transactions[0]->transactions->local_updated_at)->toDateTimeString()
                    ]);

                    $transactionLog = TransactionLog::create([
                        'transaction_id' => $newTransaction->transaction_id,
                        'action' => 'created',
                        'created_by' => $childBatch->transactions[0]->transactions->created_by,
                        'entity_id' => $childBatch->transactions[0]->transactions->created_by,
                        'type' => 'coffee_buyer',
                        'local_created_at' => Carbon::parse($childBatch->transactions[0]->transactions->local_created_at)->toDateTimeString(),
                        'local_updated_at' => Carbon::parse($childBatch->transactions[0]->transactions->local_updated_at)->toDateTimeString()
                    ]);
                    //::child transactions details
                    if (isset($childBatch->transactions[0]->transactions_detail) && $childBatch->transactions[0]->transactions_detail) {
                        $transactionsDetails = $childBatch->transactions[0]->transactions_detail;
                        foreach ($transactionsDetails as $key => $transactionsDetail) {
                            TransactionDetail::create([
                                'transaction_id' => $newTransaction->transaction_id,
                                'container_number' => $transactionsDetail->container_number,
                                'created_by' => $transactionsDetail->created_by,
                                'is_local' => FALSE,
                                'container_weight' => $transactionsDetail->container_weight,
                                'weight_unit' => $transactionsDetail->weight_unit,
                            ]);
                        }
                    }
                }
                array_push($childBatchNumberArray, $newBatch->batch_id);
                array_push($childTransactionArray, $newTransaction->transaction_id);
            }

            //::add parent batch
            $removeLocalId = explode("-", $batch_numbers->batch->batch->batch_number);

            //::remove last index of array
            $newLastBID = 1;
            $lastBatchNumber = BatchNumber::orderBy('batch_id', 'desc')->first();
            if ($lastBatchNumber) {
                $newLastBID = ($lastBatchNumber->batch_id + 1);
            }
            array_pop($removeLocalId);
            $checkMixed = 0;
            if ($removeLocalId[3] == '000') {
                $parentBatchCode = implode("-", $removeLocalId) . '-' . ($newLastBID);
                $checkMixed = 1;
            }
            if ($removeLocalId[2] == '00') {
                $parentBatchCode = implode("-", $removeLocalId) . '-' . ($newLastBID);
                $checkMixed = 1;
            }
            if ($removeLocalId[2] == 'XXX') {
                $parentBatchCode = implode("-", $removeLocalId) . '-' . ($newLastBID);
                $checkMixed = 1;
            }

            if ($checkMixed == 0) {
                //$farmerCode = implode("-", $removeLocalId) . '_' . $batch_numbers->batch->created_by;
                $farmerCode = implode("-", $removeLocalId);
                if ($batch_numbers->batch->batch->is_server_id == 1) {
                    $farmer = Farmer::where('farmer_code', $farmerCode)->first();
                } else {
                    $farmer = Farmer::where('local_code', 'like', "%$farmerCode%")->first();
                }
                $parentBatchCode = $farmer->farmer_code . '-' . ($newLastBID);
            }

            $parentBatch = BatchNumber::create([
                'batch_number' => $parentBatchCode,
                'is_parent' => 0,
                'is_mixed' => $batch_numbers->batch->batch->is_mixed,
                'created_by' => $batch_numbers->batch->batch->created_by,
                'is_local' => FALSE,
                'local_code' => $batch_numbers->batch->batch->local_code,
                'is_server_id' => $batch_numbers->batch->batch->is_server_id,
                'season_id' => $season->season_id,
                'season_status' => $season->status,
            ]);
            if (isset($batch_numbers->batch->transactions[0]) && isset($batch_numbers->batch->transactions[0]->transactions) && $batch_numbers->batch->transactions[0]->transactions) {

                $pCheckSession = CoffeeSession::where('user_id', $batch_numbers->batch->transactions[0]->transactions->created_by)->where('local_session_id', $batch_numbers->batch->transactions[0]->transactions->session_no)->first();
                if ($pCheckSession) {
                    $pSession = $pCheckSession->server_session_id;
                } else {
                    $sessiondata = $sessiondata + 1;
                    CoffeeSession::create([
                        'user_id' => $batch_numbers->batch->transactions[0]->transactions->created_by,
                        'local_session_id' => $batch_numbers->batch->transactions[0]->transactions->session_no,
                        'server_session_id' => $sessiondata,
                    ]);
                    $pSession = $sessiondata;
                }

                $parentTransaction = Transaction::create([
                    'batch_number' => $parentBatch->batch_number,
                    'is_parent' => 0,
                    'is_mixed' => $batch_numbers->batch->transactions[0]->transactions->is_mixed,
                    'created_by' => $batch_numbers->batch->transactions[0]->transactions->created_by,
                    'is_local' => FALSE,
                    'transaction_type' => $batch_numbers->batch->transactions[0]->transactions->transaction_type,
                    'local_code' => $batch_numbers->batch->transactions[0]->transactions->local_code,
                    'transaction_status' => 'created',
                    'is_server_id' => $batch_numbers->batch->transactions[0]->transactions->is_server_id,
                    'is_new' => $batch_numbers->batch->transactions[0]->transactions->is_new,
                    'sent_to' => 2,
                    'session_no' => $pSession,
                    'local_session_no' => $pSession,
                    'local_created_at' => Carbon::parse($batch_numbers->batch->transactions[0]->transactions->local_created_at)->toDateTimeString(),
                    'local_updated_at' => Carbon::parse($batch_numbers->batch->transactions[0]->transactions->local_updated_at)->toDateTimeString()
                ]);

                $transactionLog = TransactionLog::create([
                    'transaction_id' => $parentTransaction->transaction_id,
                    'action' => 'created',
                    'created_by' => $batch_numbers->batch->transactions[0]->transactions->created_by,
                    'entity_id' => $batch_numbers->batch->transactions[0]->transactions->created_by,
                    'type' => 'coffee_buyer',
                    'local_created_at' => Carbon::parse($batch_numbers->batch->transactions[0]->transactions->local_created_at)->toDateTimeString(),
                    'local_updated_at' => Carbon::parse($batch_numbers->batch->transactions[0]->transactions->local_updated_at)->toDateTimeString()
                ]);


                if (isset($batch_numbers->batch->transactions[0]->transactions_detail) && $batch_numbers->batch->transactions[0]->transactions_detail) {

                    $transactionsDetails = $batch_numbers->batch->transactions[0]->transactions_detail;
                    foreach ($transactionsDetails as $key => $transactionsDetail) {
                        TransactionDetail::create([
                            'transaction_id' => $parentTransaction->transaction_id,
                            'container_number' => $transactionsDetail->container_number,
                            'created_by' => $transactionsDetail->created_by,
                            'is_local' => FALSE,
                            'container_weight' => $transactionsDetail->container_weight,
                            'weight_unit' => $transactionsDetail->weight_unit,
                        ]);
                    }
                }

                if (isset($batch_numbers->batch->transactions[0]->transactions_invoices) && $batch_numbers->batch->transactions[0]->transactions_invoices) {
                    $transactionsInvoices = $batch_numbers->batch->transactions[0]->transactions_invoices;
                    $i = 1;
                    foreach ($transactionsInvoices as $key => $transactionsInvoice) {
                        if ($transactionsInvoice->invoice_image) {
                            //TransactionInvoices::dispatch($parentTransaction->transaction_id, $transactionsInvoice->invoice_image, $transactionsInvoice->created_by ,$i)->delay(Carbon::now()->addSecond(1200));
                            $destinationPath = 'storage/app/images/';
                            $file = base64_decode($transactionsInvoice->invoice_image);
                            $file_name = time() . $i . getFileExtensionForBase64($file);
                            file_put_contents($destinationPath . $file_name, $file);
                            $userProfileImage = FileSystem::create([
                                'user_file_name' => $file_name,
                            ]);
                            TransactionInvoice::create([
                                'transaction_id' => $parentTransaction->transaction_id,
                                'created_by' => $transactionsInvoice->created_by,
                                'invoice_id' => $userProfileImage->file_id,
                            ]);
                        }
                        $i++;
                    }
                }
            }
            array_push($batchesArray, $parentBatch->batch_id);
            BatchNumber::whereIn('batch_id', $childBatchNumberArray)->update(['is_parent' => $parentBatch->batch_id]);
            Transaction::whereIn('transaction_id', $childTransactionArray)->update(['is_parent' => $parentTransaction->transaction_id]);
        }

        $dataArray = array();
        $currentBatchesData = BatchNumber::whereIn('batch_id', $batchesArray)->with('childBatchNumber.latestTransation.transactionDetail')->with('latestTransation.transactionDetail')->get();
        foreach ($currentBatchesData as $key => $currentBatchData) {
            $patentTransactions = null;
            $patentTransactionsDetail = null;
            $childBatches = array();
            if ($currentBatchData->is_mixed == 1) {
                if (isset($currentBatchData->childBatchNumber) && $currentBatchData->childBatchNumber) {
                    foreach ($currentBatchData->childBatchNumber as $key => $childBatchNumber) {
                        $childPatentTransactions = null;
                        $childPatentTransactionsDetail = null;
                        if (isset($childBatchNumber->latestTransation) && $childBatchNumber->latestTransation) {


                            if (isset($childBatchNumber->latestTransation->transactionDetail) && $childBatchNumber->latestTransation->transactionDetail) {
                                $childPatentTransactionsDetail = $childBatchNumber->latestTransation->transactionDetail;
                                $childBatchNumber->latestTransation->makeHidden('transactionDetail');
                            }
                            $childPatentTransactions = $childBatchNumber->latestTransation;

                            $childPatentTransactions->buyer_name = '';
                            $checkBatch = BatchNumber::where('batch_number', $childPatentTransactions->batch_number)->with('buyer')->first();
                            if ($checkBatch && isset($checkBatch->buyer)) {
                                $childPatentTransactions->buyer_name = $checkBatch->buyer->first_name . ' ' . $checkBatch->buyer->last_name;
                            }
                            $childBatchNumber->makeHidden('latestTransation');
                        }
                        $childtransactionData = ['transaction' => $childPatentTransactions, 'transactions_detail' => $childPatentTransactionsDetail];
                        $dataPush = ['batch' => $childBatchNumber, 'transactions' => $childtransactionData];
                        array_push($childBatches, $dataPush);
                    }
                }
                $currentBatchData->makeHidden('childBatchNumber');
            } else {
                $currentBatchData->makeHidden('childBatchNumber');
            }
            if (isset($currentBatchData->latestTransation) && $currentBatchData->latestTransation) {
                if (isset($currentBatchData->latestTransation->transactionDetail) && $currentBatchData->latestTransation->transactionDetail) {
                    $patentTransactionsDetail = $currentBatchData->latestTransation->transactionDetail;
                    $currentBatchData->latestTransation->makeHidden('transactionDetail');
                }
                $patentTransactions = $currentBatchData->latestTransation;

                $patentTransactions->buyer_name = '';
                $parentCheckBatch = BatchNumber::where('batch_number', $patentTransactions->batch_number)->with('buyer')->first();
                if ($parentCheckBatch && isset($parentCheckBatch->buyer)) {
                    $patentTransactions->buyer_name = $parentCheckBatch->buyer->first_name . ' ' . $parentCheckBatch->buyer->last_name;
                }
            }
            $currentBatchData->makeHidden('latestTransation');
            //  $currentBatchData->makeHidden('transaction');
            $transactionData = ['transaction' => $patentTransactions, 'transactions_detail' => $patentTransactionsDetail];

            $data = ['batch' => $currentBatchData, 'child_batch' => $childBatches, 'transactions' => $transactionData];
            array_push($dataArray, $data);
        }
        $session = 1;
        $findLatestSession = Transaction::where('created_by', $this->userId)->orderBy('local_session_no', 'desc')->first();
        if ($findLatestSession) {
            $session = ($findLatestSession->local_session_no + 1);
        }
        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.ADD_COFFEE"), $session);
        //        $currentBatch = BatchNumber::where('batch_id', $parentBatch->batch_id)->with('childBatchNumber.transaction.transactionDetail')->with('transaction.transactionDetail')->first();
        //        return sendSuccess('Coffee was added Successfully', $currentBatch);
    }

    //    function addCoffeeWithBatchNumber(Request $request) {
    //        //::validation
    //        $validator = Validator::make($request->all(), [
    //                    'batch_number' => 'required',
    //        ]);
    //        if ($validator->fails()) {
    //            $errors = implode(', ', $validator->errors()->all());
    //            return sendError($errors, 400);
    //        }
    //        $batches_numbers = json_decode($request['batch_number']);
    //        $transationArray = array();
    //        foreach ($batches_numbers as $key => $batch_numbers) {
    //            //::insert child transactions id
    //            $childTransactionArray = array();
    //            //::Add child batch number
    //            foreach ($batch_numbers->child_transactions as $key => $childBatch) {
    //                if ($childBatch->transaction->is_server_id == 1) {
    //                    $TbatchNumber = BatchNumber::where('batch_number', $childBatch->transaction->batch_number)->first();
    //                } else {
    //                    $bat = $childBatch->transaction->batch_number;
    //                    $TbatchNumber = BatchNumber::where('local_code', 'like', "$bat%")->first();
    //                }
    //                //::child transactions
    //                if (isset($childBatch->transaction) && isset($childBatch->transaction) && $childBatch->transaction) {
    //                    $newTransaction = Transaction::create([
    //                                'batch_number' => $TbatchNumber->batch_number,
    //                                'is_parent' => 0,
    //                                'is_mixed' => 0,
    //                                'created_by' => $childBatch->transaction->created_by,
    //                                'is_local' => FALSE,
    //                                'transaction_type' => $childBatch->transaction->transaction_type,
    //                                'is_mixed' => 0,
    //                                'local_code' => $childBatch->transaction->local_code,
    //                                'transaction_status' => 'created',
    //                                'is_server_id' => $childBatch->transaction->is_server_id,
    //                                'is_new' => $childBatch->transaction->is_new,
    //                                'sent_to' => 2,
    //                                'session_no' => 2,
    //                                'local_created_at' => $childBatch->transaction->created_at,
    //                    ]);
    //
    //                    $transactionLog = TransactionLog::create([
    //                                'transaction_id' => $newTransaction->transaction_id,
    //                                'action' => 'created',
    //                                'created_by' => $childBatch->transaction->created_by,
    //                                'entity_id' => $childBatch->transaction->created_by,
    //                                'type' => 'coffee_buyer',
    //                                'local_created_at' => $childBatch->transaction->created_at,
    //                    ]);
    //                    //::child transactions details
    //                    if (isset($childBatch->transactions_detail) && $childBatch->transactions_detail) {
    //                        $transactionsDetails = $childBatch->transactions_detail;
    //                        foreach ($transactionsDetails as $key => $transactionsDetail) {
    //                            TransactionDetail::create([
    //                                'transaction_id' => $newTransaction->transaction_id,
    //                                'container_number' => $transactionsDetail->container_number,
    //                                'created_by' => $transactionsDetail->created_by,
    //                                'is_local' => FALSE,
    //                                'container_weight' => $transactionsDetail->container_weight,
    //                                'weight_unit' => $transactionsDetail->weight_unit,
    //                            ]);
    //                        }
    //                    }
    //                }
    //                array_push($childTransactionArray, $newTransaction->transaction_id);
    //            }
    //            //::add parent batch
    //            //return print_r($batch_numbers,true);
    //            if (isset($batch_numbers->transactions) && isset($batch_numbers->transactions->transaction) && $batch_numbers->transactions->transaction) {
    //                $bat = $batch_numbers->transactions->transaction->batch_number;
    //
    //                if ($batch_numbers->transactions->transaction->is_server_id == 1) {
    //                    $batchNumber = BatchNumber::where('batch_number', $batch_numbers->transactions->transaction->batch_number)->first();
    //                } else {
    //                    $batchNumber = BatchNumber::where('local_code', 'like', "$bat%")->first();
    //                }
    //                if (!$batchNumber) {
    //                    $error_data['status'] = "error";
    //                    if ($this->app_lang == 'ar') {
    //                        $error_data['message'] = "لم يتم ايجاد رقم دفعة";
    //                    } else {
    //                        $error_data['message'] = "Batch number " . $bat . " Not found";
    //                    }
    //
    //                    $error_data['data'] = [];
    //
    //                    return json_encode($error_data);
    //                }
    //                $parentTransaction = Transaction::create([
    //                            'batch_number' => $batchNumber->batch_number,
    //                            'is_parent' => 0,
    //                            'is_mixed' => $batch_numbers->transactions->transaction->is_mixed,
    //                            'created_by' => $batch_numbers->transactions->transaction->created_by,
    //                            'is_local' => FALSE,
    //                            'transaction_type' => $batch_numbers->transactions->transaction->transaction_type,
    //                            'local_code' => $batch_numbers->transactions->transaction->local_code,
    //                            'transaction_status' => 'created',
    //                            'is_server_id' => $batch_numbers->transactions->transaction->is_server_id,
    //                            'is_new' => $batch_numbers->transactions->transaction->is_new,
    //                            'sent_to' => 2,
    //                            'session_no' => 2,
    //                            'local_created_at' => $batch_numbers->transactions->transaction->created_at,
    //                ]);
    //                $transactionLog = TransactionLog::create([
    //                            'transaction_id' => $parentTransaction->transaction_id,
    //                            'action' => 'created',
    //                            'created_by' => $batch_numbers->transactions->transaction->created_by,
    //                            'entity_id' => $batch_numbers->transactions->transaction->created_by,
    //                            'type' => 'coffee_buyer',
    //                            'local_created_at' => $batch_numbers->transactions->transaction->created_at,
    //                ]);
    //                if (isset($batch_numbers->transactions->transactions_detail) && $batch_numbers->transactions->transactions_detail) {
    //                    $transactionsDetails = $batch_numbers->transactions->transactions_detail;
    //                    foreach ($transactionsDetails as $key => $transactionsDetail) {
    //                        TransactionDetail::create([
    //                            'transaction_id' => $parentTransaction->transaction_id,
    //                            'container_number' => $transactionsDetail->container_number,
    //                            'created_by' => $transactionsDetail->created_by,
    //                            'is_local' => FALSE,
    //                            'container_weight' => $transactionsDetail->container_weight,
    //                            'weight_unit' => $transactionsDetail->weight_unit,
    //                        ]);
    //                    }
    //                }
    //
    //                if (isset($batch_numbers->transactions->transactions_invoices) && $batch_numbers->transactions->transactions_invoices) {
    //                    $transactionsInvoices = $batch_numbers->transactions->transactions_invoices;
    //                    $i = 1;
    //                    foreach ($transactionsInvoices as $key => $transactionsInvoice) {
    //
    //                        if ($transactionsInvoice->invoice_image) {
    //                            $destinationPath = 'storage/app/images/';
    //                            $file = base64_decode($transactionsInvoice->invoice_image);
    //                            $file_name = time() . $i . getFileExtensionForBase64($file);
    //                            file_put_contents($destinationPath . $file_name, $file);
    //                            $userProfileImage = FileSystem::create([
    //                                        'user_file_name' => $file_name,
    //                            ]);
    //                            TransactionInvoice::create([
    //                                'transaction_id' => $parentTransaction->transaction_id,
    //                                'created_by' => $transactionsInvoice->created_by,
    //                                'invoice_id' => $userProfileImage->file_id,
    //                            ]);
    //                        }
    //                        $i++;
    //                    }
    //                }
    //            }
    //            Transaction::whereIn('transaction_id', $childTransactionArray)->update(['is_parent' => $parentTransaction->transaction_id]);
    //            array_push($transationArray, $parentTransaction->transaction_id);
    //        }
    //        $dataArray = array();
    //        $user_image = asset('storage/app/images/demo_user_image.png');
    //        $user_image_path = asset('storage/app/images/');
    //        $currentBatchesData = Transaction::whereIn('transaction_id', $transationArray)->with('childTransation.transactionDetail', 'transactionDetail')->with(['transactions_invoices.invoice' => function($query) use($user_image, $user_image_path) {
    //                        $query->select('file_id', 'user_file_name', \DB::raw("IFNULL(CONCAT('" . $user_image_path . "/',`user_file_name`),IFNULL(`user_file_name`,'" . $user_image . "')) as user_file_name"));
    //                    }])->get();
    //
    //        foreach ($currentBatchesData as $key => $currentBatches) {
    //            $childTransaction = array();
    //            if (isset($currentBatches->childTransation) && $currentBatches->childTransation) {
    //
    //                foreach ($currentBatches->childTransation as $key => $child_transation) {
    //                    $emptyObject = array();
    //                    $child_transation->buyer_name = '';
    //                    $checkBatch = BatchNumber::where('batch_number', $child_transation->batch_number)->with('buyer')->get();
    //                    if ($checkBatch && isset($checkBatch->buyer)) {
    //                        $child_transation->buyer_name = $checkBatch->buyer->first_name . ' ' . $checkBatch->buyer->last_name;
    //                    }
    //                    $childtransactions_detail = $child_transation->transactionDetail;
    //                    $transactions_invoices = $child_transation->transactions_invoices;
    //                    $child_transation->makeHidden('transactionDetail');
    //                    $child_transation->makeHidden('transactions_invoices');
    //                    $parentData2 = ['transaction' => $currentBatches, 'transactions_detail' => $childtransactions_detail, 'transactions_invoices' => $emptyObject];
    //                    array_push($childTransaction, $parentData2);
    //                }
    //            }
    //
    //            $currentBatches->makeHidden('childTransation');
    //            $transactions_detail = $currentBatches->transactionDetail;
    //            $currentBatches->buyer_name = '';
    //            $parentCheckBatch = BatchNumber::where('batch_number', $currentBatches->batch_number)->with('buyer')->get();
    //            if ($parentCheckBatch && isset($parentCheckBatch->buyer)) {
    //                $currentBatches->buyer_name = $parentCheckBatch->buyer->first_name . ' ' . $parentCheckBatch->buyer->last_name;
    //            }
    //            if (isset($currentBatches->transactions_invoices)) {
    //                foreach ($currentBatches->transactions_invoices as $key => $transactions_invoices) {
    //                    $transactions_invoices->invoice_image = '';
    //                    if (isset($transactions_invoices->invoice)) {
    //                        $transactions_invoices->invoice_image = $transactions_invoices->invoice->user_file_name;
    //                    }
    //                    $transactions_invoices->makeHidden('invoice');
    //                }
    //            }
    //
    //            $transactions_invoices = $currentBatches->transactions_invoices;
    //            $currentBatches->makeHidden('transactionDetail');
    //            $currentBatches->makeHidden('transactions_invoices');
    //            $currentBatches->center_id = 0;
    //            $currentBatches->center_name = '';
    //            $currentBatches->colorCode = '';
    //            $parentData = ['transaction' => $currentBatches, 'transactions_detail' => $transactions_detail, 'transactions_invoices' => $transactions_invoices];
    //            $data = ['transactions' => $parentData, 'child_transactions' => $childTransaction];
    //            array_push($dataArray, $data);
    //        }
    //        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.ADD_COFFEE"), $dataArray);
    //    }

    function addCoffeeWithOutBatchNumber(Request $request)
    {
        //::validation
        $validator = Validator::make($request->all(), [
            'transaction' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            return sendError($errors, 400);
        }

        $allTransactions = array();
        $allTransactionsData = json_decode($request['transaction']);

        foreach ($allTransactionsData as $key => $transactions) {
            $newTransactionid = null;
            if (isset($transactions->transactions) && $transactions->transactions) {
                $batchCode = $transactions->transactions->batch_number;
                if ($transactions->transactions->is_server_id == FALSE) {
                    $currentBatch = BatchNumber::where('local_code', 'like', "$batchCode%")->first();
                    $batchCode = $currentBatch->batch_number;
                }
                $newTransaction = Transaction::create([
                    'batch_number' => $batchCode,
                    'is_parent' => 0,
                    'is_mixed' => 0,
                    'created_by' => $transactions->transactions->created_by,
                    'is_local' => FALSE,
                    'transaction_type' => $transactions->transactions->transaction_type,
                    'is_mixed' => 0,
                    'local_code' => $transactions->transactions->local_code,
                    'transaction_status' => 'created',
                    'is_server_id' => $transactions->transactions->is_server_id,
                    'is_new' => $transactions->transactions->is_new,
                    'sent_to' => 2,
                    'session_no' => 2,
                    'local_created_at' => $transactions->transactions->created_at,
                ]);
                $newTransactionid = $newTransaction->transaction_id;
                $transactionLog = TransactionLog::create([
                    'transaction_id' => $newTransaction->transaction_id,
                    'action' => 'created',
                    'created_by' => $transactions->transactions->created_by,
                    'entity_id' => $transactions->transactions->created_by,
                    'type' => 'coffee_buyer',
                    'local_created_at' => $transactions->transactions->created_at,
                ]);
            }
            //::child transactions details
            if (isset($transactions->transactions_detail) && $transactions->transactions_detail) {
                $transactionsDetails = $transactions->transactions_detail;
                foreach ($transactionsDetails as $key => $transactionsDetail) {
                    TransactionDetail::create([
                        'transaction_id' => $newTransactionid,
                        'container_number' => $transactionsDetail->container_number,
                        'created_by' => $transactionsDetail->created_by,
                        'is_local' => FALSE,
                        'container_weight' => $transactionsDetail->container_weight,
                        'weight_unit' => $transactionsDetail->weight_unit,
                    ]);
                }
            }

            if (isset($transactions->transactions_invoices) && $transactions->transactions_invoices) {
                $transactionsInvoices = $transactions->transactions_invoices;
                $i = 1;
                foreach ($transactionsInvoices as $key => $transactionsInvoice) {

                    if ($transactionsInvoice->invoice_image) {
                        $destinationPath = 'storage/app/images/';
                        $file = base64_decode($transactionsInvoice->invoice_image);
                        $file_name = time() . $i . getFileExtensionForBase64($file);
                        file_put_contents($destinationPath . $file_name, $file);
                        $userProfileImage = FileSystem::create([
                            'user_file_name' => $file_name,
                        ]);
                        TransactionInvoice::create([
                            'transaction_id' => $newTransaction->transaction_id,
                            'created_by' => $transactionsInvoice->created_by,
                            'invoice_id' => $userProfileImage->file_id,
                        ]);
                    }
                    $i++;
                }
            }
            $user_image = asset('storage/app/images/demo_user_image.png');
            $user_image_path = asset('storage/app/images/');

            $currentBatch = Transaction::where('transaction_id', $newTransactionid)->with('transactionDetail')->with(['transactions_invoices.invoice' => function ($query) use ($user_image, $user_image_path) {
                $query->select('file_id', 'user_file_name', \DB::raw("IFNULL(CONCAT('" . $user_image_path . "/',`user_file_name`),IFNULL(`user_file_name`,'" . $user_image . "')) as user_file_name"));
            }])->first();

            $currentBatch->buyer_name = '';
            $parentCheckBatch = BatchNumber::where('batch_number', $currentBatch->batch_number)->with('buyer')->first();
            if ($parentCheckBatch && isset($parentCheckBatch->buyer)) {
                $currentBatch->buyer_name = $parentCheckBatch->buyer->first_name . ' ' . $parentCheckBatch->buyer->last_name;
            }

            $transationsDetail = $currentBatch->transactionDetail;
            if (isset($currentBatch->transactions_invoices)) {
                foreach ($currentBatch->transactions_invoices as $key => $transactions_invoices) {
                    $transactions_invoices->invoice_image = '';
                    if (isset($transactions_invoices->invoice)) {
                        $transactions_invoices->invoice_image = $transactions_invoices->invoice->user_file_name;
                    }
                    $transactions_invoices->makeHidden('invoice');
                }
            }
            $currentBatch->makeHidden('transactionDetail');
            $transactions_invoices = $currentBatch->transactions_invoices;
            $currentBatch->makeHidden('transactions_invoices');
            $transations = $currentBatch;

            $data = ['transactions' => $transations, 'transactions_detail' => $transationsDetail, 'transactions_invoices' => $transactions_invoices];
            array_push($allTransactions, $data);
        }
        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.ADD_COFFEE"), $allTransactions);
    }

    function coffeeBuyerCoffee(Request $request)
    {
        $allTransactions = array();
        $user_image = asset('storage/app/images/demo_user_image.png');
        $user_image_path = asset('storage/app/images/');
        $transactions = Transaction::where('is_parent', 0)
            ->where('created_by', $this->userId)
            ->where('transaction_status', 'created')
            ->doesntHave('isReference')
            ->with('childTransation.transactionDetail', 'transactionDetail')
            ->with(['transactions_invoices.invoice' => function ($query) use ($user_image, $user_image_path) {
                $query->select('file_id', 'user_file_name', \DB::raw("IFNULL(CONCAT('" . $user_image_path . "/',`user_file_name`),IFNULL(`user_file_name`,'" . $user_image . "')) as user_file_name"));
            }])
            ->orderBy('transaction_id', 'desc')
            ->get();
        foreach ($transactions as $key => $transaction) {
            $childTransactions = array();
            if ($transaction->childTransation) {
                foreach ($transaction->childTransation as $key => $childTransation) {
                    $childTransation->buyer_name = '';
                    $checkBatch = BatchNumber::where('batch_number', $childTransation->batch_number)->with('buyer')->first();

                    if ($checkBatch && isset($checkBatch->buyer)) {
                        $childTransation->buyer_name = $checkBatch->buyer->first_name . ' ' . $checkBatch->buyer->last_name;
                    }

                    $childTransationDetail = $childTransation->transactionDetail;
                    $childTransation->makeHidden('transactionDetail');
                    $transactions_invoices = array();
                    $childData = ['transactions' => $childTransation, 'transactions_detail' => $childTransationDetail, 'transactions_invoices' => $transactions_invoices];
                    array_push($childTransactions, $childData);
                }
            }
            if (isset($transaction->transactions_invoices)) {
                foreach ($transaction->transactions_invoices as $key => $transactions_invoices) {
                    $transactions_invoices->invoice_image = '';
                    if (isset($transactions_invoices->invoice)) {
                        $transactions_invoices->invoice_image = $transactions_invoices->invoice->user_file_name;
                    }
                    $transactions_invoices->makeHidden('invoice');
                }
            }

            $transactionDetail = $transaction->transactionDetail;
            $transactions_invoices = $transaction->transactions_invoices;
            $transaction->buyer_name = '';
            $parentCheckBatch = BatchNumber::where('batch_number', $transaction->batch_number)->with('buyer')->first();
            if ($parentCheckBatch && isset($parentCheckBatch->buyer)) {
                $transaction->buyer_name = $parentCheckBatch->buyer->first_name . ' ' . $parentCheckBatch->buyer->last_name;
            }
            $transaction->makeHidden('transactionDetail');
            $transaction->makeHidden('childTransation');
            $transaction->makeHidden('transactions_invoices');
            $data = ['transactions' => $transaction, 'child_transaction' => $childTransactions, 'transactions_detail' => $transactionDetail, 'transactions_invoices' => $transactions_invoices];
            array_push($allTransactions, $data);
        }

        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.RETRIEVED_TRANSACTION"), $allTransactions);
    }

    function addBatchNumber(Request $request)
    {
        //::validation
        $validator = Validator::make($request->all(), [
            'batches' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            return sendError($errors, 400);
        }
        $batchesId = array();
        $batches = json_decode($request['batches']);
        $lastBatch = 1;
        $season = Season::where('status', 0)->first();
        $lastBatchNumber = BatchNumber::orderBy('batch_id', 'desc')->first();
        if ($lastBatchNumber) {
            $lastBatch = ($lastBatchNumber->batch_id + 1);
        }
        foreach ($batches as $key => $batch) {

            $removeLocalId = explode("-", $batch->batch_number);
            //::remove last index of array
            array_pop($removeLocalId);
            $farmerCode = implode("-", $removeLocalId);

            if ($removeLocalId[3] == '000') {
                $parentBatchCode = implode("-", $removeLocalId) . '-' . ($lastBatch);
            } else {
                if ($batch->is_server_id == 1) {
                    $farmer = Farmer::where('farmer_code', $farmerCode)->first();
                } else {
                    $farmer = Farmer::where('local_code', 'like', "%$farmerCode%")->first();
                }
                $parentBatchCode = $farmer->farmer_code . '-' . ($lastBatch);
            }
            $parentBatch = BatchNumber::create([
                'batch_number' => $parentBatchCode,
                'is_parent' => 0,
                'is_mixed' => $batch->is_mixed,
                'created_by' => $batch->created_by,
                'is_local' => FALSE,
                'local_code' => $batch->local_code,
                'is_server_id' => True,
                'season_id' => $season->season_id,
                'season_status' => $season->status,
            ]);

            array_push($batchesId, $parentBatch->batch_id);
        }

        $currentBatchesData = BatchNumber::whereIn('batch_id', $batchesId)->get();
        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.ADD_BATCHES"), $currentBatchesData);
    }
}
