<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionInvoice extends Model {

    protected $primaryKey = 'transactions_invoice';
    protected $fillable = ['transaction_id', 'created_by', 'invoice_id'];

}
