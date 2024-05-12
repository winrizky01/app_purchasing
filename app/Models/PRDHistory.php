<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PRDHistory extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = "pr_detail_histories";

    public function product()
    {
        return $this->hasOne(Product::class, 'id','product_id');
    }

}
