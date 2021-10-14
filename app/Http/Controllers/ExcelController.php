<?php

namespace App\Http\Controllers;

use App\Imports\FarmersImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    public function importExportView()
    {
        return view('import');
    }
    public function import()
    {
        Excel::import(new FarmersImport, request()->file('file'));

        return back();
    }
}
