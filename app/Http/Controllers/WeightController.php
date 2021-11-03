<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Governerate;
use App\Transaction;
use App\BatchNumber;
use App\TransactionDetail;
use App\Region;
use App\Village;
use App\Farmer;
use DB;

class WeightController extends Controller
{
	public function governorweight()
	{

		$data['governor'] = Governerate::all();
		// $governerate=Governerate::select('governerate_code')->get();
		// // dd($governerate_code);



		//       $totalweight = Transaction::whereHas('transactionDetail', function($q){
		// 	    $q->where('is_parent', 0)
		// 	    ->orWhere('batch_number','LIKE',);
		// 	})->sum('container_weight');
		//       dd($totalweight);

		$data['transaction'] = Transaction::with('details')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->get();

		// $data['transaction']=Transaction::where('is_parent', '0')->get();
		return view('admin.weight.governorweight', $data);
	}

	public function governorweightcode($id)
	{
		$data['governore'] = Governerate::find($id);
		$data['region'] = Region::where('region_code', 'LIKE', $data['governore']->governerate_code . '%')->get();
		return view('admin.weight.regionweight', $data);
	}

	public function regionweightcode($id)
	{
		$data['region'] = Region::find($id);
		$data['village'] = Village::where('village_code', 'LIKE', $data['region']->region_code . '%')->get();
		return view('admin.weight.villageweight', $data);
	}

	public function villageweightcode($id)
	{
		$data['village'] = Village::find($id);
		$data['farmer'] = Farmer::where('farmer_code', 'LIKE', $data['village']->village_code . '%')->get();
		return view('admin.weight.farmerweight', $data);
	}
}
