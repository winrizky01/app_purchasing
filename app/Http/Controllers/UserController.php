<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Models\User;

use DB;
use Redirect;
use DataTables;
use Exception;
use Validator;
// use PDF;

class UserController extends Controller
{
    public function select(Request $request)
    {
        $query = User::select(["id", "name", "name as text"])->get();
        $data  = [];
        if($request->expectsJson() || $request->ajax()){
            return response()->json([
                'status' => true,
                'message'=> "User successfuly access",
                'code'   => 200,
                'results'=> $query
            ], 200);
        }
        else{

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
                $where[] = ["users.id", $request->id];
            }

            $offset = ($page - 1) * $limit;
            
            $query = User::with(['role','user_location','department','division'])->where($where)->orderBy("users.id", "ASC");
            if ($limit > 0) {
                $query = $query->offset($offset)->limit($limit)->paginate($limit);
            } 
            else {
                $query = $query->get();
            }

            return response()->json([
                        'status' => true,
                        'message'=> "User successfuly access",
                        'code'   => 200,
                        'results'=> $query
                    ], 200);
        }
        else {
            $data["title"] = "List Users";

            $view = "pages.user.index";
            return view($view, $data);
        }
    }

    public function create()
    {
        $data["title"] = "Add Users";

        $view = "pages.user.create";
        return view($view, $data);
    }

    public function edit(Request $request, $id)
    {
        $user = User::with(['role','user_location','department','division'])->find($id);
        if(!$user){
            return handleErrorResponse($request, 'Opps, data not found!', 'master/user', 404, null);
        }

        if($request->expectsJson())
        {
            return response()->json([
                'status' => true,
                'message'=> "Data found.",
                'code'   => 200,
                'results'=> $user
            ], 200);
        }
        else{
            $data["title"] = "Edit Users";
            $data["data"]  = $user;

            $view = "pages.user.edit";
            
            return view($view, $data);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name'    => 'required',
            'email'   => 'required',
            'password'=> 'required',
            'confirm_password' => 'required',
            'role_id' => 'required',
            'status'  => 'required'
        ]);

        if($validator->fails()){
            return handleErrorResponse($request, 'The following fields are required !', 'master/user', 404, null);
        }

        $check = User::where("name","LIKE","%".$request->name."%")->orWhere("email", "LIKE","%".$request->email."%")->get();
        if(count($check) > 0){
            return handleErrorResponse($request, 'Opps, this name or email is exist!', 'master/user', 404, null);
        }

        DB::beginTransaction();
        try {
            $user = User::create([
                "name"      => $request->name,
                "email"     => $request->email,
                "password"  => bcrypt($request->password),
                "role"      => (int)$request->role_id,
                "user_location_id"  => $request->user_location_id,
                "department_id"     => $request->department_id,
                "division_id"       => $request->division_id,
                "status"    => $request->status,
                "created_at"=> date("Y-m-d H:i:s")
            ]);
        } catch (Exception $e) {
            DB::rollback();
            return handleErrorResponse($request, $e->getMessage(), 'master/user', 404, null);
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
            return redirect()->to('master/user');
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'name'    => 'required',
            'email'   => 'required',
            'role_id' => 'required',
            'status'  => 'required'
        ]);

        if($validator->fails()){
            return handleErrorResponse($request, 'The following fields are required !', 'master/user', 404, null);
        }

        $user = User::find($id);
        if(!$user){
            return handleErrorResponse($request, 'Opps, data not found!', 'master/user', 404, null);
        }

        DB::beginTransaction();
        try {
            $user->name     = $request->name;
            $user->email    = $request->email;
            $user->role     = $request->role_id;
            $user->user_location_id = $request->user_location_id;
            $user->department_id    = $request->department_id;
            $user->division_id      = $request->division_id;
            $user->status   = $request->status;
            $user->updated_at = date("Y-m-d H:i:s");

            if($request->password != ""){
                $user->password = bcrypt($request->password);
            }

            $user->save();
        } catch (Exception $e) {
            DB::rollback();
            return handleErrorResponse($request, $e->getMessage(), 'master/user', 404, null);
        }

        DB::commit();

        if($request->expectsJson()){
            return response()->json([
                'status' => true,
                'message'=> "Data successfuly updated.",
                'code'   => 200,
                'results'=> $user
            ], 200);
        }
        else{
            Session::put('success','Data successfuly updated.');
            return redirect()->to('master/user');
        }
    }

    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        $user = User::find($id);
        if(!$user){
            return handleErrorResponse($request, 'Opps, data not found.', 'master/user', 404, null);
        }

        try {
            $user->status = "inactive";
            $user->save();
            $user->delete();
        } catch (Exception $e) {
            DB::rollBack();
            return handleErrorResponse($request, 'Opps, data failed to delete.', 'master/user', 404, null);
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
            return redirect()->to('master/user');
        }
    }

    public function dataTables(Request $request)
    {
        $where = [];
        if($request->name != ""){
            $where[] = ["users.name", "LIKE", "%".$request->name."%"];
        }
        if($request->email != ""){
            $where[] = ["users.email", "LIKE", "%".$request->email."%"];
        }
        if($request->status != ""){
            $where[] = ['users.status', $request->status];
        }
        
        $data = User::with(['role','user_location','department','division'])->where($where)->get();
        return datatables()->of($data)->toJson();
    }
}
