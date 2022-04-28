<?php

namespace App\Http\Controllers;

use App\Center;
use App\CenterUser;
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
        fputcsv($file, ['Farmer name', 'Farmer Code', 'Batch Number', 'Date of Purchase', 'Purchase Weight', 'Baskets', 'Governante', 'Region', 'Village', 'Coffee buyer', 'Purchase price kg', 'Total purchase']);
        foreach ($transactions as $key => $transaction) {
            $user = User::find($key);
            foreach ($transaction as $tran) {
                $detailsString = '';
                foreach ($tran->details as $detail) {
                    $detailsString .= (string)($detail->container_number . ':' . $detail->container_weight) . ',';
                }
                // $arr = ['Buyer Name' => $user->user_first_name . ' ' . $user->last_name, "Batch Number" => $tran->batch_number, 'Container weight' => $tran->details->sum('container_weight'), 'Date & Time' => $tran->created_at->format('Y:m:d H:i:s')];
                $arr = [
                    getFarmer($tran->batch_number), \Str::beforelast($tran->batch_number, '-',), $tran->batch_number, $tran->local_created_at, $tran->details->sum('container_weight'), $detailsString,
                    getGov($tran->batch_number), getRegion($tran->batch_number),  getVillage($tran->batch_number),
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
        $transactions = Transaction::with(['meta' => function ($q) {
            $q->where('key', 'moisture_measurement')->latest();
        }])->with('log', 'details')->where('sent_to', 10)->where('transaction_status', 'sent')->whereBetween('created_at', [$request->from, $request->to])->where('batch_number', 'NOT LIKE', '%000%')->get()->groupBy('created_by');


        $name = "public/reports/" . uniqid(rand(), true)  . ".csv";


        header('Content-Type: text/xml,  charset=UTF-8; encoding=UTF-8');
        $file = fopen($name, 'w');
        fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
        // fputcsv($file, ['Buyer Name', 'Batch Number', 'Container weight', 'Date & Time']);
        fputcsv($file, ['Coffee Centre', 'Centre Manager', 'Drying Bed Number', 'Farmer name', 'Farmer code', 'Batch number', 'Input weight', 'Date of input', 'Last moisture recorded %', 'Last moisture recorded date and time', 'Total drying days']);
        foreach ($transactions as $key => $transaction) {
            // $user = User::find($key);
            foreach ($transaction as $tran) {
                // dd($tran);
                $center = Center::find($tran->log->entity_id);
                $centerName = '';
                if ($center) {
                    $centerName = $center->center_name;
                }
                $centerUser = CenterUser::where('center_id', $tran->log->entity_id)->first();
                $managerName = '';
                if ($centerUser) {
                    $user = User::find($centerUser->user_id);
                    $managerName =    $user->user_first_name . ' ' . $user->last_name;
                }
                $dryingBed = '';

                foreach ($tran->details as $detail) {
                    $dryingBed = $detail->container_name;
                }
                $meta = '';
                $localcareatedAt = '';
                foreach ($tran->meta as $m) {
                    $meta = $m->value;
                    $localcareatedAt = $m->local_created_at;
                }
                $now = \Carbon\Carbon::now();
                $today = $now->today()->toDateString();
                // $arr = ['Buyer Name' => $user->user_first_name . ' ' . $user->last_name, "Batch Number" => $tran->batch_number, 'Container weight' => $tran->details->sum('container_weight'), 'Date & Time' => $tran->created_at->format('Y:m:d H:i:s')];
                $arr = [
                    $centerName, $managerName,  $dryingBed,
                    getFarmer($tran->batch_number), \Str::beforelast($tran->batch_number, '-'), $tran->batch_number, $tran->details->sum('container_weight'), $tran->local_created_at, $meta,
                    $localcareatedAt, $tran->created_at->diffInDays($today) . 'Days'

                ];
                // return $arr

                fputcsv($file, $arr);
            }
        }

        fclose($file);
        return Response::Download($name);
    }
    public function generateWarehouse(Request $request)
    {
        $transactions = Transaction::with(['meta' => function ($q) {
            $q->where('key', 'yemen_warehouse')->latest();
        }])->with('log', 'details')->where('sent_to', 13)->where('transaction_status', 'sent')->whereBetween('created_at', [$request->from, $request->to])->get()->groupBy('created_by');



        $name = "public/reports/" . uniqid(rand(), true)  . ".csv";


        header('Content-Type: text/xml,  charset=UTF-8; encoding=UTF-8');
        $file = fopen($name, 'w');
        fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
        // fputcsv($file, ['Buyer Name', 'Batch Number', 'Container weight', 'Date & Time']);
        fputcsv($file, ['Coffee Centre', 'Centre Manager',  'Batch number', 'Baskets', 'Input weight', 'Date of input', 'WareHouse']);
        foreach ($transactions as $key => $transaction) {
            // $user = User::find($key);
            foreach ($transaction as $tran) {
                // dd($tran);
                $detailsString = '';
                foreach ($tran->details as $detail) {
                    $detailsString .= (string)($detail->container_number . ':' . $detail->container_weight) . ',';
                }
                $center = Center::find($tran->log->entity_id);
                $centerName = '';
                if ($center) {
                    $centerName = $center->center_name;
                }
                $centerUser = CenterUser::where('center_id', $tran->log->entity_id)->first();
                $managerName = '';
                if ($centerUser) {
                    $user = User::find($centerUser->user_id);
                    $managerName =    $user->user_first_name . ' ' . $user->last_name;
                }
                $meta = '';
                foreach ($tran->meta as $m) {
                    if ($m->key == 'yemen_warehouse') {
                        $meta = $m->value;
                    }
                }
                $now = \Carbon\Carbon::now();
                $today = $now->today()->toDateString();
                $weight = 0;
                if ($tran->details) {

                    $weight +=   $tran->details->sum('container_weight');
                }
                // $arr = ['Buyer Name' => $user->user_first_name . ' ' . $user->last_name, "Batch Number" => $tran->batch_number, 'Container weight' => $tran->details->sum('container_weight'), 'Date & Time' => $tran->created_at->format('Y:m:d H:i:s')];
                foreach ($tran->details as $detail) {
                    $detailsString .= (string)($detail->container_number . ':' . $detail->container_weight) . ',';
                    $arr = [
                        $centerName, $managerName,
                        $tran->batch_number, $detail->container_number, $weight, $tran->local_created_at, $meta
    
                    ];
                }
                // return $arr

                fputcsv($file, $arr);
            }
        }
        fclose($file);
        return Response::Download($name);
    }
}
