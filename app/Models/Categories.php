<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    //
    protected $fillable = ['parent_id', 'code', 'name'];
    public $timestamps = true;

    public function products()
    {
        return $this->belongsTo(Products::class);
    }
}
