<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function product_category()
    {
        return $this->belongsTo(General::class, 'product_category_id','id');
    }

    public function product_unit()
    {
        return $this->belongsTo(General::class, 'unit_id','id');
    }

    public function product_machine()
    {
        return $this->belongsTo(General::class, 'machine_id','id');
    }
}
