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
        $transactions = Transaction::with('details')->where('sent_to', 2)->whereDate('created_at', $request->date->toDateString())->where('batch_number', 'NOT LIKE', '%000%')->get()->groupBy('created_by');
        $name = "public/reports/" . uniqid(rand(), true)  . ".csv";
        $file = fopen($name, "w");

        fputcsv($file, ['Buyer Name', 'Batch Number', 'Container weight', 'Date & Time']);
        foreach ($transactions as $key => $transaction) {
            $user = User::find($key);
            foreach ($transaction as $tran) {
                $arr = ['Buyer Name' => $user->user_first_name . ' ' . $user->last_name, "Batch Number" => $tran->batch_number, 'Container weight' => $tran->details->sum('container_weight'), 'Date & Time' => $tran->created_at->format('Y:m:d H:i:s')];
                fputcsv($file, $arr);
            }
        }

        fclose($file);
        return Response::Download($name);
    }
}
