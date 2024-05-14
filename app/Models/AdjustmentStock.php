<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdjustmentStock extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function warehouse()
    {
        return $this->hasOne(Warehouse::class, 'id', 'warehouse_id');
    }

    public function document_status()
    {
        return $this->hasOne(General::class, 'id', 'document_status_id');
    }

    public function type_adjustment()
    {
        return $this->hasOne(General::class, 'id', 'stock_type_id');
    }

    public function detail()
    {
        return $this->hasMany(AdjustmentStockDetail::class, 'adjustment_stock_id','id');
    }

}
