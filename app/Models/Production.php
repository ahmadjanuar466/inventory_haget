<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    //
    protected $fillable = [
        'production_no',
        'production_date',
        'warehouse_id',
        'notes',
        'created_by',
    ];
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
