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
        'is_active',
    ];

    public $timestamps = true;

    public function branch()
    {
        return $this->belongsTo(Branches::class, 'branch_id');
    }

    public function branches()
    {
        return $this->branch();
    }

    public function stocks()
    {
        return $this->hasMany(Stocks::class, 'warehouse_id');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovements::class, 'warehouse_id');
    }

    public function purchaseReceipts()
    {
        return $this->hasMany(PurchaseReceipts::class, 'warehouse_id');
    }
}
