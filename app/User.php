<?php

namespace App;

use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{

    use Notifiable, HasRoles, SoftDeletes, HasApiTokens;

    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'picture_id', 'table_id', 'table_name'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getIdAttribute()
    {
        return $this->user_id;
    }

    public function center_user()
    {
        return $this->hasOne(CenterUser::class, 'user_id', 'user_id');
    }
    public function getImage()
    {
        $imageName = null;

        if ($file = FileSystem::where('file_id', $this->picture_id)->first()) {
            $imageName = $file->user_file_name;
        }

        return $imageName;
    }
    public function firstPurchase()
    {
        $userId = $this->user_id;
        $firstPurchase = Transaction::with('details')->where('created_by',   $userId)->first()['created_at'];
        return  $firstPurchase;
    }
    public function lastPurchase()
    {
        $userId = $this->user_id;
        $lastPurchase = Transaction::where('created_by',   $userId)->latest()->first()['created_at'];
        return  $lastPurchase;
    }
    public function special()
    {
        $userId = $this->user_id;
        $transactions = Transaction::with('details')->where(['created_by' =>   $userId, 'is_special' => 1])->get();
        $sum = 0;
        foreach ($transactions as $transaction) {
            $sum += $transaction->details->sum('container_weight');
        }
        return  $sum;
    }
    public function nonSpecial()
    {
        $userId = $this->user_id;
        $transactions = Transaction::with('details')->where(['created_by' =>   $userId, 'is_special' => 0])->get();
        $sum = 0;
        foreach ($transactions as $transaction) {
            $sum += $transaction->details->sum('container_weight');
        }
        return  $sum;
    }
    public function nonSpecialPrice()
    {
        $userId = $this->user_id;
        $transactions = Transaction::with('details')->where(['created_by' =>   $userId, 'is_special' => 0])->get();
        $totalWeight = 0;
        $totalPrice = 0;
        foreach ($transactions as $transaction) {
            $weight = $transaction->details->sum('container_weight');
            $price = 0;
            $farmer_code = Str::beforeLast($transaction->batch_number, '-');
           
            $farmerPrice = optional(Farmer::where('farmer_code', $farmer_code)->first())->price_per_kg; 
            if (!$farmerPrice) {
                $village_code = Str::beforeLast($farmer_code, '-');
                $price = Village::where('village_code',  $village_code)->first()->price_per_kg;

              
            } else {
                $price = Farmer::where('farmer_code', $farmer_code)->first()->price_per_kg;
            }

            $totalPrice += $weight * $price;
            $totalWeight += $weight;
        }
        
        $this->non_special_weight =  $totalWeight ;
        $this->non_special_price =  $totalPrice;
        return $this;
    }
    public function specialPrice()
    {
        $userId = $this->user_id;
        $transactions = Transaction::with('details')->where(['created_by' =>   $userId, 'is_special' => 1])->get();
        $totalWeight = 0;
        $totalPrice = 0;
        foreach ($transactions as $transaction) {
            $weight = $transaction->details->sum('container_weight');
            $price = 0;
            $farmer_code = Str::beforeLast($transaction->batch_number, '-');
           
            $farmerPrice = optional(Farmer::where('farmer_code', $farmer_code)->first())->price_per_kg; 
            if (!$farmerPrice) {
                $village_code = Str::beforeLast($farmer_code, '-');
                $price = Village::where('village_code',  $village_code)->first()->price_per_kg;

              
            } else {
                $price = Farmer::where('farmer_code', $farmer_code)->first()->price_per_kg;
            }

            $totalPrice += $weight * $price;
            $totalWeight += $weight;
        }
        
        $this->special_weight =  $totalWeight ;
        $this->special_price =  $totalPrice;
        return $this;
    }
    public function getFarmers()
    {
        $userId = $this->user_id;
        $transactions = Transaction::where('created_by', $userId)->get();
        $batchNumbers = collect();
        $farmers = collect();
        foreach ($transactions as $transaction) {
            $batchNumber = $transaction->batch_number;
            $batchNumbers->push($batchNumber);
        }
        foreach ($batchNumbers as $batchNumber) {
            $farmerCode = Str::beforeLast($batchNumber, '-');
            $farmer = Farmer::where('farmer_code',  $farmerCode)->first();
            $farmers->push($farmer);
        }
        return $farmers;
    }
    public function getRegions()
    {
        $userId = $this->user_id;
        $transactions = Transaction::where('created_by', $userId)->get();
        $batchNumbers = collect();
        $villages = collect();
        foreach ($transactions as $transaction) {
            $batchNumber = $transaction->batch_number;
            $batchNumbers->push($batchNumber);
        }
        foreach ($batchNumbers as $batchNumber) {
            $village_code = explode('-', $batchNumber)[0] . '-' . explode('-', $batchNumber)[1]. '-' . explode('-', $batchNumber)[2];
            $village = Village::where('village_code',  $village_code)->first();
            $villages->push($village);
        }
        $uniqueVillages = $villages->unique();
        return $uniqueVillages;
    }
    public function getTransactions()
    {
        $userId = $this->user_id;
        $transactions = Transaction::where('created_by', $userId)->get();
      return $transactions;
        
    }
}
