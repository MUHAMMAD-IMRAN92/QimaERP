<?php

namespace App;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];

    protected $statuses = [
        1 => 'Created',
        2 => 'Prepared',
        3 => 'Collected',
        4 => 'Delivered',
        5 => 'Paid'
    ];

    public function details()
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'id');
    }

    public static function genOrderNumber()
    {
        $newId = static::max('id') + 1;

        return 'ORD-' . Str::padLeft($newId, 4, 0);
    }

    public function getStatus()
    {
        return $this->statuses[$this->status];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
}
