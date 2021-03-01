<?php

namespace App;

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
        'ready_to_milled' => 'boolean'
    ];

    public function transactionDetail()
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
}
