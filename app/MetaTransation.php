<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MetaTransation extends Model
{

    protected $table = "transaction_meta";
    protected $primaryKey = 'transaction_meta_id';
    // protected $fillable = ['transaction_meta_id', 'transaction_id', 'key', 'value'];

    protected $guarded = [];

}
