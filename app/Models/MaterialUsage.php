<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialUsage extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function detail()
    {
        return $this->hasMany(MaterialUsageDetail::class, 'material_usage_id','id');
    }

    public function warehouse()
    {
        return $this->hasOne(Warehouse::class, 'id','warehouse_id');
    }
    
    public function document_status()
    {
        return $this->hasOne(General::class, 'id', 'document_status_id');
    }

}
