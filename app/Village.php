<?php

namespace App;

use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Model;

class Village extends Model
{

    protected $casts = [
        'is_local' => 'boolean',
    ];
    protected $primaryKey = 'village_id';
    protected $fillable = ['village_code', 'village_title', 'created_by', 'is_local', 'local_code', 'local_system_code', 'village_title_ar'];
    public function gov_region()
    {
        $village_code = $this->village_code;
        $region_code = explode('-', $village_code)[0] . '-' .  explode('-', $village_code)[1];
        $region = Region::where('region_code',  $region_code)->first()['region_title'];
        $gov_code = explode('-', $village_code)[0];
        $governerate = Governerate::where('governerate_code',  $gov_code)->first()['governerate_title'];
        $farmers = Farmer::with('file')->where('farmer_code', 'LIKE', $village_code . '%')->get();
        $createdBys = Transaction::where('batch_number', 'LIKE', $village_code . '%')->where('sent_to', 2)->pluck('created_by');
        // $role = Role::with(['users' => function ($query) use ($createdBys) {
        //     $query->whereIn('user_id', $createdBys->toArray());
        // }, 'users.file'])->where('name', 'Coffee Buyer')->first();
        $users = User::whereIn('user_id', $createdBys)->get();

        $this->buyers = $users;
        $this->region = $region;
        $this->governrate =  $governerate;
        $this->farmers = $farmers;
        return $this;
    }
}
