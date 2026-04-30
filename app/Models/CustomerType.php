<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerType extends Model
{
    //
    protected $fillable = ['name'];
    public $timestamps = true;

    public function customers()
    {
        return $this->hasMany(Customers::class, 'customer_type_id');
    }
}
