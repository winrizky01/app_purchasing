<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function payment_method()
    {
        return $this->belongsTo(General::class, 'payment_method_id','id');
    }

    public function bank_account()
    {
        return $this->belongsTo(General::class, 'bank_account_id','id');
    }

}
