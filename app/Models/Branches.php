<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branches extends Model
{
    //
    protected $fillable = ['code',
    'name','type','address','phone',
    'is_active'];
}
