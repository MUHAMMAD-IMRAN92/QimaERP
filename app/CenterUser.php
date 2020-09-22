<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CenterUser extends Model
{
    public function center() {
        return $this->hasOne(Center::class, 'center_id', 'center_id');
    }
    
}
