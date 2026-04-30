<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Suppliers extends Model
{
    //
    protected $fillable = [
        'code',
        'name',
        'contact_person',
        'phone',
        'address',
        'email',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public $timestamps = true;

    public function purchaseReceipts()
    {
        return $this->hasMany(PurchaseReceipts::class, 'supplier_id');
    }
}
