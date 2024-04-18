<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Models\Department;

use DB;
use Redirect;
use DataTables;
use Exception;
use Validator;

class DepartmentController extends Controller
{
    public function select(Request $request)
    {
        $query = Department::select(["id", "name", "name as text"]);
        $query = $query->get();

        if($request->expectsJson() || $request->ajax()){
            return response()->json([
                'status' => true,
                'message'=> "Department successfuly access",
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
                $where[] = ["departments.id", $request->id];
            }

            $offset = ($page - 1) * $limit;
            
            $query = Department::where($where)->orderBy("departments.id", "ASC");
            if ($limit > 0) {
                $query = $query->offset($offset)->limit($limit)->paginate($limit);
            } 
            else {
                $query = $query->get();
            }

            return response()->json([
                        'status' => true,
                        'message'=> "Department successfuly access",
                        'code'   => 200,
                        'results'=> $query
                    ], 200);
        }
        else {
            $data["title"] = "List Department";

            $view = "pages.department.index";
            return view($view, $data);
        }

    }

    public function create(Request $request)
    {}

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'code'    => 'required',
            'name'    => 'required',
            'status'  => 'required'
        ]);

        if($validator->fails()){
            return handleErrorResponse($request, 'The following fields are required !', 'master/department', 404, null);
        }

        DB::beginTransaction();
        try {
            $department = Department::create([
                "code"       => $request->code,
                "name"       => $request->name,
                "description"=> $request->description,
                "status"     => $request->status,
                "created_at" => date("Y-m-d H:i:s"),
                "created_by" => auth()->user()->id,
            ]);
        } catch (Exception $e) {
            DB::rollback();
            return handleErrorResponse($request, $e->getMessage(), 'master/department', 404, null);
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
            return redirect()->to('master/department');
        }
    }

    public function edit(Request $request)
    {}

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'code'    => 'required',
            'name'    => 'required',
            'status'  => 'required'
        ]);

        if($validator->fails()){
            return handleErrorResponse($request, 'The following fields are required !', 'master/department', 404, null);
        }

        $department = Department::find($id);
        if(!$department){
            return handleErrorResponse($request, 'Opps, data not found!', 'master/department', 404, null);
        }

        DB::beginTransaction();
        try {
            $department->code       = $request->code;
            $department->name       = $request->name;
            $department->description= $request->description;
            $department->status     = $request->status;
            $department->updated_at = date("Y-m-d H:i:s");
            $department->updated_by = auth()->user()->id;
            $department->save();
        }
        catch (Exception $e) {
            DB::rollback();
            return handleErrorResponse($request, $e->getMessage(), 'master/department', 404, null);
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
            return redirect()->to('master/department');
        }
    }

    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        $department = Department::find($id);
        if(!$department){
            return handleErrorResponse($request, 'Opps, data not found.', 'master/department', 404, null);
        }

        try {
            $department->status     = "inactive";
            $department->deleted_at = date("Y-m-d H:i:s");
            $department->deleted_by = auth()->user()->id;
            $department->save();
            $department->delete();
        } catch (Exception $e) {
            DB::rollBack();
            return handleErrorResponse($request, 'Opps, data failed to delete.', 'master/department', 404, null);
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
            return redirect()->to('master/department');
        }
    }

    public function dataTables(Request $request)
    {
        $where = [];
        if($request->name != ""){
            $where[] = ["departments.name", "LIKE", "%".$request->name."%"];
        }
        if($request->status != ""){
            $where[] = ['departments.status', $request->status];
        }

        $data = Department::where($where)->get();
        return datatables()->of($data)->toJson();
    }
}
