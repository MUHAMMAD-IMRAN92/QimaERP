<?php

namespace App\Http\Controllers\API;

use App\SystemDefination;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SystemDefinationController extends Controller
{
    public function get()
    {
        $system_defination = SystemDefination::all();
        $genetices = collect();
        $flavours = collect();

        $genetics =   $system_defination->filter(function ($query) {
            return $query->key == 'genetice';
        });
        $flavours =   $system_defination->filter(function ($query) {
            return $query->key == 'flavour';
        });

        $systemDefinations = [
            'genetics' =>   $genetics,
            'flavours' => $flavours
        ];

        return sendSuccess(config("statuscodes." . $this->app_lang . ".success_messages.RECV_COFFEE_MESSAGE"),    $systemDefinations);
    }
}
