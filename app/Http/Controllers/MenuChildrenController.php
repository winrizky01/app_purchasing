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

class MenuChildrenController extends Controller
{
    public function select(Request $request)
    {
        $query = MenuChildren::select(["id", "name", "name as text"]);
        if($request->parent_id != ""){
            $query = $query->where("menu_parent_id", $request->parent_id);
        }
        $query = $query->get();
        if($request->expectsJson() || $request->ajax()){
            return response()->json([
                'status' => true,
                'message'=> "Menu Children successfuly access",
                'code'   => 200,
                'results'=> $query
            ], 200);
        }
    }

}
