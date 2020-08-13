<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BatchNumber extends Model {

    protected $primaryKey = 'batch_id';
    protected $fillable = ['batch_id', 'batch_number', 'is_parent', 'created_by', 'is_local', 'local_code'];

}
