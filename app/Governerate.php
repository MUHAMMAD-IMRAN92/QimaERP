<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Governerate extends Model {

    protected $casts = [
        'is_local' => 'boolean',
    ];
    protected $primaryKey = 'governerate_id';
    //public $incrementing = false;
    protected $fillable = ['governerate_code', 'governerate_title', 'created_by', 'is_local', 'local_code'];

}
