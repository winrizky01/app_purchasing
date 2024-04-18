<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Menu extends Model{
    use HasFactory;
    
    public static function menuUser($id){
        $user = DB::table("users")->where("id",$id)->first();
        $role = DB::table("roles")->where("id",$user->role)->first();
        $roleDetail = DB::table("role_details")->where("role_id",$role->id)->where("deleted_at",NULL)->where("status","active")->get();
        
        $menu_parents = array();
    
        foreach($roleDetail as $role){
            $parent      = DB::table("menu_parents")->where("status","active")->find($role->menu_parent_id);
            $children    = DB::table("menu_childrens")->where("status","active")->find($role->menu_children_id);
            $subchildren = DB::table("menu_sub_childrens")->where("status","active")->find($role->menu_sub_children_id);
    
            if($parent){
                // Check if parent already exists in menu_parents array
                $parentExists = false;
                $parentKey = null;
                foreach ($menu_parents as $key => $menu) {
                    if ($menu['parent']->id == $parent->id) {
                        $parentExists = true;
                        $parentKey = $key;
                        break;
                    }
                }
    
                // If parent doesn't exist, add it to menu_parents array
                if (!$parentExists) {
                    $menu_parents[] = array(
                        'parent' => $parent,
                        'children' => array(),
                    );
                    $parentKey = count($menu_parents) - 1;
                }
    
                if($children){
                    $menu_parents[$parentKey]['children'][] = array(
                        'child' => $children,
                        'subchildren' => array(),
                    );
                    $childKey = count($menu_parents[$parentKey]['children']) - 1;
        
                    if($subchildren){
                        $menu_parents[$parentKey]['children'][$childKey]['subchildren'][] = $subchildren;
                    }
                }
            }
        }
    
        // Sort menu_parents array based on parent position
        usort($menu_parents, function($a, $b) {
            return $a['parent']->position - $b['parent']->position;
        });
        
        return $menu_parents;
    }
    
}