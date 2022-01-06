<?php

namespace App\Http\Controllers;

use App\CropsterReport;
use App\FileSystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CropsterReportController extends Controller
{
    public function index(Request $request)
    {
        // return $request->all();
        if ($request->cropster_report) {
            $file = $request->cropster_report;
            $file_name = time() . '.' . $file->getClientOriginalExtension();
            $path = $request->file('cropster_report')->storeAs('cropsterReports', $file_name, 's3');
            Storage::disk('s3')->setVisibility($path, 'public');
            $url = Storage::disk('s3')->url('cropsterReports/' . $file_name);
            CropsterReport::create([
                'entity_id' => $request->entity_id,
                'entity_type' => $request->entity_type,
                'file_url' => $url,

            ]);
            return redirect()->back()->with('msg', 'Report Uploaded Successfully!');
        } else {
            return redirect()->back()->with('dmsg', 'Please select atleast any file.');
        }
    }
}
