<?php

namespace App\Http\Controllers;

use App\Transaction;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Laravel\Ui\Presets\React;

class ReportController extends Controller
{

    public function index()
    {
        return view('admin.report.index');
    }
    public function generateReport(Request $request)
    {

        $transactions = Transaction::with('details')->where('sent_to', 2)->whereBetween('created_at', [$request->from, $request->to])->where('batch_number', 'NOT LIKE', '%000%')->get()->groupBy('created_by');
        $name = "public/reports/" . uniqid(rand(), true)  . ".csv";


        header('Content-Type: text/xml,  charset=UTF-8; encoding=UTF-8');
        $file = fopen($name, 'w');
        fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
        // fputcsv($file, ['Buyer Name', 'Batch Number', 'Container weight', 'Date & Time']);
        fputcsv($file, ['Farmer name', 'Farmer Code', 'Batch Number', 'Date of Purchase', 'Purchase Weight', 'Governante', 'Region', 'Village', 'Coffee buyer', 'Purchase price kg', 'Total purchase']);
        foreach ($transactions as $key => $transaction) {
            $user = User::find($key);
            foreach ($transaction as $tran) {
                // $arr = ['Buyer Name' => $user->user_first_name . ' ' . $user->last_name, "Batch Number" => $tran->batch_number, 'Container weight' => $tran->details->sum('container_weight'), 'Date & Time' => $tran->created_at->format('Y:m:d H:i:s')];
                $arr = [
                    getFarmer($tran->batch_number), \Str::beforelast($tran->batch_number, '-',), $tran->batch_number, $tran->local_created_at, $tran->details->sum('container_weight'), getGov($tran->batch_number), getRegion($tran->batch_number),  getVillage($tran->batch_number),
                    $user->user_first_name . ' ' . $user->last_name, farmerPricePerKg($tran->batch_number), $tran->details->sum('container_weight') * farmerPricePerKg($tran->batch_number)
                ];
                // return $arr

                fputcsv($file, $arr);
            }
        }

        fclose($file);
        return Response::Download($name);
    }
    public function generateCfeDrying(Request $request)
    {
        $transactions = Transaction::with('details', 'metas')->where('sent_to', 10)->whereBetween('created_at', [$request->from, $request->to])->where('batch_number', 'NOT LIKE', '%000%')->get()->groupBy('created_by');
    }
    public function generateWarehouse(Request $request)
    {
        return $request->all();
    }
}
