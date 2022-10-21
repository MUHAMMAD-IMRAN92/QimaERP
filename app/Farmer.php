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
    function Fregion()
    {
        // return $this->hasOne();
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
        $transactions = Transaction::with('details')->where('batch_number', 'LIKE', "$farmerCode-%")
            ->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')
            ->get();
        if ($transactions) {
            $sum = 0;
            foreach ($transactions as $transaction) {
                $sum += $transaction->details->sum('container_weight');
            }
            return $sum;
        } else {
            return $sum = 0;
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
        $farmerCode = $this->farmer_code;
        $transactions = Transaction::with('details')->where('batch_number', 'LIKE', "$farmerCode%")->where('sent_to', 2)->get();
        $this->transactions = $transactions;
        return $this;
    }

    public function farmerInvoice()
    {
        $farmerCode = $this->farmer_code;
        $transaction = Transaction::where('sent_to', 2)->where('batch_number', 'LIKE',   '%' . $farmerCode . '%')->first();
        if ($transaction) {
            $transInvoice = TransactionInvoice::where('transaction_id', $transaction->transaction_id)->first();
            if ($transInvoice) {
                $inovice = $transInvoice->invoice_id;
                if ($file = FileSystem::where('file_id', $inovice)->first()) {
                    $inovice = $file->user_file_name;
                }
                return $inovice;
            }
        } else {
            return null;
        }
    }
    public function paidPriceFromInvoice()
    {
        $farmerCode = $this->farmer_code;
        $paidPrice = 0;
        $transactions = Transaction::where('sent_to', 2)->where('batch_number', 'LIKE',   '%' . $farmerCode . '-%')->get();
        foreach ($transactions as $transaction) {
            $transInvoices = TransactionInvoice::where('transaction_id', $transaction->transaction_id)->get();
            foreach ($transInvoices as  $transInvoice) {
                $paidPrice += $transInvoice->invoice_price;
            }
        }

        return $paidPrice;
    }
    public function cropsterReports()
    {
        $farmerId = $this->farmer_id;
        $urls =   CropsterReport::where('entity_type', 1)->where('entity_id', $farmerId)->orderByDesc('created_at')->get();
        return $urls;
    }

    protected $appends = ['village_name'];
    public function getVillageNameAttribute()
    {
        $villageCode  =  Str::beforeLast($this->farmer_code, '-');
        $village = Village::where('village_code', $villageCode)->first();
        if ($village) {
            return $village->village_title_ar;
        }
    }
}
