<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPrice extends Model
{
    //
    protected $fillable = ['product_id', 'price', 'is_active'];

    protected $casts = [
        'product_id' => 'integer',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public $timestamps = true;

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }
}
