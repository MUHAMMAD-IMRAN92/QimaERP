<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Village extends Model {

    protected $primaryKey = 'village_id';
    protected $fillable = ['village_code', 'village_title'];

}
