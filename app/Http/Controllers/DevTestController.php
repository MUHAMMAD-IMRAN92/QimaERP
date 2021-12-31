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
use App\Governerate;
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
        // $oldfarmerCode = 'SAN-HAR-133-1790';
        // $code = checkBatchNumber($oldfarmerCode);
        // dd($code);}
        // return parentBatch('SAN-HAY-03-000-2328');




        // $transactions = \DB::table('transactions')->where('sent_to', 10)
        //     ->select(\DB::raw('count(*) as duplicated', 'batch_number', 'sent_to', 'created_by', 'local_code'))
        //     ->groupBy('batch_number', 'batch_number', 'sent_to', 'created_by')
        //     ->get();
        // return $transactions;

        $transactions = \DB::table('transactions')->where('sent_to', 10)->select(\DB::raw('count(*) as duplicate'),'batch_number', 'sent_to', 'created_by', 'local_code')
            ->groupBy('batch_number', 'sent_to', 'created_by', 'local_code')->orderBy(\DB::raw('1'),'desc')->get();
        return $transactions;

    }
}
