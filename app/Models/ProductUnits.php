<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductUnits extends Model
{
    //
    protected $fillable = [
        'product_id',
        'unit_id',
        'conversion_qty',
        'is_base'
    ];
    public $timestamps = true;
    public function product()
    {
        return $this->hasMany(Products::class, 'product_id', 'id');
    }
    public function unit()
    {
        return $this->hasMany(Units::class, 'unit_id', 'id');
    }
}
