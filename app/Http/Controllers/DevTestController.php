<?php

namespace App\Http\Controllers;

use App\Farmer;
use ProductNameSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class DevTestController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $secret = '81aGk2WUJt4Sy3tGr9gQRtDTTsg0MDxpRI1kY0Vd';
        abort_unless($request->secret === $secret, 403, 'Only dev is authorized for this route.');

         $farmers = Farmer::all();
         
         $farmers->each(function($farmer){
             $farmer->local_code = $farmer->farmer_code;
             $farmer->save();
         });
        

        return [
            'message' => 'Hello Dev Alee how are you feeling today?',
            'farmers' => $farmers 
        ];
    }
}
