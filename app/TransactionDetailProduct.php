<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionDetailProduct extends Model
{
    protected $table = 'transaction_detail_products';

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
