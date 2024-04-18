<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductMachine extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function machine()
    {
        return $this->hasOne(General::class, 'id','machine_id');
    }

}
