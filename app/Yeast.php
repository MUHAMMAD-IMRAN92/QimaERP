<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Yeast extends Model {

    protected $table = "yeast";
    protected $primaryKey = 'yeast_id';
    protected $fillable = ['yeast_id', 'yeast_name'];

}
