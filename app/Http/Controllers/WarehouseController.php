<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Models\Warehouse;

use DB;
use Redirect;
use DataTables;
use Exception;
use Validator;

class WarehouseController extends Controller
{
    public function select(Request $request)
    {
        $query = Warehouse::select(["id", "name", "name as text"]);
        $query = $query->get();

        if($request->expectsJson() || $request->ajax()){
            return response()->json([
                'status' => true,
                'message'=> "Warehouse successfuly access",
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
                $where[] = ["warehouses.id", $request->id];
            }

            $offset = ($page - 1) * $limit;
            
            $query = Warehouse::where($where)->orderBy("warehouses.id", "ASC");
            if ($limit > 0) {
                $query = $query->offset($offset)->limit($limit)->paginate($limit);
            } 
            else {
                $query = $query->get();
            }

            return response()->json([
                        'status' => true,
                        'message'=> "Warehouse successfuly access",
                        'code'   => 200,
                        'results'=> $query
                    ], 200);
        }
        else {
            $data["title"] = "List Warehouse";

            $view = "pages.warehouse.index";
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
            return handleErrorResponse($request, 'The following fields are required !', 'master/warehouse', 404, null);
        }

        DB::beginTransaction();
        try {
            $warehouse = Warehouse::create([
                "code"                  => $request->code,
                "name"                  => $request->name,
                "location"              => $request->location,
                "contact_person"        => $request->contact_person,
                "contact_person_number" => $request->contact_person_number,
                "description"           => $request->description,
                "option_warehouse"      => $request->option_warehouse,
                "status"                => $request->status,
                "created_at"            => date("Y-m-d H:i:s"),
                "created_by"            => auth()->user()->id,
            ]);
        } catch (Exception $e) {
            DB::rollback();
            return handleErrorResponse($request, $e->getMessage(), 'master/warehouse', 404, null);
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
            return redirect()->to('master/warehouse');
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
            return handleErrorResponse($request, 'The following fields are required !', 'master/warehouse', 404, null);
        }

        $warehouse = Warehouse::find($id);
        if(!$warehouse){
            return handleErrorResponse($request, 'Opps, data not found!', 'master/warehouse', 404, null);
        }

        DB::beginTransaction();
        try {
            $warehouse->code       = $request->code;
            $warehouse->name       = $request->name;
            $warehouse->location   = $request->location;
            $warehouse->contact_person          = $request->contact_person;
            $warehouse->contact_person_number   = $request->contact_person_number;
            $warehouse->description             = $request->description;
            $warehouse->option_warehouse        = $request->option_warehouse;
            $warehouse->status     = $request->status;
            $warehouse->updated_at = date("Y-m-d H:i:s");
            $warehouse->updated_by = auth()->user()->id;
            $warehouse->save();
        }
        catch (Exception $e) {
            DB::rollback();
            return handleErrorResponse($request, $e->getMessage(), 'master/warehouse', 404, null);
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
            return redirect()->to('master/warehouse');
        }
    }

    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        $warehouse = Warehouse::find($id);
        if(!$warehouse){
            return handleErrorResponse($request, 'Opps, data not found.', 'master/warehouse', 404, null);
        }

        try {
            $warehouse->status     = "inactive";
            $warehouse->deleted_at = date("Y-m-d H:i:s");
            $warehouse->deleted_by = auth()->user()->id;
            $warehouse->save();
            $warehouse->delete();
        } catch (Exception $e) {
            DB::rollBack();
            return handleErrorResponse($request, 'Opps, data failed to delete.', 'master/warehouse', 404, null);
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
            return redirect()->to('master/warehouse');
        }
    }

    public function dataTables(Request $request)
    {
        $where = [];
        if($request->name != ""){
            $where[] = ["warehouses.name", "LIKE", "%".$request->name."%"];
        }
        if($request->status != ""){
            $where[] = ['warehouses.status', $request->status];
        }

        $data = Warehouse::where($where)->get();
        return datatables()->of($data)->toJson();
    }
}
