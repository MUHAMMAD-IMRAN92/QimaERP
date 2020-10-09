<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CoffeeSession extends Model {

    protected $table = "coffee_session";
    protected $fillable = ['user_id', 'local_session_id', 'server_session_id'];

}
