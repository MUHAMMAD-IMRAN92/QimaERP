<?php

namespace App;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;

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

    public static function createGenericAccumulated($batchNumber, $userId, $isSpecial, $referenceIds, $status, $sentTo, $sessionNo, $type)
    {
        $transaction =  static::create([
            'batch_number' => $batchNumber,
            'is_parent' => 0,
            'created_by' => $userId,
            'is_local' => FALSE,
            'local_code' => $batchNumber,
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
}
