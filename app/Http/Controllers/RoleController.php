<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Models\Role;
use App\Models\RoleDetail;

use DB;
use Redirect;
use DataTables;
use Exception;
use Validator;

class RoleController extends Controller
{
    public function select(Request $request)
    {
        $query = Role::select(["id", "name", "name as text"])->get();
        if($request->expectsJson() || $request->ajax()){
            return response()->json([
                'status' => true,
                'message'=> "Role successfuly access",
                'code'   => 200,
                'results'=> $query
            ], 200);
        }
    }

    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            $page   = 1;
            $limit  = 0;
            $where  = [];

            if ($request->page != "") {
                $page = $request->page;
            }
            if ($request->limit != "") {
                $limit = $request->limit;
            }
            if($request->id != ""){
                $where[] = ["roles.id", $request->id];
            }

            $offset = ($page - 1) * $limit;
            
            $query = Role::with(['role'])->where($where)->orderBy("roles.id", "ASC");
            if ($limit > 0) {
                $query = $query->offset($offset)->limit($limit)->paginate($limit);
            } 
            else {
                $query = $query->get();
            }

            return response()->json([
                        'status' => true,
                        'message'=> "Role successfuly access",
                        'code'   => 200,
                        'results'=> $query
                    ], 200);
        }
        else {
            $data["title"] = "List Role";

            $view = "pages.role.index";
            return view($view, $data);
        }
    }

    public function create(Request $request)
    {
        $data["title"] = "Add Role";

        $view = "pages.role.create";
        return view($view, $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name'    => 'required',
            'status'  => 'required'
        ]);

        if($validator->fails()){
            return handleErrorResponse($request, 'The following fields are required !', 'setting/role', 404, null);
        }

        $check = Role::where("name","LIKE","%".$request->name."%")->get();
        if(count($check) > 0){
            return handleErrorResponse($request, 'Opps, this name is exist!', 'setting/role', 404, null);
        }

        DB::beginTransaction();
        try {
            $role = Role::create([
                "name"      => $request->name,
                "status"    => $request->status,
                "created_at"=> date("Y-m-d H:i:s"),
                "created_by"=> auth()->user()->id,
            ]);

            if($role){
                foreach($request->role_detail as $dtl){
                    $explode    = explode("-", $dtl);
                    $parent     = trim($explode[0]) === "" ? null : $explode[0];
                    $child      = trim($explode[1]) === "" ? null : $explode[1];
                    $subChild   = trim($explode[2]) === "" ? null : $explode[2];
                    
                    $roleDetail = RoleDetail::create([
                        "role_id"               => $id,
                        "menu_parent_id"        => $parent,
                        "menu_children_id"      => $child,
                        "menu_sub_children_id"  => $subChild,
                        "status"    => "active",
                        "created_at"=> date("Y-m-d H:i:s"),
                        "created_by"=> auth()->user()->id,
                    ]);
                }
            }
        }
        catch (Exception $e) {
            DB::rollback();
            return handleErrorResponse($request, $e->getMessage(), 'setting/role', 404, null);
        }

        DB::commit();

        if($request->expectsJson()){
            return response()->json([
                'status' => true,
                'message'=> "Data successfuly created.",
                'code'   => 200,
                'results'=> []
            ], 200);
        }
        else{
            Session::put('success','Data successfuly created.');
            return redirect()->to('setting/role');
        }
    }

    public function edit(Request $request, $id)
    {
        $role = Role::with(["detail","detail.parent","detail.children","detail.subchildren"])->find($id);
        if(!$role){
            return handleErrorResponse($request, 'Opps, data not found!', 'setting/role', 404, null);
        }

        if($request->expectsJson())
        {
            return response()->json([
                'status' => true,
                'message'=> "Data found.",
                'code'   => 200,
                'results'=> $role
            ], 200);
        }
        else{
            $data["title"] = "Edit Role";
            $data["data"]  = $role;

            $view = "pages.role.edit";
            
            return view($view, $data);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'name'    => 'required',
            'status'  => 'required'
        ]);

        if($validator->fails()){
            return handleErrorResponse($request, 'The following fields are required !', 'setting/role', 404, null);
        }

        $role = Role::find($id);
        if(!$role){
            return handleErrorResponse($request, 'Opps, data not found!', 'setting/role', 404, null);
        }

        DB::beginTransaction();
        try {
            $role->name         = $request->name;
            $role->status       = $request->status;
            $role->updated_at   = date("Y-m-d H:i:s");
            $role->updated_by   = auth()->user()->id;
            $role->save();

            if($role){
                RoleDetail::where("role_id", $id)
                        ->update(array(
                            "status"    => "inactive", 
                            "deleted_at"=> date("Y-m-d H:i:s"), 
                            "deleted_by"=> auth()->user()->id
                        ));
                RoleDetail::where("role_id", $id)->delete();

                foreach($request->role_detail as $dtl){
                    $explode    = explode("-", $dtl);
                    $parent     = trim($explode[0]) === "" ? null : $explode[0];
                    $child      = trim($explode[1]) === "" ? null : $explode[1];
                    $subChild   = trim($explode[2]) === "" ? null : $explode[2];
                    
                    $roleDetail = RoleDetail::create([
                        "role_id"               => $id,
                        "menu_parent_id"        => $parent,
                        "menu_children_id"      => $child,
                        "menu_sub_children_id"  => $subChild,
                        "status"    => "active",
                        "created_at"=> date("Y-m-d H:i:s"),
                        "created_by"=> auth()->user()->id,
                    ]);
                }
            }
        }
        catch (Exception $e) {
            DB::rollback();
            return handleErrorResponse($request, $e->getMessage(), 'setting/role', 404, null);
        }

        DB::commit();

        if($request->expectsJson()){
            return response()->json([
                'status' => true,
                'message'=> "Data successfuly updated.",
                'code'   => 200,
                'results'=> []
            ], 200);
        }
        else{
            Session::put('success','Data successfuly updated.');
            return redirect()->to('setting/role');
        }
    }

    public function delete(Request $request, $id)
    {
        DB::beginTransaction();
        $role = Role::find($id);
        if(!$user){
            return handleErrorResponse($request, 'Opps, data not found.', 'setting/role', 404, null);
        }

        try {
            $role->status = "inactive";
            $role->save();
            $role->delete();
        }
        catch (Exception $e) {
            DB::rollBack();
            return handleErrorResponse($request, 'Opps, data failed to delete.', 'setting/role', 404, null);
        }

        DB::commit();

        if($request->expectsJson()){
            return response()->json([
                'status' => true,
                'message'=> "Data successfuly deleted.",
                'code'   => 200,
                'results'=> []
            ], 200);
        }
        else {
            Session::put('success','Data successfuly deleted.');
            return redirect()->to('setting/role');
        }
    }

    public function dataTables(Request $request)
    {
        $where = [];
        if($request->name != ""){
            $where[] = ["roles.name", "LIKE", "%".$request->name."%"];
        }
        if($request->status != ""){
            $where[] = ['roles.status', $request->status];
        }

        $data = Role::where($where)->get();
        return datatables()->of($data)->toJson();
    }
}
