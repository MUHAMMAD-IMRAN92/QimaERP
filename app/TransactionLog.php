<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionLog extends Model
{
     protected $primaryKey = 'transaction_log_id';
    protected $fillable = ['transaction_log_id', 'transaction_id', 'action', 'created_by', 'entity_id', 'local_created_at','type','center_name'];

}
