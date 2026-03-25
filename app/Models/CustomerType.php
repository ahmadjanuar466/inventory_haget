<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerType extends Model
{
    //
    protected $fillable = ['name'];
    public $timestamps = true;

    public function customer()
    {
        return $this->belongsTo(Customers::class);
    }
}
