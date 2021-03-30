<?php

namespace App\Http\Controllers;

use App\BatchNumber;
use ProductNameSeeder;
use Illuminate\Support\Str;
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
        $secret = '81aGk2WUJt4Sy3tGr9gQRtDTTsg0MDxpRI1kY0Vdv1';
        abort_unless($request->secret === $secret, 403, 'Only dev is authorized for this route.');

        $batch_numbers = BatchNumber::all();

        $batch_numbers->each(function($batch_number){
            $local_code = Str::before($batch_number->local_code, 'T');
            $batch_number->local_code = $local_code;

            $batch_number->save();
        });

        return [
            'message' => 'Hello Dev Alee how are you feeling today?',
            'batch_numbers' => $batch_numbers
        ];
    }
}
