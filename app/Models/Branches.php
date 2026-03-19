<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branches extends Model
{
    //
    protected $fillable = [
        'code',
        'name',
        'branch_type_id',
        'address',
        'phone',
        'is_active'
    ];
    public $timestamps = true;

    public function warehouse()
    {
        return $this->hasMany(Warehouse::class);
    }
    public function branchtype()
    {
        return $this->belongsTo(BranchType::class, 'branch_type_id');
    }
}
