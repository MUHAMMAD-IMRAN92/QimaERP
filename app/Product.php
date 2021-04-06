<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

    public function scopeMarket($query)
    {
        return $query->where('for', 1);
    }

    public function scopeSorting($query)
    {
        return $query->where('for', 2);
    }

    public function details()
    {
        return $this->hasMany(TransactionDetailProduct::class, 'product_id', 'id');
    }
}
