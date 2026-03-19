<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    //
    protected $fillable = [
        'branch_id',
        'code',
        'name',
        'is_default',
        'is_active'
    ];
    public $timestamps = true;

    public function branches()
    {
        return $this->belongsTo(Branches::class, 'branch_id');
    }
}
