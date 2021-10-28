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
use Illuminate\Support\Carbon;

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
        // $transactions = [];
        // $array  = [];
        // $users =  Role::with(['users'])->where('name', 'Coffee Buyer')->get();
        // $weight = 0;
        // foreach ($users as $user) {
        //     foreach ($user->users as $u) {


        //         $transactions = Transaction::where('sent_to', 2)->where('created_by', $u->user_id)->where('batch_number', 'NOT LIKE', '%000%')->get();
        //         foreach ($transactions as $transaction) {
        //             $weight += $transaction->details->sum('container_weight');
        //             $transInvoices = TransactionInvoice::where('transaction_id', $transaction->transaction_id)->get();
        //             $inoviceName = [];
        //             foreach ($transInvoices as  $transInvoice) {
        //                 $inovice = $transInvoice->invoice_id;
        //                 if ($file = FileSystem::where('file_id', $inovice)->first()) {
        //                     $inovice = $file->user_file_name;
        //                     array_push($inoviceName,  $inovice);
        //                 }
        //             }
        //             $farmerCode =  Str::beforeLast($transaction->batch_number, '-');
        //             $farmer  = Farmer::where('farmer_code', 'LIKE', $farmerCode . '%')->first();
        //             if ($farmer) {
        //                 $arr = [$u->user_id, $farmer->farmer_name,  $transaction->details->sum('container_weight'), $transaction->created_at, $inoviceName];
        //                 array_push($array, $arr);
        //             }
        //         }
        //     }
        // }

        // // 
        // // $transactions = Transaction::where('created_by', 5)->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->get();
        // // foreach ($transactions as $transaction) {
        // //     $weight +=  $transaction->details->sum('container_weight');
        // // }
        // // return $weight;

        // return view('admin.excel', [
        //     'array' => $array,
        //     'total_weight' => $weight,
        // ]);
        // $sentTo = ['Coffe Buyer' => 2, 'Coffee Buyer Manager' => 3, 'Center Manager' => 4, 'Processing Manager' => 5, 'Special processing' =>  7, 'Coffee drying' => 10, 'Yemen operative' => 13, 'Mill operative' => 17,   'Coffee Sorting' => 22,  'Yemen Pack Operative ' => 33, 'Yemen Local Market' => 193, 'Shipping' => 39, 'Uk Warehouse' => 43, 'China Warehouse' => 474];

        // $width = [2, 3, 10 , 100];
        // foreach ($width as $w) {
        //     echo '<pre>' . "<div style='background-color : green; width :" . $w . 'px' . "'>   </div>";
        // }
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
    }
}
