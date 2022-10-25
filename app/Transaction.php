<?php

namespace App;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaction extends Model
{

    protected $primaryKey = 'transaction_id';

    protected $guarded = [];

    // protected $fillable = [
    //     'transaction_id',
    //     'batch_number',
    //     'is_parent',
    //     'created_by',
    //     'is_local',
    //     'local_code',
    //     'is_mixed',
    //     'transaction_type',
    //     'reference_id',
    //     'transaction_status',
    //     'is_server_id',
    //     'is_new',
    //     'sent_to',
    //     'is_sent',
    //     'session_no',
    //     'local_created_at',
    //     'is_in_process',
    //     'is_update_center',
    //     'local_session_no',
    //     'mill_id'
    // ];

    protected $casts = [
        'is_local' => 'boolean',
        'is_mixed' => 'boolean',
        'is_new' => 'boolean',
        'is_server_id' => 'boolean',
        'is_sent' => 'boolean',
        'is_in_process' => 'boolean',
        'is_update_center' => 'boolean',
        'ready_to_milled' => 'boolean',
        'is_specially_processed' => 'boolean'
    ];

    public function isSpecial()
    {
        return $this->is_special;
    }

    public function transactionDetail()
    {
        return $this->hasMany(TransactionDetail::class, 'transaction_id', 'transaction_id');
    }

    public function details()
    {
        return $this->hasMany(TransactionDetail::class, 'transaction_id', 'transaction_id');
    }

    public function isReference()
    {
        return $this->hasMany(Transaction::class, 'reference_id', 'transaction_id');
    }

    public function childTransation()
    {
        return $this->hasMany(Transaction::class, 'is_parent', 'transaction_id');
    }

    public function transactionLog()
    {
        return $this->hasMany(TransactionLog::class, 'transaction_id', 'transaction_id');
    }

    public function log()
    {
        return $this->hasOne(TransactionLog::class, 'transaction_id', 'transaction_id');
    }

    public function sent_transaction()
    {
        return $this->hasOne(Transaction::class, 'reference_id', 'transaction_id');
    }

    public function center_manager_received_transaction()
    {
        return $this->hasOne(Transaction::class, 'reference_id', 'transaction_id');
    }

    public function transactions_invoices()
    {
        return $this->hasMany(TransactionInvoice::class, 'transaction_id', 'transaction_id');
    }

    public function meta()
    {
        return $this->hasMany(MetaTransation::class, 'transaction_id', 'transaction_id');
    }

    public function child()
    {
        return $this->hasMany(ChildTransaction::class, 'parent_transaction_id', 'transaction_id');
    }

    public static function findParent($isServerId, $referenceId, $userId)
    {
        $parentTransaction = null;

        if ($isServerId) {
            $parentTransaction = static::where('transaction_id', $referenceId)->first();
        } else {
            $localCode = $referenceId . '_' . $userId . '-T';

            $parentTransaction = static::where('local_code', 'like', "$localCode%")
                ->latest('transaction_id')
                ->first();
        }

        return $parentTransaction;
    }

    public static function createAndLog($transactionData, $userId, $status, $sessionNo, $type, $transactionType = 1, $sentTo = null)
    {
        $parentTransaction = self::findParent($transactionData['is_server_id'], $transactionData['reference_id'], $userId);

        if (!$parentTransaction) {
            throw new Exception('Parent transaction not found. reference_id = ' . $transactionData['reference_id']);
        }

        $batchCheck = BatchNumber::where('batch_number', $transactionData['batch_number'])->exists();

        if (!$batchCheck) {
            throw new Exception("Batch Number [{$transactionData['batch_number']}] does not exists.");
        }

        $transaction =  static::create([
            'batch_number' => $transactionData['batch_number'],
            'is_parent' => 0,
            'created_by' => $userId,
            'is_local' => FALSE,
            'local_code' => $transactionData['local_code'],
            'is_special' => $parentTransaction->is_special,
            'is_mixed' => $transactionData['is_mixed'],
            'transaction_type' => $transactionType,
            'reference_id' => $parentTransaction->transaction_id,
            'transaction_status' => $status,
            'is_new' => 0,
            'sent_to' => $sentTo ?? $transactionData['sent_to'],
            'is_server_id' => true,
            'is_sent' => $transactionData['is_sent'],
            'session_no' => $sessionNo,
            'ready_to_milled' => $transactionData['ready_to_milled'],
            'is_in_process' => $transactionData['is_in_process'],
            'is_update_center' => array_key_exists('is_update_center', $transactionData) ? $transactionData['is_update_center'] : false,
            'local_session_no' => array_key_exists('local_session_no', $transactionData) ? $transactionData['local_session_no'] : false,
            'local_created_at' => toSqlDT($transactionData['local_created_at']),
            'local_updated_at' => toSqlDT($transactionData['local_updated_at'])
        ]);


        $log = new TransactionLog();
        $log->action = $status;
        $log->created_by = $userId;
        $log->entity_id = $transactionData['center_id'];
        $log->local_created_at = $transaction->local_created_at;
        $log->local_updated_at = $transaction->local_updated_at;
        $log->type =  $type;
        $log->center_name = array_key_exists('center_name', $transactionData) ? $transactionData['center_name'] : null;

        $transaction->log()->save($log);

        return $transaction;
    }

    public function createAll($transactionObj, $extras = [])
    {
    }

    public static function createGeneric($batchNumber, $userId, $referenceIds, $status, $sentTo, $sessionNo, $type)
    {
        $transaction =  static::create([
            'batch_number' => $batchNumber,
            'is_parent' => 0,
            'created_by' => $userId,
            'is_local' => FALSE,
            'local_code' => $batchNumber,
            'is_special' => false,
            'is_mixed' => true,
            'transaction_type' => 1,
            'reference_id' => $referenceIds,
            'transaction_status' => $status,
            'is_new' => 0,
            'sent_to' => $sentTo,
            'is_server_id' => true,
            'is_sent' => false,
            'session_no' => $sessionNo,
            'ready_to_milled' => false,
            'is_in_process' => false,
            'is_update_center' => false,
            'local_session_no' => $sessionNo,
            'local_created_at' => now()->toDateTimeString(),
            'local_updated_at' => now()->toDateTimeString()
        ]);

        $log = new TransactionLog();
        $log->action = $status;
        $log->created_by = $userId;
        $log->entity_id = 0;
        $log->local_created_at = $transaction->local_created_at;
        $log->local_updated_at = $transaction->local_updated_at;
        $log->type =  $type;
        $log->center_name = null;

        $transaction->log()->save($log);

        return $transaction;
    }

    public static function createGenericAccumulated($batchNumber, $userId, $isSpecial, $referenceIds, $localCode, $status, $sentTo, $sessionNo, $type)
    {
        $transaction =  static::create([
            'batch_number' => $batchNumber,
            'is_parent' => 0,
            'created_by' => $userId,
            'is_local' => FALSE,
            'local_code' => $localCode,
            'is_special' => $isSpecial,
            'is_mixed' => true,
            'transaction_type' => 5,
            'reference_id' => $referenceIds,
            'transaction_status' => $status,
            'is_new' => 0,
            'sent_to' => $sentTo,
            'is_server_id' => true,
            'is_sent' => false,
            'session_no' => $sessionNo,
            'ready_to_milled' => false,
            'is_in_process' => false,
            'is_update_center' => false,
            'local_session_no' => $sessionNo,
            'local_created_at' => now()->toDateTimeString(),
            'local_updated_at' => now()->toDateTimeString()
        ]);

        $log = new TransactionLog();
        $log->action = $status;
        $log->created_by = $userId;
        $log->entity_id = 0;
        $log->local_created_at = $transaction->local_created_at;
        $log->local_updated_at = $transaction->local_updated_at;
        $log->type =  $type;
        $log->center_name = null;

        $transaction->log()->save($log);

        return $transaction;
    }
    public function invoices()
    {
        $invId =   TransactionInvoice::where('transaction_id', $this->transaction_id)->get('invoice_id');
        return  FileSystem::whereIn('file_id', $invId)->get('user_file_name');
    }
    protected $appends = ['FarmerName'];
    public function getFarmerNameAttribute()
    {
        $farmer_code  =  Str::beforeLast($this->batch_number, '-');
        $farmer = Farmer::where('farmer_code', $farmer_code)->first();
        if ($farmer) {
            return $farmer->farmer_name;
        }
    }

    public static function createTransactionAndDetail($transaction)
    {

        $transactionObj = $transaction['transaction'];
        $user = auth()->user();
        // $parentTransaction = self::findParent($transaction['is_server_id'], $transaction['reference_id'], $user->user_id);

        // if (!$parentTransaction) {
        //     throw new Exception('Parent transaction not found. reference_id = ' . $transaction['reference_id'] . '=>'  . $transaction['transaction_id']);
        // }
        // if ($transaction['is_server_id']) {
        //     return  $result = $transaction;
        // }

        // $batchCheck = BatchNumber::where('batch_number', $transaction['batch_number'])->exists();

        // if (!$batchCheck) {
        //     throw new Exception("Batch Number [{$transaction['batch_number']}] does not exists.");
        // }
        // $sessionNo =  $sessionNo = CoffeeSession::max('server_session_id') + 1;
        if ($transactionObj['sent_to'] == 5) {
            $type = 'special_processing';
            $isSpecial = true;
        } else {
            $isSpecial = 1; //change from  parent
            $type = 'coffee_drying';
        }
        $removeLocalId = explode("-", $transactionObj['batch_number']);
        array_pop($removeLocalId);
        $season = Season::where('status', 0)->first();
        $lastBatchNumber = BatchNumber::orderBy('batch_id', 'desc')->first();
        if ($lastBatchNumber) {
            $newLastBID = ($lastBatchNumber->batch_id + 1);
        }

        $parentBatchCode = implode("-", $removeLocalId) . '-' . ($newLastBID);
        $parentBatch = BatchNumber::create([
            'batch_number' => $parentBatchCode,
            'is_parent' => 0,
            'is_mixed' => $lastBatchNumber->is_mixed,
            'created_by' => $lastBatchNumber->created_by,
            'is_local' => FALSE,
            'season_no' =>  $lastBatchNumber->season_no,
            'local_code' => $lastBatchNumber->local_code,
            'is_server_id' => $lastBatchNumber->is_server_id,
            'season_id' => $season->season_id,
            'season_status' => $season->status,
        ]);
        $result = self::create([
            'batch_number' => $parentBatchCode,
            'is_parent' => 0,
            'created_by' =>  $user->user_id,
            'is_local' => FALSE,
            'local_code' => $transactionObj['local_code'],
            'is_special' => $isSpecial,
            'is_mixed' => $transactionObj['is_mixed'],
            'transaction_type' => 0,
            'reference_id' => 000, //change from  parent
            'transaction_status' => 'minxed',
            'is_new' => 0,
            'sent_to' => $sentTo ?? $transactionObj['sent_to'],
            'is_server_id' => true,
            'is_sent' => $transactionObj['is_sent'],
            'session_no' => $transactionObj['session_no'],
            'ready_to_milled' => 0,
            'is_in_process' => 0,
            'is_update_center' => array_key_exists('is_update_center', $transactionObj) ? $transactionObj['is_update_center'] : false,
            'local_session_no' => array_key_exists('local_session_no', $transactionObj) ? $transactionObj['local_session_no'] : false,
            'local_created_at' => toSqlDT($transactionObj['local_created_at']),
            'local_updated_at' => toSqlDT($transactionObj['local_updated_at'])
        ]);

        $transactionLog = TransactionLog::create([
            'transaction_id' => $result->transaction_id,
            'action' => 'sent',
            'created_by' => $user->user_id,
            'entity_id' => 1,
            'center_name' => $result->center_name,
            'local_created_at' => date("Y-m-d H:i:s", strtotime($transactionObj['created_at'])),
            'type' => $type,
        ]);

        foreach ($transaction['transactionDetails'] as $key => $detail) {


            TransactionDetail::create([
                'transaction_id' => $result->transaction_id,
                'container_number' => $detail['container_number'],
                'created_by' => $detail['created_by'],
                'is_local' => FALSE,
                'container_weight' => $detail['container_weight'],
                'weight_unit' => $detail['weight_unit'],
                'center_id' => $detail['center_id'],
                'reference_id' => $detail['reference_id'],
            ]);

            TransactionDetail::where('transaction_id', $detail['reference_id'])->where('container_number', $detail['container_number'])->update(['container_status' => 1]);
        }


        return   $result =  $result->with('details');
    }
}
