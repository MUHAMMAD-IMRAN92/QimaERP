<?php

namespace App;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Farmer extends Model
{

    use SoftDeletes;

    protected $casts = [
        'is_local' => 'boolean',
    ];
    protected $primaryKey = 'farmer_id';
    protected $guarded = [];
    // protected $fillable = ['farmer_code', 'farmer_name', 'village_code', 'picture_id', 'idcard_picture_id', 'is_status', 'created_by', 'is_local', 'local_code', 'local_system_code', 'farmer_nicn', 'center_id','deleted_at'];

    public function governerate()
    {
        return $this->belongsTo(Governerate::class, 'governerate_code', 'governerate_code');
    }
    public function file()
    {
        return $this->belongsTo(FileSystem::class, 'picture_id', 'file_id');
    }
    public function getgovernerate()
    {
        $farmer_code = $this->farmer_code;
        $governoratCode = explode('-', $this->farmer_code)[0];
        $governerate = Governerate::where('governerate_code', $governoratCode)->first(['governerate_title']);
        return $governerate;
    }


    public function getVillage()
    {
        $village_code = $this->village_code;


        $village = Village::where('village_code', $village_code)->first(['village_title']);

        return $village;
    }

    public function getRegion()
    {
        $region = $this->farmer_code;
        $regionCode = explode('-', $this->farmer_code)[0] . '-' . explode('-', $this->farmer_code)[1];

        $region = Region::where('region_code', $regionCode)->first(['region_title']);

        return $region;
    }
    public function region()
    {
        return $this->belongsTo(Region::class, 'region_code', 'region_code');
    }

    public function village()
    {
        return $this->belongsTo(Village::class, 'village_code', 'village_code');
    }

    public function profileImage()
    {
        return $this->belongsTo(FileSystem::class, 'picture_id', 'file_id');
    }

    public function idcardImage()
    {
        return $this->belongsTo(FileSystem::class, 'idcard_picture_id', 'file_id');
    }
    public function getImage()
    {
        $imageName = null;

        if ($file = FileSystem::where('file_id', $this->picture_id)->first()) {
            $imageName = $file->user_file_name;
        }
        return $imageName;
    }
    public function cnic()
    {
        $imageName = null;

        if ($file = FileSystem::where('file_id', $this->idcard_picture_id)->first()) {
            $imageName = $file->user_file_name;
        }
        return $imageName;
    }
    public function getfirstTransaction()
    {
        $farmerCode = $this->farmer_code;
        $transaction = Transaction::where('batch_number', 'LIKE', "$farmerCode%")->first();
        if ($transaction) {
            return  $transaction->created_at;
        } else {
            $transaction = null;
        }
    }
    public function getlastTransaction()
    {
        $farmerCode =  $this->farmer_code;
        $transaction = Transaction::where('batch_number', 'LIKE', "$farmerCode%")->latest()->first();
        if ($transaction) {
            return  $transaction->created_at;
        } else {
            $transaction = null;
        }
    }
    public function quntity()
    {
        $farmerCode = $this->farmer_code;
        $transactions = Transaction::with('details')->where('batch_number', 'LIKE', "$farmerCode%")
            ->where('sent_to', 2)
            ->get();
        if ($transactions) {
            $sum = 0;
            foreach ($transactions as $transaction) {
                $sum += $transaction->details->sum('container_weight');
            }
            return $sum;
        } else {
            return  $transactions = null;
        }
    }
    public function price()
    {
        $village_code = $this->village_code;

        $villagePrice = Village::where('village_code', $village_code)->first(['price_per_kg']);

        return $villagePrice;
    }
    public function transactions()
    {
        $village_code = $this->village_code;
        $transactions = Transaction::with('details')->where('batch_number', 'LIKE',  $village_code . '%')->where('sent_to', 2)->get();
        $this->transactions = $transactions;
        return $this;
    }

    public function farmerInvoice()
    {
        $farmerCode = $this->farmer_code;
    }
}
