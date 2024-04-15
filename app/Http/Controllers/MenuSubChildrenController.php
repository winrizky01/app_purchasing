<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Models\MenuParent;
use App\Models\MenuChildren;
use App\Models\MenuSubChildren;

use DB;
use Redirect;
use DataTables;
use Exception;
use Validator;

class MenuSubChildrenController extends Controller
{
    public function select(Request $request)
    {
        $query = MenuSubChildren::select(["id", "name", "name as text"]);
        if($request->children_id != ""){
            $query = $query->where("menu_children_id", $request->children_id);
        }
        $query = $query->get();
        if($request->expectsJson() || $request->ajax()){
            return response()->json([
                'status' => true,
                'message'=> "Menu Sub Children successfuly access",
                'code'   => 200,
                'results'=> $query
            ], 200);
        }
    }
}
