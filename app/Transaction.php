<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model {

    protected $primaryKey = 'transaction_id';
    protected $fillable = ['transaction_id', 'batch_number', 'is_parent', 'created_by', 'is_local', 'local_code','is_mixed','transaction_type','reference_id','transaction_status'];

     function transactiondetail(){
            return $this->hasMany(TransactionDetail::class,'transaction_id');
        }

        public function children(){

			return $this->hasMany(self::class, 'is_parent');
			
		}
}
