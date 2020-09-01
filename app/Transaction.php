<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model {

    protected $primaryKey = 'transaction_id';
    protected $fillable = ['transaction_id', 'batch_number', 'is_parent', 'created_by', 'is_local', 'local_code', 'is_mixed', 'transaction_type', 'reference_id', 'transaction_status', 'is_server_id', 'is_new', 'sent_to'];
    protected $casts = [
        'is_local' => 'boolean',
        'is_mixed' => 'boolean',
        'is_new' => 'boolean',
        'is_server_id' => 'boolean',
    ];

    public function transactionDetail() {
        return $this->hasMany(TransactionDetail::class, 'transaction_id', 'transaction_id');
    }

    public function isReference() {
        return $this->hasMany(Transaction::class, 'reference_id', 'transaction_id');
    }

    public function childTransation() {
        return $this->hasMany(Transaction::class, 'is_parent', 'transaction_id');
    }

    public function transactionLog() {
        return $this->hasMany(TransactionLog::class, 'transaction_id', 'transaction_id');
    }
    public function log() {
        return $this->hasOne(TransactionLog::class, 'transaction_id', 'transaction_id');
    }

    public function sent_transaction() {
        return $this->hasOne(Transaction::class, 'reference_id', 'transaction_id');
    }

    public function center_manager_received_transaction() {
        return $this->hasOne(Transaction::class, 'reference_id', 'transaction_id');
    }

}
