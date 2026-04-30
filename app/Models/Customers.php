<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Customers extends Model
{
    //
    protected $fillable = [
        'code',
        'name',
        'phone',
        'email',
        'address',
        'customer_type_id',
        'is_active'
    ];

    public $timestamps = true;
    public function customerType()
    {
        return $this->belongsTo(CustomerType::class, 'customer_type_id');
    }
}
