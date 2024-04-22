<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Models\General;

use DB;
use Redirect;
use DataTables;
use Exception;
use Validator;

class GeneralController extends Controller
{
    public function select(Request $request)
    {
        $query = General::select(["id", "name", "name as text"]);
        if($request->type != ""){
            $query = $query->where("type",$request->type);
        }
        if($request->whereIn != ""){
            $explode = explode("-", $request->whereIn);
            $query = $query->whereIn("name", $explode);
        }
        $query = $query->get();

        if($request->expectsJson() || $request->ajax()){
            return response()->json([
                'status' => true,
                'message'=> "General successfuly access",
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
                $where[] = ["generals.id", $request->id];
            }

            $offset = ($page - 1) * $limit;
            
            $query = General::where($where)->orderBy("generals.id", "ASC");
            if ($limit > 0) {
                $query = $query->offset($offset)->limit($limit)->paginate($limit);
            } 
            else {
                $query = $query->get();
            }

            return response()->json([
                        'status' => true,
                        'message'=> "General successfuly access",
                        'code'   => 200,
                        'results'=> $query
                    ], 200);
        }
        else {
            $data["title"] = "List General";

            $view = "pages.general.index";
            return view($view, $data);
        }

    }

    public function create(Request $request)
    {}

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name'    => 'required',
            'type'    => 'required',
            'status'  => 'required'
        ]);

        if($validator->fails()){
            return handleErrorResponse($request, 'The following fields are required !', 'setting/general', 404, null);
        }

        DB::beginTransaction();
        try {
            $general = General::create([
                "name"       => $request->name,
                "type"       => $request->type,
                "description"=> $request->description,
                "status"     => $request->status,
                "created_at" => date("Y-m-d H:i:s"),
                "created_by" => auth()->user()->id,
            ]);
        } catch (Exception $e) {
            DB::rollback();
            return handleErrorResponse($request, $e->getMessage(), 'setting/general', 404, null);
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
            return redirect()->to('setting/general');
        }
    }

    public function edit(Request $request)
    {}

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'name'    => 'required',
            'type'    => 'required',
            'status'  => 'required'
        ]);

        if($validator->fails()){
            return handleErrorResponse($request, 'The following fields are required !', 'setting/general', 404, null);
        }

        $general = General::find($id);
        if(!$general){
            return handleErrorResponse($request, 'Opps, data not found!', 'setting/general', 404, null);
        }

        DB::beginTransaction();
        try {
            $general->name       = $request->name;
            $general->type       = $request->type;
            $general->description= $request->description;
            $general->status     = $request->status;
            $general->updated_at = date("Y-m-d H:i:s");
            $general->updated_by = auth()->user()->id;
            $general->save();
        }
        catch (Exception $e) {
            DB::rollback();
            return handleErrorResponse($request, $e->getMessage(), 'setting/general', 404, null);
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
            return redirect()->to('setting/general');
        }
    }

    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        $general = General::find($id);
        if(!$general){
            return handleErrorResponse($request, 'Opps, data not found.', 'setting/general', 404, null);
        }

        try {
            $general->status     = "inactive";
            $general->deleted_at = date("Y-m-d H:i:s");
            $general->deleted_by = auth()->user()->id;
            $general->save();
            $general->delete();
        } catch (Exception $e) {
            DB::rollBack();
            return handleErrorResponse($request, 'Opps, data failed to delete.', 'setting/general', 404, null);
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
            return redirect()->to('setting/general');
        }
    }

    public function dataTables(Request $request)
    {
        $where = [];
        if($request->name != ""){
            $where[] = ["generals.name", "LIKE", "%".$request->name."%"];
        }
        if($request->type != ""){
            $where[] = ['generals.type', "LIKE", "%".$request->type."%"];
        }
        if($request->status != ""){
            $where[] = ['generals.status', $request->status];
        }

        $data = General::where($where)->get();
        return datatables()->of($data)->toJson();
    }

}
