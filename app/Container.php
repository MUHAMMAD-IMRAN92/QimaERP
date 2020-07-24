<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Container extends Model {

    protected $primaryKey = 'container_id';
    protected $fillable = ['container_number', 'container_type', 'capacity'];

}
