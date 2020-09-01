<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BatchNumber extends Model {

    protected $primaryKey = 'batch_id';
    protected $fillable = ['batch_id', 'batch_number', 'is_parent', 'created_by', 'is_local', 'local_code', 'is_mixed','is_server_id','season_id','season_status'];
    protected $casts = [
          'is_local' => 'boolean',
          'is_mixed' => 'boolean',
          'is_server_id' => 'boolean',
    ];

    public function childBatchNumber() {
        return $this->hasMany(BatchNumber::class, 'is_parent', 'batch_id');
    }

    public function transaction() {
        return $this->hasMany(Transaction::class, 'batch_number', 'batch_number');
    }
    public function season() {
        return $this->hasMany(Season::class, 'season_id', 'season_id');
    }

    public function sent_transaction() {
        return $this->hasMany(Transaction::class, 'batch_number', 'batch_number');
    }

    public function center_manager_received_transaction() {
        return $this->hasMany(Transaction::class, 'batch_number', 'batch_number');
    }

    public function latestTransation() {
        return $this->hasOne(Transaction::class, 'batch_number', 'batch_number');
    }

}
