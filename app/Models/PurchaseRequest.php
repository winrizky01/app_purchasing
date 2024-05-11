<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function purchaserequestdetails()
    {
        return $this->hasMany(PurchaseRequestDetail::class, 'id','purchase_request_id');
    }

    public function department()
    {
        return $this->hasOne(Department::class, 'id', 'department_id');
    }

    public function document_status()
    {
        return $this->hasOne(General::class, 'id', 'document_status_id');
    }

    public function createdBy(){
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function last_update(){
        return $this->hasOne(User::class, 'id', 'updated_by');
    }
}