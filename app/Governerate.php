<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Governerate extends Model {

    protected $primaryKey = 'governerate_id';
    //public $incrementing = false;
    protected $fillable = ['governerate_code', 'governerate_title'];

}
