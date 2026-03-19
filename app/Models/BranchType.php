<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchType extends Model
{
    //
    protected $fillable = ['name'];
    public $timestamps = true;

    public function branch()
    {
        return $this->hasMany(Branches::class);
    }
}
