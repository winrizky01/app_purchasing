<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Models\Division;

use DB;
use Redirect;
use DataTables;
use Exception;
use Validator;

class DivisionController extends Controller
{
    public function select(Request $request)
    {
        $query = Division::select(["id", "name", "name as text", "code"]);
        if($request->department_id != ""){
            $query = $query->where("deparmtent_id", $request->department_id);
        }
        $query = $query->get();

        if($request->expectsJson() || $request->ajax()){
            return response()->json([
                'status' => true,
                'message'=> "Division successfuly access",
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
                $where[] = ["divisions.id", $request->id];
            }

            $offset = ($page - 1) * $limit;
            
            $query = Division::where($where)->orderBy("divisions.id", "ASC");
            if ($limit > 0) {
                $query = $query->offset($offset)->limit($limit)->paginate($limit);
            } 
            else {
                $query = $query->get();
            }

            return response()->json([
                        'status' => true,
                        'message'=> "Division successfuly access",
                        'code'   => 200,
                        'results'=> $query
                    ], 200);
        }
        else {
            $data["title"] = "List Division";

            $view = "pages.division.index";
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
            return handleErrorResponse($request, 'The following fields are required !', 'master/division', 404, null);
        }

        DB::beginTransaction();
        try {
            $division = Division::create([
                "code"       => $request->code,
                "name"       => $request->name,
                "department_id" => $request->department_id,
                "description"=> $request->description,
                "status"     => $request->status,
                "created_at" => date("Y-m-d H:i:s"),
                "created_by" => auth()->user()->id,
            ]);
        } catch (Exception $e) {
            DB::rollback();
            return handleErrorResponse($request, $e->getMessage(), 'master/division', 404, null);
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
            return redirect()->to('master/division');
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
            return handleErrorResponse($request, 'The following fields are required !', 'master/division', 404, null);
        }

        $division = Division::find($id);
        if(!$division){
            return handleErrorResponse($request, 'Opps, data not found!', 'master/division', 404, null);
        }

        DB::beginTransaction();
        try {
            $division->code       = $request->code;
            $division->name       = $request->name;
            $division->department_id = $request->department_id;
            $division->description= $request->description;
            $division->status     = $request->status;
            $division->updated_at = date("Y-m-d H:i:s");
            $division->updated_by = auth()->user()->id;
            $division->save();
        }
        catch (Exception $e) {
            DB::rollback();
            return handleErrorResponse($request, $e->getMessage(), 'master/division', 404, null);
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
            return redirect()->to('master/division');
        }
    }

    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        $division = Division::find($id);
        if(!$division){
            return handleErrorResponse($request, 'Opps, data not found.', 'master/division', 404, null);
        }

        try {
            $division->status     = "inactive";
            $division->deleted_at = date("Y-m-d H:i:s");
            $division->deleted_by = auth()->user()->id;
            $division->save();
            $division->delete();
        } catch (Exception $e) {
            DB::rollBack();
            return handleErrorResponse($request, 'Opps, data failed to delete.', 'master/division', 404, null);
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
            $where[] = ["divisions.name", "LIKE", "%".$request->name."%"];
        }
        if($request->status != ""){
            $where[] = ['divisions.status', $request->status];
        }

        $data = Division::where($where)->get();
        return datatables()->of($data)->toJson();
    }
}
