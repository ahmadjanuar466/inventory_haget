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

    public $timestamps = true;
}
