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

class MenuParentController extends Controller
{
    public function select(Request $request)
    {
        $query = MenuParent::select(["id", "name", "name as text"])->get();
        if($request->expectsJson() || $request->ajax()){
            return response()->json([
                'status' => true,
                'message'=> "Menu Parent successfuly access",
                'code'   => 200,
                'results'=> $query
            ], 200);
        }
    }
}
