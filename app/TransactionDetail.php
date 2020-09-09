<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model {

    protected $primaryKey = 'transaction_detail_id';
    protected $fillable = ['transaction_id', 'container_number', 'created_by', 'is_local', 'local_code', 'container_weight', 'weight_unit', 'container_status','center_id','reference_id'];
    protected $casts = [
        'is_local' => 'boolean',
    ];

    function transection() {
        return $this->hasOne(Transaction::class, 'transaction_id', 'transaction_id');
    }

}
