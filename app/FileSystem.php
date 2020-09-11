<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FileSystem extends Model {

    protected $primaryKey = 'file_id';
    protected $fillable = ['user_file_name'];

}
