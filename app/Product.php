<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

    public function scopeMarket($query)
    {
        return $query->where('for', 1);
    }

    public function scopeSorting($query)
    {
        return $query->where('for', 2);
    }

    public function scopePeaberry($query)
    {
        return $query->where('id', 8);
    }

    public function scopeGreen($query)
    {
        return $query->whereIn('id', [6, 7]);
    }

    public function details()
    {
        return $this->hasMany(TransactionDetailProduct::class, 'product_id', 'id');
    }

    public static function peaberryWithoutDefectsIds()
    {
        return self::where('for', 3)->get(['id']);
    }

    public static function greenWithoutDefectsIds()
    {
        return self::where('for', 4)->get(['id']);
    }

    public static function allDefectiveIds()
    {
        return self::where('for', 5)->get(['id']);
    }
}
