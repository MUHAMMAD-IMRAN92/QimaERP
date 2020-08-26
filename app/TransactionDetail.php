<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model {

    protected $primaryKey = 'transaction_detail_id';
    protected $fillable = ['transaction_id', 'container_number', 'created_by', 'is_local', 'local_code','weight'];



    function transection(){
        return $this->hasOne(Transaction::class,'transaction_id','transaction_id');
        }
}
