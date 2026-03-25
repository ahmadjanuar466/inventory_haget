<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stocks extends Model
{
    protected $fillable = [
        'warehouse_id',
        'product_id',
        'qty_on_hand',
        'qty_reserved',
        'qty_available',
        'last_movement_at',
    ];

    public $timestamps = true;

    protected $casts = [
        'qty_on_hand' => 'decimal:2',
        'qty_reserved' => 'decimal:2',
        'qty_available' => 'decimal:2',
        'last_movement_at' => 'datetime',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }
}
