<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Village extends Model {

    protected $casts = [
        'is_local' => 'boolean',
    ];
    protected $primaryKey = 'village_id';
    protected $fillable = ['village_code', 'village_title'];

}
