<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model {

    protected $primaryKey = 'transaction_id';
    protected $fillable = ['transaction_id', 'batch_number', 'is_parent', 'created_by', 'is_local', 'local_code', 'is_mixed', 'transaction_type', 'reference_id', 'transaction_status'];


 

    public function transactionDetail() {
        return $this->hasMany(TransactionDetail::class, 'transaction_id', 'transaction_id');
    }

    public function isReference() {
        return $this->hasMany(Transaction::class, 'reference_id', 'transaction_id');
    }

    public function childTransation() {
        return $this->hasMany(Transaction::class, 'is_parent', 'transaction_id');
    }

}
