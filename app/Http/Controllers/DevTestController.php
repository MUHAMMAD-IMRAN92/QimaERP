<?php

namespace App\Http\Controllers;

use stdClass;
use App\BatchNumber;
use App\Farmer;
use App\Transaction;
use App\TransactionDetail;
use App\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use App\FileSystem;
use App\TransactionInvoice;

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
        // $secret = '81aGk2WUJt4Sy3tGr9gQRtDTTsg0MDxpRI1kY0Vdv4';
        // abort_unless($request->secret === $secret, 403, 'Only dev is authorized for this route V3');

        // return response()->json([
        //     'msg' => 'Hello Dev, how is your day?',
        //     'live_test' => true
        // ]);
         $batch_number = BatchNumber::all();
        $allTr = [];
        foreach ($batch_number as $b) {
            $transaction = Transaction::where('batch_number', $b->batch_number)->get();
            if (count($transaction) == 0) {
                $b->delete();
            }
            array_push($allTr, $transaction);
        }
        return  $allTr;
        // 
        // $transactions = Transaction::where('created_by', 5)->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->get();
        // foreach ($transactions as $transaction) {
        //     $weight +=  $transaction->details->sum('container_weight');
        // }
        // return $weight;

        
    }
}
