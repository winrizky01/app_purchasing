<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Approval extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function type_transaction()
    {
        return $this->hasOne(General::class, 'type_transaction_id','id');
    }

    public function status()
    {
        return $this->hasOne(General::class, 'status_id','id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'user_id','id');
    }
}
