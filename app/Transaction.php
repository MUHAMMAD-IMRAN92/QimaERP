<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model {

    protected $primaryKey = 'transaction_id';
    protected $fillable = ['transaction_id', 'batch_number', 'is_parent', 'created_by', 'is_local', 'local_code','is_mixed'];

}
