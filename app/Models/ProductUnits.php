<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductUnits extends Model
{
    protected $fillable = [
        'product_id',
        'unit_id',
        'conversion_qty',
        'is_active',
        'is_base',
    ];

    public $timestamps = true;

    protected $casts = [
        'conversion_qty' => 'decimal:2',
        'is_active' => 'integer',
        'is_base' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }

    public function unit()
    {
        return $this->belongsTo(Units::class, 'unit_id');
    }
}
