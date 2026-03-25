<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseReceiptItems extends Model
{
    protected $table = 'purchase_receipt_items';

    protected $fillable = [
        'purchase_receipt_id',
        'product_id',
        'qty',
        'unit_id',
        'conversion_qty',
        'cost_price',
        'discount_amount',
        'tax_amount',
        'subtotal',
    ];

    public $timestamps = true;

    protected $casts = [
        'qty' => 'decimal:2',
        'conversion_qty' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function purchaseReceipt()
    {
        return $this->belongsTo(PurchaseReceipts::class, 'purchase_receipt_id');
    }

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }

    public function unit()
    {
        return $this->belongsTo(Units::class, 'unit_id');
    }
}
