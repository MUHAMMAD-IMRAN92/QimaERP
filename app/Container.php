<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Container extends Model {

    protected $casts = [
        'is_local' => 'boolean',
    ];
    protected $primaryKey = 'container_id';
    protected $fillable = ['container_number', 'container_type', 'capacity', 'created_by', 'is_local', 'local_code'];

}
