<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LotDetail extends Model
{
    protected $guarded = [];

    public function lot()
    {
        return $this->belongsTo(Lot::class, 'lot_id', 'id');
    }
}
