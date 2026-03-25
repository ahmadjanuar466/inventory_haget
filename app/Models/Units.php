<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Units extends Model
{
    //
    protected $fillable = ['code', 'name'];
    public $timestamps = true;

    public function products()
    {
        return $this->belongsTo(Products::class);
    }
}
