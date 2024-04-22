<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Models\Role;
use App\Models\General;
use App\Models\Product;
use App\Models\MaterialRequest;
use App\Models\MaterialRequestDetail;

use DB;
use Redirect;
use DataTables;
use Exception;
use Validator;
use File;

class MaterialRequestController extends Controller
{
    public function select(Request $request)
    {
        $query = Product::select(["id", "name", "name as text"]);
        if($request->product_category_id != ""){
            $query = $query->where("product_category_id",$request->product_category_id);
        }
        $query = $query->get();

        if($request->expectsJson() || $request->ajax()){
            return response()->json([
                'status' => true,
                'message'=> "Product successfuly access",
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
                $where[] = ["material_requests.id", $request->id];
            }

            $offset = ($page - 1) * $limit;
            
            $query = MaterialRequest::where($where)->orderBy("material_requests.id", "ASC");
            if ($limit > 0) {
                $query = $query->offset($offset)->limit($limit)->paginate($limit);
            } 
            else {
                $query = $query->get();
            }

            return response()->json([
                        'status' => true,
                        'message'=> "Material Request successfuly access",
                        'code'   => 200,
                        'results'=> $query
                    ], 200);
        }
        else {
            $data["title"] = "List Material Request";

            $view = "pages.material_request.index";
            return view($view, $data);
        }
    }

    public function create(Request $request)
    {
        $check_role = Role::find(auth()->user()->role);
        if(($check_role->name !== "Superadmin")&&($check_role->name !== "End User")){
            return handleErrorResponse($request, 'Opps, sorry you dont have access!', 'purchasing/material-request', 404, null);
        }

        if($request->expectsJson() || $request->ajax()){
            return response()->json([
                'status' => true,
                'message'=> "Product successfuly access",
                'code'   => 200,
                'results'=> ["code"=>generateCodeDocument("MR",$request->division_id)]
            ], 200);
        }
        else{
            $data["title"] = "Add Material Request";
            $data["document_number"] = generateCodeDocument("MR",false);
            $view = "pages.material_request.create";
            return view($view, $data);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'date'              => 'required',
            'request_date'      => 'required',
        ]);

        if($validator->fails()){
            return handleErrorResponse($request, 'The following fields are required !', 'inventory/material-request', 404, null);
        }

        DB::beginTransaction();
        try {
            $materialRequest = MaterialRequest::create([
                'type_material_request'     => $request->type_material_request,
                'code'                      => $request->code,
                'date'                      => date("Y-m-d", strtotime($request->date)),
                'request_date'              => date("Y-m-d", strtotime($request->request_date)),
                'department_id'             => $request->department_id,
                'division_id'               => $request->division_id,
                'justification'             => $request->justification,
                'remark_id'                 => $request->remark_id,
                // "description"               => $request->description,
                'document_status_id'        => $request->document_status_id,
                "created_at"                => date("Y-m-d H:i:s"),
                "created_by"                => auth()->user()->id,
            ]);
            if($materialRequest){
                if ($request->material_request_details) {
                    foreach ($request->material_request_details as $key => $value) {
                        $product_id = null;
                        $qty        = null;
                        $notes      = null;
                        if($request->expectsJson()){
                            $product_id = null;
                            $qty        = null;
                            $notes      = null;    
                        }
                        else{
                            $product_id = $value["product_id"];
                            $qty        = $value["product_qty"];
                            $notes      = $value["product_note"];
                        }

                        $materialRequestDetail = MaterialRequestDetail::create([
                            'material_request_id' => $materialRequest->id,
                            'product_id' => $product_id,
                            'qty'        => $qty,
                            'notes'      => $notes,
                        ]);

                        if (!$materialRequestDetail) {
                            DB::rollback();
                            return handleErrorResponse($request, "Opps, data failed created material request details", 'inventory/material-request', 404, null);
                            // $result['success'] = false;
                            // $result['message'] = 'Gagal membuat data material request details';
                            // echo json_encode($result);
                            // return;
                        }
                    }
                }    
            }
        } catch (Exception $e) {
            DB::rollback();
            return handleErrorResponse($request, $e->getMessage(), 'inventory/material-request', 404, null);
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
            return redirect()->to('inventory/material-request');
        }
    }

    public function edit(Request $request, $id)
    {
        $product = Product::find($id);
        if(!$product){
            return handleErrorResponse($request, 'Opps, data not found!', 'master/product', 404, null);
        }

        if($request->expectsJson())
        {
            return response()->json([
                'status' => true,
                'message'=> "Data found.",
                'code'   => 200,
                'results'=> $product
            ], 200);
        }
        else{
            $data["title"] = "Edit Product";
            $data["data"]  = $product;

            $view = "pages.product.edit";
            
            return view($view, $data);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'product_category_id' => 'required',
            'name'      => 'required',
            'code'      => 'required',
            'sku'       => 'required',
            'unit_id'   => 'required',
            'is_inventory'  => 'required',
            'status'    => 'required'
        ]);

        if($validator->fails()){
            return handleErrorResponse($request, 'The following fields are required !', 'master/product', 404, null);
        }

        $product = Product::find($id);
        if(!$product){
            return handleErrorResponse($request, 'Opps, data not found!', 'master/product', 404, null);
        }

        DB::beginTransaction();
        try {
            $product->product_category_id = $request->product_category_id;
            $product->name          = $request->name;
            $product->code          = $request->code;
            $product->sku           = $request->sku;
            $product->unit_id       = $request->unit_id;
            $product->is_inventory  = $request->is_inventory;
            $product->dimension     = $request->dimension;
            $product->part_number   = $request->part_number;
            $product->machine_id    = $request->machine_id;
            $product->description   = $request->description;
            $product->status        = $request->status;
            $product->updated_at    = date("Y-m-d H:i:s");
            $product->updated_by    = auth()->user()->id;

            if ($request->hasFile('media')) {
                $file   = $request->file('media');
                $photo  = str_replace(" ", "-", $file->getClientOriginalName());
                $product->photo = $photo !== "" ? 'template/assets/img/products/'.$photo : null;
                $file->move(public_path('template/assets/img/products/'), $photo);
            }
            $product->save();
        }
        catch (Exception $e) {
            DB::rollback();
            return handleErrorResponse($request, $e->getMessage(), 'master/product', 404, null);
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
            return redirect()->to('master/product');
        }
    }

    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        $product = Product::find($id);
        if(!$product){
            return handleErrorResponse($request, 'Opps, data not found.', 'master/product', 404, null);
        }

        try {
            $product->status     = "inactive";
            $product->deleted_at = date("Y-m-d H:i:s");
            $product->deleted_by = auth()->user()->id;
            $product->save();
            $product->delete();
        } catch (Exception $e) {
            DB::rollBack();
            return handleErrorResponse($request, 'Opps, data failed to delete.', 'master/product', 404, null);
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
            return redirect()->to('master/product');
        }
    }

    public function dataTables(Request $request)
    {
        $where = [];
        if($request->code != ""){
            $where[] = ["material_requests.name", "LIKE", "%".$request->code."%"];
        }
        if($request->status != ""){
            $where[] = ['material_requests.document_status_id', $request->document_status_id];
        }

        $data = MaterialRequest::with(['department','document_status'])->where($where)->get();
        return datatables()->of($data)->toJson();
    }

    public function print(Request $request, $id)
    {}
}
