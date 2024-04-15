<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoleDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function parent()
    {
        return $this->belongsTo(MenuParent::class, "menu_parent_id", "id");
    }
    public function children()
    {
        return $this->hasMany(MenuChildren::class, 'id','menu_children_id');
    }
    public function subchildren()
    {
        return $this->hasMany(MenuSubChildren::class, 'id','menu_sub_children_id');
    }

}
