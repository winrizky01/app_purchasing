<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function material_request_details()
    {
        return $this->hasMany(MaterialRequestDetail::class, 'material_request_id','id');
    }
}
