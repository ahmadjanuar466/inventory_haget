<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseReceipts extends Model
{
    protected $table = 'purchase_receipts';

    protected $fillable = [
        'receipt_no',
        'receipt_date',
        'supplier_id',
        'warehouse_id',
        'invoice_no',
        'status',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'total_amount',
        'notes',
        'created_by',
        'approved_by',
    ];

    public $timestamps = true;

    protected $casts = [
        'receipt_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Suppliers::class, 'supplier_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items()
    {
        return $this->hasMany(PurchaseReceiptItems::class, 'purchase_receipt_id');
    }
}
