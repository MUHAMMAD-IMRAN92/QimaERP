<?php

namespace App;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];

    public function details()
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'id');
    }

    public static function genOrderNumber()
    {
        $newId = static::max('id') + 1;

        return 'ORD-' . Str::padLeft($newId, 3, 0);
    }
}
