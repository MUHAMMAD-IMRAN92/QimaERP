<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CoffeeProcess extends Model {

    protected $table = 'coffee_process';
    protected $primaryKey = 'process_id';
    protected $fillable = ['process_id', 'process_name','ar_process_name'];

}
