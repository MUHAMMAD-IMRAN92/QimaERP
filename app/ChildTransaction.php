<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChildTransaction extends Model
{
    protected $primaryKey = 'child_transactions_id';
    protected $fillable = ['child_transactions_id', 'parent_transaction_id', 'transaction_id', 'batch_number', 'is_parent', 'created_by', 'is_local', 'local_code', 'is_mixed', 'transaction_type', 'reference_id', 'transaction_status', 'is_server_id', 'is_new', 'sent_to', 'is_sent', 'session_no', 'local_created_at', 'is_in_process', 'is_update_center', 'local_session_no', 'mill_id'];
    protected $casts = [
        'is_local' => 'boolean',
        'is_mixed' => 'boolean',
        'is_new' => 'boolean',
        'is_server_id' => 'boolean',
        'is_sent' => 'boolean',
        'is_in_process' => 'boolean',
        'is_update_center' => 'boolean',
    ];
}
