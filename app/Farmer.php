<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Farmer extends Model {

    protected $casts = [
        'is_local' => 'boolean',
    ];
    protected $primaryKey = 'farmer_id';
    protected $fillable = ['farmer_code', 'farmer_name', 'governerate_code', 'region_code', 'village_code', 'picture_id', 'idcard_picture_id', 'is_status'];

    public function governerate() {
        return $this->belongsTo(Governerate::class, 'governerate_code', 'governerate_code');
    }

    public function region() {
        return $this->belongsTo(Region::class, 'region_code', 'region_code');
    }

    public function village() {
        return $this->belongsTo(Village::class, 'village_code', 'village_code');
    }

    public function profileImage() {
        return $this->belongsTo(FileSystem::class, 'picture_id', 'file_id');
    }

    public function idcardImage() {
        return $this->belongsTo(FileSystem::class, 'idcard_picture_id', 'file_id');
    }

}
