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

    public function department()
    {
        return $this->hasOne(Department::class, 'id', 'department_id');
    }

    public function division()
    {
        return $this->hasOne(Division::class, 'id', 'division_id');
    }

    public function document_status()
    {
        return $this->hasOne(General::class, 'id', 'document_status_id');
    }
}
