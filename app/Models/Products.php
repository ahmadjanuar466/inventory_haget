<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    //
    protected $fillable = [
        'sku',
        'name',
        'category_id',
        'units_id',
        'track_stock',
        'has_expiry',
        'cost_price',
        'sell_price',
        'min_stock',
        'is_active'
    ];

    public $timestamps = true;

    public function units()
    {
        return $this->hasMany(Units::class, 'id');
    }
    public function categories()
    {
        return $this->hasMany(Categories::class, 'category_id');
    }

    public function stocks()
    {
        return $this->hasMany(Stocks::class, 'product_id');
    }
}
