<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{
    protected $table = 'metas';

    protected $guarded = [];

    public function transactionDetail()
    {
        return $this->belongsTo(TransactionDetail::class, 'transaction_detail_id', 'transaction_detail_id');
    }
}
