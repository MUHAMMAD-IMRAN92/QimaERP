<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Farmer;
use App\BatchNumber;
use App\Transaction;
use App\TransactionDetail;
use App\TransactionLog;

class CoffeeBuyerManager extends Controller {

    function farmer(Request $request) {
        $skip = 0;
        if ($request->skip) {
            $skip = $request->skip * 15;
        }
        $take = 15;
        $farmerName = $request->farmer_name;
        $governerateCode = $request->governerate_code;
        $regionCode = $request->region_code;
        $user_image = asset('storage/app/images/demo_user_image.png');
        $user_image_path = asset('storage/app/images/');
        $farmers = Farmer::when($farmerName, function($q) use ($farmerName) {
                    $q->where(function($q) use ($farmerName) {
                        $q->where('farmer_name', 'like', "%$farmerName%");
                    });
                })->when($governerateCode, function($q) use ($governerateCode) {
                    $q->where(function($q) use ($governerateCode) {
                        $q->where('governerate_code', 'like', "%$governerateCode%");
                    });
                })->when($regionCode, function($q) use ($regionCode) {
                    $q->where(function($q) use ($regionCode) {
                        $q->where('region_code', 'like', "%$regionCode%");
                    });
                })->skip($skip)->take($take)->with('governerate', 'region', 'village')->with(['profileImage' => function($query) use($user_image, $user_image_path) {
                        $query->select('file_id', 'system_file_name', \DB::raw("IFNULL(CONCAT('" . $user_image_path . "/',`system_file_name`),IFNULL(`system_file_name`,'" . $user_image . "')) as system_file_name"));
                    }])->with(['idcardImage' => function($query) use($user_image, $user_image_path) {
                        $query->select('file_id', 'system_file_name', \DB::raw("IFNULL(CONCAT('" . $user_image_path . "/',`system_file_name`),IFNULL(`system_file_name`,'" . $user_image . "')) as system_file_name"));
                    }])->orderBy('farmer_name')->get();
        return sendSuccess('Successfully retrieved farmers', $farmers);
    }
    
    
     function sentTransactions(Request $request) {
        //::validation
        $validator = Validator::make($request->all(), [
                    'transactions' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            return sendError($errors, 400);
        }
        $sentTransactions = json_decode($request['transactions']);
        foreach ($sentTransactions as $key => $sentTransaction) {
            $localTransactionCode = $sentTransaction->reference_id . '_' . $sentTransaction->created_by . '-T';
            $alreadyExistTransaction = Transaction::where('local_code', 'like', "$localTransactionCode%")->where('created_by', $sentTransaction->created_by)->first();

            $transaction = Transaction::create([
                        'batch_number' => $alreadyExistTransaction->batch_number,
                        'is_parent' => $sentTransaction->is_parent,
                        'is_mixed' => $sentTransaction->is_mixed,
                        'created_by' => $sentTransaction->created_by,
                        'is_local' => FALSE,
                        'transaction_type' => $sentTransaction->transaction_type,
                        'local_code' => $sentTransaction->local_code,
                        'transaction_status' => $sentTransaction->transaction_status,
                        'reference_id' => $alreadyExistTransaction->transaction_id,
            ]);

            $transactionLog = TransactionLog::create([
                        'transaction_id' => $transaction->transaction_id,
                        'action' => $sentTransaction->transaction_log->action,
                        'created_by' => $sentTransaction->transaction_log->created_by,
                        'sent_to' => $sentTransaction->transaction_log->sent_to,
                        'local_created_at' => $sentTransaction->transaction_log->local_created_at,
            ]);

            $transactionContainers = $sentTransaction->transactions_detail;
            foreach ($transactionContainers as $key => $transactionContainer) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->transaction_id,
                    'container_number' => $transactionContainer->container_number,
                    'created_by' => $sentTransaction->transaction_log->created_by,
                    'is_local' => FALSE,
                    'weight' => $transactionContainer->container_weight,
                ]);
            }
        }
        return sendSuccess('Transactions sent successfully', []);
    }

}
