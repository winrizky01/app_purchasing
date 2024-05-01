<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MRDHistory extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = "mr_detail_histories";

    public function product()
    {
        return $this->hasOne(Product::class, 'id','product_id');
    }

}
