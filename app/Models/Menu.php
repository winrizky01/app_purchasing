<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Menu extends Model{
    use HasFactory;

    // public static function menuUser($id){
    //     $user = DB::table("users")->where("id",$id)->first();
    //     $role = DB::table("roles")->where("id",$user->role)->first();
    //     $roleDetail = DB::table("role_details")->where("role_id",$role->id)->get();
    //     $menu_parents       = array();
    //     $menu_childrens     = array();
    //     $menu_subchildrens  = array();

    //     $tamp_parent_id = array();
    //     $tamp_child_id  = array();
    //     $tamp_subchild_id = array();
    //     foreach($roleDetail as $role){
    //         $parent      = DB::table("menu_parents")->where("status","active")->orderBy("position","ASC")->find($role->menu_parent_id);
    //         $children    = DB::table("menu_childrens")->where("status","active")->orderBy("position","ASC")->find($role->menu_children_id);
    //         $subchildren = DB::table("menu_sub_childrens")->where("status","active")->orderBy("position","ASC")->find($role->menu_sub_children_id);

    //         if($parent){
    //             if(in_array($parent->id, $tamp_parent_id) == false){
    //                 array_push($menu_parents, $parent);
    //                 array_push($tamp_parent_id, $parent->id);
    //             }
    
    //             if($children){
    //                 if(in_array($children->id, $tamp_child_id) == false){
    //                     array_push($menu_childrens, $children);
    //                     array_push($tamp_child_id, $children->id);
    //                 }
        
    //                 if($subchildren){
    //                     if(in_array($subchildren->id, $tamp_subchild_id) == false){
    //                         array_push($menu_subchildrens, $subchildren);
    //                         array_push($tamp_subchild_id, $subchildren->id);
    //                     }
    //                 }
    //             }
    //         }
    //     }

    //     return array("menu_parents"=>$menu_parents, "menu_childs"=>$menu_childrens, "menu_subchilds"=>$menu_subchildrens);
    // }
    public static function menuUser($id){
        $user = DB::table("users")->where("id",$id)->first();
        $role = DB::table("roles")->where("id",$user->role)->first();
        $roleDetail = DB::table("role_details")->where("role_id",$role->id)->get();
        
        $menu_parents       = array();
    
        foreach($roleDetail as $role){
            $parent      = DB::table("menu_parents")->where("status","active")->orderBy("position","ASC")->find($role->menu_parent_id);
            $children    = DB::table("menu_childrens")->where("status","active")->orderBy("position","ASC")->find($role->menu_children_id);
            $subchildren = DB::table("menu_sub_childrens")->where("status","active")->orderBy("position","ASC")->find($role->menu_sub_children_id);
    
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
    
        return $menu_parents;
    }
    
}