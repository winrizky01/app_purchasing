<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MRHistory extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = "mr_histories";

    public function detail(){
        return $this->hasMany(MRDHistory::class, 'mr_history_id','id');
    }

    public function material_type(){
        return $this->hasOne(General::class, 'id','type_material_request');
    }

    public function remark()
    {
        return $this->hasOne(General::class, 'id', 'remark_id');
    }

    public function revisiedBy(){
        return $this->hasOne(User::class, 'id', 'revisied_by');
    }
}
