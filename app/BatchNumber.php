<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BatchNumber extends Model {

    protected $primaryKey = 'batch_id';
    protected $fillable = ['batch_id', 'batch_number', 'is_parent', 'created_by', 'is_local', 'local_code', 'is_mixed'];

    public function childBatchNumber() {
        return $this->hasMany(BatchNumber::class, 'is_parent', 'batch_id');
    }

    public function transaction() {
        return $this->hasMany(Transaction::class, 'batch_number', 'batch_number');
    }

    public function sent_transaction() {
        return $this->hasMany(Transaction::class, 'batch_number', 'batch_number');
    }

    public function center_manager_received_transaction() {
        return $this->hasMany(Transaction::class, 'batch_number', 'batch_number');
    }

}
