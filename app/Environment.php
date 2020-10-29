<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Environment extends Model {

    protected $primaryKey = 'environment_id';
    protected $fillable = ['environment_id', 'environment_name'];

}
