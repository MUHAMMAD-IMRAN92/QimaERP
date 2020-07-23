<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Region extends Model {

    protected $primaryKey = 'region_id';
    protected $fillable = ['region_code', 'region_title'];

}
