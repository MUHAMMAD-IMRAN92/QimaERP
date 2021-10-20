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
    public function file()
    {
        return $this->belongsTo(FileSystem::class, 'picture_id', 'file_id');
    }
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
        $firstPurchase = Transaction::with('details')->where('created_by',   $userId)->first();
        if ($firstPurchase) {
            return  $firstPurchase->created_at;
        }
    }
    public function lastPurchase()
    {
        $userId = $this->user_id;
        $lastPurchase = Transaction::where('created_by',   $userId)->latest()->first();
        if ($lastPurchase) {
            return  $lastPurchase->created_at;
        }
    }
    public function special()
    {
        $userId = $this->user_id;
        $transactions = Transaction::with('details')->where(['created_by' =>   $userId, 'is_special' => 1])->where('batch_number', 'NOT LIKE', '%000%')->get();
        if ($transactions) {
            $sum = 0;
            foreach ($transactions as $transaction) {
                $sum += $transaction->details->sum('container_weight');
            }
            return  $sum;
        }
    }
    public function nonSpecial()
    {
        $userId = $this->user_id;
        $transactions = Transaction::with('details')->where(['created_by' =>   $userId, 'is_special' => 0])->where('batch_number', 'NOT LIKE', '%000%')->get();
        if ($transactions) {
            $sum = 0;
            foreach ($transactions as $transaction) {
                $sum += $transaction->details->sum('container_weight');
            }
            return  $sum;
        }
    }
    public function nonSpecialPrice()
    {
        $userId = $this->user_id;
        $transactions = Transaction::with('details')->where(['created_by' =>   $userId, 'is_special' => 0])->where('batch_number', 'NOT LIKE', '%000%')->where('sent_to', 2)->get();
        if ($transactions) {
            $totalWeight = 0;
            $totalPrice = 0;
            foreach ($transactions as $transaction) {
                $weight = $transaction->details->sum('container_weight');
                $price = 0;
                $farmer_code = Str::beforeLast($transaction->batch_number, '-');

                $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();
                if ($farmerPrice) {
                    $farmerPrice =  $farmerPrice->price_per_kg;
                }
                if (!$farmerPrice) {
                    $village_code = Str::beforeLast($farmer_code, '-');
                    $price = Village::where('village_code',  $village_code)->first();
                    if ($price) {
                        $price =  $price->price_per_kg;
                    }
                } else {
                    $price = Farmer::where('farmer_code', $farmer_code)->first();
                    if ($price) {
                        $price = $price->price_per_kg;
                    }
                }

                $totalPrice += $weight * $price;
                $totalWeight += $weight;
            }

            $this->non_special_weight =  $totalWeight;
            $this->non_special_price =  $totalPrice;
            return $this;
        }
    }
    public function specialPrice()
    {
        $userId = $this->user_id;
        $transactions = Transaction::with('details')->where(['created_by' =>   $userId, 'is_special' => 1])->where('batch_number', 'NOT LIKE', '%000%')->where('sent_to', 2)->get();
        if ($transactions) {
            $totalWeight = 0;
            $totalPrice = 0;
            foreach ($transactions as $transaction) {
                $weight = $transaction->details->sum('container_weight');
                $price = 0;
                $farmer_code = Str::beforeLast($transaction->batch_number, '-');

                $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();
                if ($farmerPrice) {
                    $farmerPrice =  $farmerPrice->price_per_kg;
                }
                if (!$farmerPrice) {
                    $village_code = Str::beforeLast($farmer_code, '-');
                    $price = Village::where('village_code',  $village_code)->first();
                    if ($price) {
                        $price =  $price->price_per_kg;
                    }
                } else {
                    $price = Farmer::where('farmer_code', $farmer_code)->first();
                    if ($price) {
                        $price =  $price->price_per_kg;
                    }
                }

                $totalPrice += $weight * $price;
                $totalWeight += $weight;
            }

            $this->special_weight =  $totalWeight;
            $this->special_price =  $totalPrice;
            return $this;
        }
    }
    public function getFarmers()
    {
        $userId = $this->user_id;
        $transactions = Transaction::where('created_by', $userId)->get();
        if ($transactions) {
            $batchNumbers = collect();
            $farmers = collect();
            foreach ($transactions as $transaction) {
                $batchNumber = $transaction->batch_number;
                $batchNumbers->push($batchNumber);
            }
            foreach ($batchNumbers as $batchNumber) {
                $farmerCode = Str::beforeLast($batchNumber, '-');
                $farmer = Farmer::where('farmer_code',  $farmerCode)->first();

                if ($farmer) {
                    $farmer->farmer_image = $farmer->getImage();

                    $farmers->push($farmer);
                }
            }
            return $farmers;
        }
        $batchNumbers = collect();
        $farmers = collect();
        foreach ($transactions as $transaction) {
            $batchNumber = $transaction->batch_number;
            $batchNumbers->push($batchNumber);
        }
        foreach ($batchNumbers as $batchNumber) {
            $farmerCode = Str::beforeLast($batchNumber, '-');
            $farmer = Farmer::where('farmer_code',  $farmerCode)->first();

            if ($farmer) {
                $farmer->farmer_image = $farmer->getImage();

                $farmers->push($farmer);
            }
        }
        return $farmers;
    }
    public function getVillages()
    {
        $userId = $this->user_id;
        $transactions = Transaction::where('created_by', $userId)->get();
        if ($transactions) {
            $batchNumbers = collect();
            $villages = collect();
            foreach ($transactions as $transaction) {
                $batchNumber = $transaction->batch_number;
                $batchNumbers->push($batchNumber);
            }
            foreach ($batchNumbers as $batchNumber) {
                $village_code = explode('-', $batchNumber)[0] . '-' . explode('-', $batchNumber)[1] . '-' . explode('-', $batchNumber)[2];
                $village = Village::where('village_code',  $village_code)->first();
                $villages->push($village);
            }
            $uniqueVillages = $villages->unique();
            return $uniqueVillages;
        }
    }
    public function VillagesResposibleFor()
    {
        $user_id = $this->user_id;
        $userVillage = BuyerVillages::where('user_id', $user_id)->get();
        $villages = [];
        foreach ($userVillage as $villageId) {
            $village = Village::find($villageId->village_id);
            array_push($villages, $village);
        }
        return $villages;
    }
    public function getTransactions()
    {
        $userId = $this->user_id;
        $transactions = Transaction::with('details')->where('created_by', $userId)->where('batch_number', 'NOT LIKE', '%000%')->where('sent_to', 2)->get();
        if ($transactions) {
            return $transactions;
        }
    }
    public function getTransactionsManager()
    {
        $userId = $this->user_id;
        $transactions = Transaction::with('details')->where('created_by', $userId)->where('sent_to', 3)->get();
        if ($transactions) {
            return $transactions;
        }
    }
    public function nonSpecialPriceManager()
    {
        $userId = $this->user_id;
        $transactions = Transaction::with('details')->where(['created_by' =>   $userId, 'is_special' => 0])->where('sent_to', 3)->get();
        if ($transactions) {
            $totalWeight = 0;
            $totalPrice = 0;
            foreach ($transactions as $transaction) {
                $weight = $transaction->details->sum('container_weight');
                $price = 0;
                $farmer_code = Str::beforeLast($transaction->batch_number, '-');

                $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();

                if ($farmerPrice) {
                    $price =  $farmerPrice->price_per_kg;
                }
                if ($price ==  null) {
                    $village_code = Str::beforeLast($farmer_code, '-');
                    $village = Village::where('village_code',  $village_code)->first();
                    if ($village) {
                        $price = $village->price_per_kg;
                    }
                }
                $totalPrice += $weight * $price;
                $totalWeight += $weight;
            }

            $this->non_special_weight =  $totalWeight;
            $this->non_special_price =  $totalPrice;
            return $this;
        }
    }
    public function specialPriceManager()
    {
        $userId = $this->user_id;
        $transactions = Transaction::with('details')->where(['created_by' =>   $userId, 'is_special' => 1])->where('sent_to', 3)->get();
        if ($transactions) {
            $totalWeight = 0;
            $totalPrice = 0;
            $arrfarmer = [];
            $arrVillage = [];
            foreach ($transactions as $transaction) {
                $weight = $transaction->details->sum('container_weight');
                $price = 0;
                $farmer_code = Str::beforeLast($transaction->batch_number, '-');

                $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();

                if ($farmerPrice) {
                    $price =  $farmerPrice->price_per_kg;
                }
                if ($price ==  null) {
                    $village_code = Str::beforeLast($farmer_code, '-');
                    $village = Village::where('village_code',  $village_code)->first();
                    if ($village) {
                        $price = $village->price_per_kg;
                    }
                }

                $totalPrice += $weight * $price;
                $totalWeight += $weight;
            }
            $this->special_weight =  $totalWeight;
            $this->special_price =  $totalPrice;
            return $this;
        }
    }
    public function firstPurchaseManager()
    {
        $userId = $this->user_id;
        $firstPurchase = Transaction::with('details')->where('created_by',   $userId)->first();
        if ($firstPurchase) {
            return  $firstPurchase->created_at;
        }
    }
    public function lastPurchaseManager()
    {
        $userId = $this->user_id;
        $lastPurchase = Transaction::where('created_by',   $userId)->latest()->first();
        if ($lastPurchase) {
            return  $lastPurchase->created_at;
        }
    }
    public function todaySpecialTransaction($date, $sent_to)
    {
        $userId = $this->user_id;
        $transactions = Transaction::with('details')->where(['created_by' =>   $userId, 'is_special' => 1])->where('sent_to', $sent_to)->whereDate('created_at', $date)->get();
        if ($transactions) {
            $totalWeight = 0;
            $totalPrice = 0;
            foreach ($transactions as $transaction) {
                $weight = $transaction->details->sum('container_weight');
                $price = 0;
                $farmer_code = Str::beforeLast($transaction->batch_number, '-');

                $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();
                if ($farmerPrice) {
                    $farmerPrice =  $farmerPrice->price_per_kg;
                }
                if (!$farmerPrice) {
                    $village_code = Str::beforeLast($farmer_code, '-');
                    $price = Village::where('village_code',  $village_code)->first();
                    if ($price) {
                        $price =  $price->price_per_kg;
                    }
                } else {
                    $price = Farmer::where('farmer_code', $farmer_code)->first();
                    if ($price) {
                        $price = $price->price_per_kg;
                    }
                }

                $totalPrice += $weight * $price;
                $totalWeight += $weight;
            }

            $this->special_weight =  $totalWeight;
            $this->special_price =  $totalPrice;
            return $this;
        }
    }
    public function todayNonSpecialTransaction($date, $sent_to)
    {
        $userId = $this->user_id;
        if ($sent_to == 2) {
            $transactions = Transaction::with('details')->where(['created_by' =>   $userId, 'is_special' => 0])->where('sent_to', $sent_to)->whereDate('created_at', $date)->get();
        } else {

            $transactions = Transaction::with('details')->where(['created_by' =>   $userId, 'is_special' => 0])->where('sent_to', $sent_to)->whereDate('created_at', $date)->get();
        }
        if ($transactions) {
            $totalWeight = 0;
            $totalPrice = 0;
            foreach ($transactions as $transaction) {
                $weight = $transaction->details->sum('container_weight');
                $price = 0;
                $farmer_code = Str::beforeLast($transaction->batch_number, '-');

                $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();
                if ($farmerPrice) {
                    $farmerPrice =  $farmerPrice->price_per_kg;
                }
                if (!$farmerPrice) {
                    $village_code = Str::beforeLast($farmer_code, '-');
                    $price = Village::where('village_code',  $village_code)->first();
                    if ($price) {
                        $price =  $price->price_per_kg;
                    }
                } else {
                    $price = Farmer::where('farmer_code', $farmer_code)->first();
                    if ($price) {
                        $price = $price->price_per_kg;
                    }
                }

                $totalPrice += $weight * $price;
                $totalWeight += $weight;
            }

            $this->non_special_weight =  $totalWeight;
            $this->non_special_price =  $totalPrice;
            return $this;
        }
    }
    public function lastMonthNonSpecialTransaction($month, $year, $sent_to)
    {
        $userId = $this->user_id;
        $transactions = Transaction::with('details')->where(['created_by' =>   $userId, 'is_special' => 0])->whereMonth('created_at', $month)->whereYear('created_at',  $year)->where('sent_to', $sent_to)->get();
        if ($transactions) {
            $totalWeight = 0;
            $totalPrice = 0;
            foreach ($transactions as $transaction) {
                $weight = $transaction->details->sum('container_weight');
                $price = 0;
                $farmer_code = Str::beforeLast($transaction->batch_number, '-');

                $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();
                if ($farmerPrice) {
                    $farmerPrice =  $farmerPrice->price_per_kg;
                }
                if (!$farmerPrice) {
                    $village_code = Str::beforeLast($farmer_code, '-');
                    $price = Village::where('village_code',  $village_code)->first();
                    if ($price) {
                        $price =  $price->price_per_kg;
                    }
                } else {
                    $price = Farmer::where('farmer_code', $farmer_code)->first();
                    if ($price) {
                        $price = $price->price_per_kg;
                    }
                }

                $totalPrice += $weight * $price;
                $totalWeight += $weight;
            }

            $this->non_special_weight =  $totalWeight;
            $this->non_special_price =  $totalPrice;
            return $this;
        }
    }
    public function lastMonthSpecialTransaction($month, $year, $sent_to)
    {
        $userId = $this->user_id;
        $transactions = Transaction::with('details')->where(['created_by' =>   $userId, 'is_special' => 1])->whereMonth('created_at', $month)->whereYear('created_at',  $year)->where('sent_to', $sent_to)->get();
        if ($transactions) {
            $totalWeight = 0;
            $totalPrice = 0;
            foreach ($transactions as $transaction) {
                $weight = $transaction->details->sum('container_weight');
                $price = 0;
                $farmer_code = Str::beforeLast($transaction->batch_number, '-');

                $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();
                if ($farmerPrice) {
                    $farmerPrice =  $farmerPrice->price_per_kg;
                }
                if (!$farmerPrice) {
                    $village_code = Str::beforeLast($farmer_code, '-');
                    $price = Village::where('village_code',  $village_code)->first();
                    if ($price) {
                        $price =  $price->price_per_kg;
                    }
                } else {
                    $price = Farmer::where('farmer_code', $farmer_code)->first();
                    if ($price) {
                        $price = $price->price_per_kg;
                    }
                }

                $totalPrice += $weight * $price;
                $totalWeight += $weight;
            }

            $this->special_weight =  $totalWeight;
            $this->special_price =  $totalPrice;
            return $this;
        }
    }
    public function yearSpecialTransaction($year, $sent_to)
    {
        $userId = $this->user_id;
        $transactions = Transaction::with('details')->where(['created_by' =>   $userId, 'is_special' => 1])->whereYear('created_at', $year)->where('sent_to', $sent_to)->get();
        if ($transactions) {
            $totalWeight = 0;
            $totalPrice = 0;
            foreach ($transactions as $transaction) {
                $weight = $transaction->details->sum('container_weight');
                $price = 0;
                $farmer_code = Str::beforeLast($transaction->batch_number, '-');

                $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();
                if ($farmerPrice) {
                    $farmerPrice =  $farmerPrice->price_per_kg;
                }
                if (!$farmerPrice) {
                    $village_code = Str::beforeLast($farmer_code, '-');
                    $price = Village::where('village_code',  $village_code)->first();
                    if ($price) {
                        $price =  $price->price_per_kg;
                    }
                } else {
                    $price = Farmer::where('farmer_code', $farmer_code)->first();
                    if ($price) {
                        $price = $price->price_per_kg;
                    }
                }

                $totalPrice += $weight * $price;
                $totalWeight += $weight;
            }
            $this->special_weight =  $totalWeight;
            $this->special_price =  $totalPrice;
            return $this;
        }
    }
    public function yearNonSpecialTransaction($year, $sent_to)
    {
        $userId = $this->user_id;
        $transactions = Transaction::with('details')->where(['created_by' =>   $userId, 'is_special' => 0])->whereYear('created_at', $year)->where('sent_to', $sent_to)->get();
        if ($transactions) {
            $totalWeight = 0;
            $totalPrice = 0;
            foreach ($transactions as $transaction) {
                $weight = $transaction->details->sum('container_weight');
                $price = 0;
                $farmer_code = Str::beforeLast($transaction->batch_number, '-');

                $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();
                if ($farmerPrice) {
                    $farmerPrice =  $farmerPrice->price_per_kg;
                }
                if (!$farmerPrice) {
                    $village_code = Str::beforeLast($farmer_code, '-');
                    $price = Village::where('village_code',  $village_code)->first();
                    if ($price) {
                        $price =  $price->price_per_kg;
                    }
                } else {
                    $price = Farmer::where('farmer_code', $farmer_code)->first();
                    if ($price) {
                        $price = $price->price_per_kg;
                    }
                }

                $totalPrice += $weight * $price;
                $totalWeight += $weight;
            }

            $this->non_special_weight =  $totalWeight;
            $this->non_special_price =  $totalPrice;
            return $this;
        }
    }
    public function betweenSpecialTransaction($start, $end, $sent_to)
    {
        $userId = $this->user_id;
        if ($sent_to == 2) {

            $transactions = Transaction::with('details')->where(['created_by' =>   $userId, 'is_special' => 1])->whereBetween('created_at', [$start, $end])->where('sent_to', $sent_to)->get();
        } else {

            $transactions = Transaction::with('details')->where(['created_by' =>   $userId, 'is_special' => 1])->whereBetween('created_at', [$start, $end])->where('sent_to', $sent_to)->get();
        }
        if ($transactions) {
            $totalWeight = 0;
            $totalPrice = 0;
            foreach ($transactions as $transaction) {
                $weight = $transaction->details->sum('container_weight');
                $price = 0;
                $farmer_code = Str::beforeLast($transaction->batch_number, '-');

                $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();
                if ($farmerPrice) {
                    $farmerPrice =  $farmerPrice->price_per_kg;
                }
                if (!$farmerPrice) {
                    $village_code = Str::beforeLast($farmer_code, '-');
                    $price = Village::where('village_code',  $village_code)->first();
                    if ($price) {
                        $price =  $price->price_per_kg;
                    }
                } else {
                    $price = Farmer::where('farmer_code', $farmer_code)->first();
                    if ($price) {
                        $price = $price->price_per_kg;
                    }
                }

                $totalPrice += $weight * $price;
                $totalWeight += $weight;
            }

            $this->special_weight =  $totalWeight;
            $this->special_price =  $totalPrice;
            return $this;
        }
    }
    public function betweenNonSpecialTransaction($start, $end, $sent_to)
    {
        $userId = $this->user_id;
        if ($sent_to == 2) {

            $transactions = Transaction::with('details')->where(['created_by' =>   $userId, 'is_special' => 0])->whereBetween('created_at', [$start, $end])->where('sent_to', $sent_to)->get();
        } else {

            $transactions = Transaction::with('details')->where(['created_by' =>   $userId, 'is_special' => 0])->whereBetween('created_at', [$start, $end])->where('sent_to', $sent_to)->get();
        }
        if ($transactions) {
            $totalWeight = 0;
            $totalPrice = 0;
            foreach ($transactions as $transaction) {
                $weight = $transaction->details->sum('container_weight');
                $price = 0;
                $farmer_code = Str::beforeLast($transaction->batch_number, '-');

                $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();
                if ($farmerPrice) {
                    $farmerPrice =  $farmerPrice->price_per_kg;
                }
                if (!$farmerPrice) {
                    $village_code = Str::beforeLast($farmer_code, '-');
                    $price = Village::where('village_code',  $village_code)->first();
                    if ($price) {
                        $price =  $price->price_per_kg;
                    }
                } else {
                    $price = Farmer::where('farmer_code', $farmer_code)->first();
                    if ($price) {
                        $price = $price->price_per_kg;
                    }
                }

                $totalPrice += $weight * $price;
                $totalWeight += $weight;
            }

            $this->non_special_weight =  $totalWeight;
            $this->non_special_price =  $totalPrice;
            return $this;
        }
    }
}
