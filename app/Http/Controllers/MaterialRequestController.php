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
                'results'=> ["code"=>generateCodeDocument("MR",auth()->user()->division_id)]
            ], 200);
        }
        else{
            $data["title"] = "Add Material Request";
            $data["document_number"] = generateCodeDocument("MR",auth()->user()->division_id);
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
            $filephoto  = "";
            $photo = "";
            if ($request->hasFile('document_photo')) {
                $filephoto= $request->file('document_photo');
                $photo    = str_replace(" ", "-", $filephoto->getClientOriginalName());
            }

            $filepdf  = "";
            $pdf = "";
            if ($request->hasFile('document_pdf')) {
                $filepdf= $request->file('document_pdf');
                $pdf    = str_replace(" ", "-", $filepdf->getClientOriginalName());
            }

            $materialRequest = MaterialRequest::create([
                'type_material_request'     => $request->type_material_request_id,
                'code'                      => $request->code,
                'date'                      => date("Y-m-d", strtotime($request->date)),
                'request_date'              => date("Y-m-d", strtotime($request->request_date)),
                'department_id'             => auth()->user()->department_id,
                'division_id'               => auth()->user()->division_id,
                'justification'             => $request->justification,
                'remark_id'                 => $request->remark_id,
                'document_photo'            => $photo !== "" ? 'template/assets/material_request/'.$photo : null,
                'document_pdf'              => $pdf !== "" ? 'template/assets/material_request/'.$pdf : null,
                'document_status_id'        => $request->document_status_id,
                'document_status_id'        => 1,
                "created_at"                => date("Y-m-d H:i:s"),
                "created_by"                => auth()->user()->id,
            ]);
            if($materialRequest){
                if($photo != ""){
                    $filephoto->move(public_path('template/assets/material_request/'), $photo);
                }
                if($pdf != ""){
                    $filepdf->move(public_path('template/assets/material_request/'), $pdf);
                }

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
        // page control
        if(!pageControl($request)){
            return redirect('/');
        }

        $check_role = session('role')->name;

        $materialRequest = MaterialRequest::with(['material_request_details','material_request_details.product','department','document_status'])->find($id);
        if(!$materialRequest){
            return handleErrorResponse($request, 'Opps, data not found!', 'inventory/material-request', 404, null);
        }

        $getDocumentStatus = findAllStatusGeneral(["id"=>$materialRequest->document_status_id]);
        $getDocumentStatus = $getDocumentStatus->name;
        if($getDocumentStatus == "Draft"){
            if($check_role !== "End User"){
                return handleErrorResponse($request, 'Opps, sorry you dont have access!', 'inventory/material-request', 404, null);
            }    
        }
        else if(($getDocumentStatus == "Submit")||($getDocumentStatus == "Revisie")){
            if($check_role !== "Tech Support"){
                return handleErrorResponse($request, 'Opps, sorry you dont have access!', 'inventory/material-request', 404, null);
            }    
        }
        else if($getDocumentStatus == "Riview"){            
            if($check_role !== "Plan Manager"){
                return handleErrorResponse($request, 'Opps, sorry you dont have access!', 'inventory/material-request', 404, null);
            }    
        }

        if($request->expectsJson())
        {
            return response()->json([
                'status' => true,
                'message'=> "Data found.",
                'code'   => 200,
                'results'=> $materialRequest
            ], 200);
        }
        else{
            $data["title"] = "Edit Material Request";
            $data["data"]  = $materialRequest;
            $data["mode"]  = "edit";

            $view = "pages.material_request.edit";
            
            return view($view, $data);
        }
    }

    public function show(Request $request, $id)
    {
        $materialRequest = MaterialRequest::with([
            'material_request_details',
            'material_request_details.product',
            'material_request_details.product.product_category',
            'material_request_details.product.product_unit' ,
            'department',
            'document_status'])
            ->find($id);
        if(!$materialRequest){
            return handleErrorResponse($request, 'Opps, data not found!', 'inventory/material-request', 404, null);
        }

        if($request->expectsJson())
        {
            return response()->json([
                'status' => true,
                'message'=> "Data found.",
                'code'   => 200,
                'results'=> $materialRequest
            ], 200);
        }
        else{
            $data["title"] = "Edit Material Request";
            $data["data"]  = $materialRequest;
            $data["mode"]  = "show";
            
            $view = "pages.material_request.edit";
            
            return view($view, $data);
        }
    }

    public function update(Request $request, $id)
    {
        var_dump($request->all());die();
        
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

    public function reject(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'reason' => 'required',
        ]);
        if($validator->fails()){
            return handleErrorResponse($request, 'The following fields are required !', 'inventory/material-request', 404, null);
        }

        DB::beginTransaction();
        $materialRequest = MaterialRequest::find($id);
        if(!$materialRequest){
            return handleErrorResponse($request, 'Opps, data not found!', 'inventory/material-request', 404, null);
        }

        $getDocumentStatus = findAllStatusGeneral(["type"=>"document_status_id", "name"=>"Reject"]);
        $getDocumentStatus = $getDocumentStatus->id;

        try {
            $materialRequest->document_status_id = $getDocumentStatus;
            $materialRequest->updated_at = date("Y-m-d H:i:s");
            $materialRequest->updated_by = auth()->user()->id;
            $materialRequest->save();
        } catch (Exception $e) {
            DB::rollBack();
            return handleErrorResponse($request, 'Opps, data failed to reject.', 'inventory/material-request', 404, null);
        }

        var_dump($request->all());die();
    }

    public function revision(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'reason' => 'required',
        ]);
        if($validator->fails()){
            return handleErrorResponse($request, 'The following fields are required !', 'inventory/material-request', 404, null);
        }

        DB::beginTransaction();
        $materialRequest = MaterialRequest::find($id);
        if(!$materialRequest){
            return handleErrorResponse($request, 'Opps, data not found!', 'inventory/material-request', 404, null);
        }

        $getDocumentStatus = findAllStatusGeneral(["type"=>"document_status_id", "name"=>"Revision"]);
        $getDocumentStatus = $getDocumentStatus->id;

        try {
            $materialRequest->document_status_id = $getDocumentStatus;
            $materialRequest->updated_at = date("Y-m-d H:i:s");
            $materialRequest->updated_by = auth()->user()->id;
            $materialRequest->save();
        } catch (Exception $e) {
            DB::rollBack();
            return handleErrorResponse($request, 'Opps, data failed to reject.', 'inventory/material-request', 404, null);
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
