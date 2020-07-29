<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Region extends Model {

    protected $casts = [
        'is_local' => 'boolean',
    ];
    protected $primaryKey = 'region_id';
    protected $fillable = ['region_code', 'region_title','created_by','is_local','local_code'];

}
