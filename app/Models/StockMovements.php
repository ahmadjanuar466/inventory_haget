<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovements extends Model
{
    protected $table = 'stock_movements';

    protected $fillable = [
        'movement_date',
        'warehouse_id',
        'product_id',
        'reference_type',
        'reference_id',
        'movement_type',
        'qty_in',
        'qty_out',
        'qty_balance_after',
        'unit_cost',
        'notes',
        'created_by',
    ];

    public $timestamps = true;

    protected $casts = [
        'movement_date' => 'datetime',
        'qty_in' => 'decimal:2',
        'qty_out' => 'decimal:2',
        'qty_balance_after' => 'decimal:2',
        'unit_cost' => 'decimal:2',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reference()
    {
        return $this->morphTo();
    }
}
