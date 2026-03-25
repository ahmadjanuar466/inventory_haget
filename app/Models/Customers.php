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
    // public function customertype()
    // {
    //     return $this->hasMany(CustomerType::class);
    // }
}
