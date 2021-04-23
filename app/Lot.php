<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lot extends Model
{
    protected $guarded = [];

    public function details()
    {
        return $this->hasMany(LotDetail::class, 'lot_id', 'id');
    }
}
