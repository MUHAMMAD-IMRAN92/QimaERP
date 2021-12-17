<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionInvoice extends Model {

    protected $table = "transactions_invoice";
    protected $fillable = ['transaction_id', 'created_by', 'invoice_id' , 'invoive' , 'invoice_price'];


     public function invoice() {
         return $this->belongsTo(FileSystem::class, 'invoice_id', 'file_id');
    }
}
