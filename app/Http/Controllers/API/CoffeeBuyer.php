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
use Storage;

class CoffeeBuyer extends Controller {

    private $userId;
    private $user;

    public function __construct() {
        set_time_limit(0);
        $headers = getallheaders();
        $checksession = LoginUser::where('session_key', $headers['session_token'])->first();

        if ($checksession) {
            $user = User::where('user_id', $checksession->user_id)->with('roles')->first();
            if ($user) {
                $this->user = $user;
                $this->userId = $user->user_id;
            } else {
                return sendError('Session Expired', 404);
            }
        } else {
            return sendError('Session Expired', 404);
        }
    }

    function farmer(Request $request) {
        $farmerName = $request->farmer_name;
        $villageCode = $request->village_code;
        $farmerCode = $request->farmer_code;
        $farmerNicn = $request->farmer_nicn;
        $user_image = asset('storage/app/images/demo_user_image.png');
        $user_image_path = asset('storage/app/images/');
        $farmers = Farmer::when($farmerName, function($q) use ($farmerName) {
                    $q->where(function($q) use ($farmerName) {
                        $q->where('farmer_name', 'like', "%$farmerName%");
                    });
                })->when($villageCode, function($q) use ($villageCode) {
                    $q->where(function($q) use ($villageCode) {
                        $q->where('village_code', 'like', "%$villageCode%");
                    });
                })->when($farmerCode, function($q) use ($farmerCode) {
                    $q->where(function($q) use ($farmerCode) {
                        $q->where('farmer_code', 'like', "%$farmerCode%");
                    });
                })->when($farmerNicn, function($q) use ($farmerNicn) {
                    $q->where(function($q) use ($farmerNicn) {
                        $q->where('farmer_nicn', 'like', "%$farmerNicn%");
                    });
                })->where('is_status', 1)->with(['profileImage' => function($query) use($user_image, $user_image_path) {
                        $query->select('file_id', 'user_file_name', \DB::raw("IFNULL(CONCAT('" . $user_image_path . "/',`user_file_name`),IFNULL(`user_file_name`,'" . $user_image . "')) as user_file_name"));
                    }])->with(['idcardImage' => function($query) use($user_image, $user_image_path) {
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
        return sendSuccess('Successfully retrieved farmers', $farmers);
    }

    function batches(Request $request) {
        $search = $request->search;
        $batches = BatchNumber::when($search, function($q) use ($search) {
                    $q->where(function($q) use ($search) {
                        $q->where('batch_number', 'like', "%$search%");
                    });
                })->where('is_parent', 0)->where('created_by', $this->userId)->get();
        return sendSuccess('Successfully retrieved batches', $batches);
    }

    function addFarmer(Request $request) {
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
                $lastFarmer = Farmer::orderBy('created_at', 'desc')->first();
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
        $farmers = Farmer::whereIn('farmer_id', $formaersId)->with(['profileImage' => function($query) use($user_image, $user_image_path) {
                        $query->select('file_id', 'user_file_name', \DB::raw("IFNULL(CONCAT('" . $user_image_path . "/',`user_file_name`),IFNULL(`user_file_name`,'" . $user_image . "')) as user_file_name"));
                    }])->with(['idcardImage' => function($query) use($user_image, $user_image_path) {
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

        return sendSuccess('Farmer was created Successfully', $farmers);
    }

    function addCoffeeWithBatchNumber(Request $request) {
        //::validation
        $validator = Validator::make($request->all(), [
                    'batch_number' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            return sendError($errors, 400);
        }
        $batches_numbers = json_decode($request['batch_number']);
        $transationArray = array();
        foreach ($batches_numbers as $key => $batch_numbers) {
            //::insert child transactions id
            $childTransactionArray = array();
            //::Add child batch number
            foreach ($batch_numbers->child_transactions as $key => $childBatch) {
                if ($childBatch->transaction->is_server_id == 1) {
                    $TbatchNumber = BatchNumber::where('batch_number', $childBatch->transaction->batch_number)->first();
                } else {
                    $bat = $childBatch->transaction->batch_number;
                    $TbatchNumber = BatchNumber::where('local_code', 'like', "$bat%")->first();
                }
                //::child transactions
                if (isset($childBatch->transaction) && isset($childBatch->transaction) && $childBatch->transaction) {
                    $newTransaction = Transaction::create([
                                'batch_number' => $TbatchNumber->batch_number,
                                'is_parent' => 0,
                                'is_mixed' => 0,
                                'created_by' => $childBatch->transaction->created_by,
                                'is_local' => FALSE,
                                'transaction_type' => $childBatch->transaction->transaction_type,
                                'is_mixed' => 0,
                                'local_code' => $childBatch->transaction->local_code,
                                'transaction_status' => 'created',
                                'is_server_id' => $childBatch->transaction->is_server_id,
                                'is_new' => $childBatch->transaction->is_new,
                                'sent_to' => 2,
                    ]);

                    $transactionLog = TransactionLog::create([
                                'transaction_id' => $newTransaction->transaction_id,
                                'action' => 'created',
                                'created_by' => $childBatch->transaction->created_by,
                                'entity_id' => $childBatch->transaction->created_by,
                                'type' => 'coffee_buyer',
                                'local_created_at' => $childBatch->transaction->created_at,
                    ]);
                    //::child transactions details
                    if (isset($childBatch->transactions_detail) && $childBatch->transactions_detail) {
                        $transactionsDetails = $childBatch->transactions_detail;
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
                array_push($childTransactionArray, $newTransaction->transaction_id);
            }
            //::add parent batch
            return print_r($batch_numbers,true);
            if (isset($batch_numbers->transactions) && isset($batch_numbers->transactions->transaction) && $batch_numbers->transactions->transaction) {
                if ($batch_numbers->transactions->transaction->is_server_id == 1) {
                    $batchNumber = BatchNumber::where('batch_number', $batch_numbers->transactions->transaction->batch_number)->first();
                } else {
                    $bat = $batch_numbers->transactions->transaction->batch_number;
                    $batchNumber = BatchNumber::where('local_code', 'like', "$bat%")->first();
                }
                $parentTransaction = Transaction::create([
                            'batch_number' => $batchNumber->batch_number,
                            'is_parent' => 0,
                            'is_mixed' => $batch_numbers->transactions->transaction->is_mixed,
                            'created_by' => $batch_numbers->transactions->transaction->created_by,
                            'is_local' => FALSE,
                            'transaction_type' => $batch_numbers->transactions->transaction->transaction_type,
                            'local_code' => $batch_numbers->transactions->transaction->local_code,
                            'transaction_status' => 'created',
                            'is_server_id' => $batch_numbers->transactions->transaction->is_server_id,
                            'is_new' => $batch_numbers->transactions->transaction->is_new,
                            'sent_to' => 2,
                ]);
                $transactionLog = TransactionLog::create([
                            'transaction_id' => $parentTransaction->transaction_id,
                            'action' => 'created',
                            'created_by' => $batch_numbers->transactions->transaction->created_by,
                            'entity_id' => $batch_numbers->transactions->transaction->created_by,
                            'type' => 'coffee_buyer',
                            'local_created_at' => $batch_numbers->transactions->transaction->created_at,
                ]);
                if (isset($batch_numbers->transactions->transactions_detail) && $batch_numbers->transactions->transactions_detail) {
                    $transactionsDetails = $batch_numbers->transactions->transactions_detail;
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

                if (isset($batch_numbers->transactions->transactions_invoices) && $batch_numbers->transactions->transactions_invoices) {
                    $transactionsInvoices = $batch_numbers->transactions->transactions_invoices;
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
                                'transaction_id' => $parentTransaction->transaction_id,
                                'created_by' => $transactionsInvoice->created_by,
                                'invoice_id' => $userProfileImage->file_id,
                            ]);
                        }
                        $i++;
                    }
                }
            }
            Transaction::whereIn('transaction_id', $childTransactionArray)->update(['is_parent' => $parentTransaction->transaction_id]);
            array_push($transationArray, $parentTransaction->transaction_id);
        }
        $dataArray = array();
        $user_image = asset('storage/app/images/demo_user_image.png');
        $user_image_path = asset('storage/app/images/');
        $currentBatchesData = Transaction::whereIn('transaction_id', $transationArray)->with('childTransation.transactionDetail', 'transactionDetail')->with(['transactions_invoices.invoice' => function($query) use($user_image, $user_image_path) {
                        $query->select('file_id', 'user_file_name', \DB::raw("IFNULL(CONCAT('" . $user_image_path . "/',`user_file_name`),IFNULL(`user_file_name`,'" . $user_image . "')) as user_file_name"));
                    }])->get();

        foreach ($currentBatchesData as $key => $currentBatches) {
            $childTransaction = array();
            if (isset($currentBatches->childTransation) && $currentBatches->childTransation) {

                foreach ($currentBatches->childTransation as $key => $child_transation) {
                    $emptyObject = array();

                    $childtransactions_detail = $child_transation->transactionDetail;
                    $transactions_invoices = $child_transation->transactions_invoices;
                    $child_transation->makeHidden('transactionDetail');
                    $child_transation->makeHidden('transactions_invoices');
                    $parentData2 = ['transaction' => $currentBatches, 'transactions_detail' => $childtransactions_detail, 'transactions_invoices' => $emptyObject];
                    array_push($childTransaction, $parentData2);
                }
            }

            $currentBatches->makeHidden('childTransation');

            $transactions_detail = $currentBatches->transactionDetail;
            if (isset($currentBatches->transactions_invoices)) {
                foreach ($currentBatches->transactions_invoices as $key => $transactions_invoices) {
                    $transactions_invoices->invoice_image = '';
                    if (isset($transactions_invoices->invoice)) {
                        $transactions_invoices->invoice_image = $transactions_invoices->invoice->user_file_name;
                    }
                    $transactions_invoices->makeHidden('invoice');
                }
            }

            $transactions_invoices = $currentBatches->transactions_invoices;
            $currentBatches->makeHidden('transactionDetail');
            $currentBatches->makeHidden('transactions_invoices');
            $currentBatches->center_id = 0;
            $currentBatches->center_name = '';
            $currentBatches->colorCode = '';
            $parentData = ['transaction' => $currentBatches, 'transactions_detail' => $transactions_detail, 'transactions_invoices' => $transactions_invoices];

            $data = ['transactions' => $parentData, 'child_transactions' => $childTransaction];
            array_push($dataArray, $data);
        }

        return sendSuccess('Coffee was added Successfully', $dataArray);
    }

    function addCoffeeWithOutBatchNumber(Request $request) {
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
            $currentBatch = Transaction::where('transaction_id', $newTransactionid)->with('transactionDetail')->with(['transactions_invoices.invoice' => function($query) use($user_image, $user_image_path) {
                            $query->select('file_id', 'user_file_name', \DB::raw("IFNULL(CONCAT('" . $user_image_path . "/',`user_file_name`),IFNULL(`user_file_name`,'" . $user_image . "')) as user_file_name"));
                        }])->first();
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
        return sendSuccess('Coffee was added Successfully', $allTransactions);
    }

    function coffeeBuyerCoffee(Request $request) {
        $allTransactions = array();
        $user_image = asset('storage/app/images/demo_user_image.png');
        $user_image_path = asset('storage/app/images/');
        $transactions = Transaction::where('is_parent', 0)->where('created_by', $this->userId)->where('transaction_status', 'created')->doesntHave('isReference')->with('childTransation.transactionDetail', 'transactionDetail')->with(['transactions_invoices.invoice' => function($query) use($user_image, $user_image_path) {
                        $query->select('file_id', 'user_file_name', \DB::raw("IFNULL(CONCAT('" . $user_image_path . "/',`user_file_name`),IFNULL(`user_file_name`,'" . $user_image . "')) as user_file_name"));
                    }])->orderBy('transaction_id', 'desc')->get();
        foreach ($transactions as $key => $transaction) {
            $childTransactions = array();
            if ($transaction->childTransation) {
                foreach ($transaction->childTransation as $key => $childTransation) {
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
            $transaction->makeHidden('transactionDetail');
            $transaction->makeHidden('childTransation');
            $transaction->makeHidden('transactions_invoices');
            $data = ['transactions' => $transaction, 'child_transaction' => $childTransactions, 'transactions_detail' => $transactionDetail, 'transactions_invoices' => $transactions_invoices];
            array_push($allTransactions, $data);
        }

        return sendSuccess('Transactions retrieved successfully', $allTransactions);
    }

    function addBatchNumber(Request $request) {
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
        return sendSuccess('Farmer was created Successfully', $currentBatchesData);
    }

}
