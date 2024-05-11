<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestDetail;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\General;
use App\Models\Role;

use DB;
use Redirect;
use DataTables;
use Exception;
use Validator;
use File;

class PurchaseRequestController extends Controller
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
                $where[] = ["purchase_requests.id", $request->id];
            }

            $offset = ($page - 1) * $limit;
            
            $query = PurchaseRequest::where($where)->orderBy("purchase_requests.id", "ASC");
            if ($limit > 0) {
                $query = $query->offset($offset)->limit($limit)->paginate($limit);
            } 
            else {
                $query = $query->get();
            }

            return response()->json([
                        'status' => true,
                        'message'=> "PurchaseRequest successfuly access",
                        'code'   => 200,
                        'results'=> $query
                    ], 200);
        }
        else {
            $data["title"] = "List Purchase Request";

            $view = "pages.purchase_request.index";
            return view($view, $data);
        }
    }

    public function create(Request $request)
    {
        $check_role = Role::find(auth()->user()->role);
        if(($check_role->name !== "Superadmin")&&($check_role->name !== "Administrator Inventory")){
            return handleErrorResponse($request, 'Opps, sorry you dont have access!', 'purchasing/purchase-request', 404, null);
        }

        $data["title"] = "Add Purchase Request";
        $data["document_number"] = "PR/PROJ/XII/".date("Y");
        $view = "pages.purchase_request.create";
        return view($view, $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'purchase_request_no' => 'required',
            'code'                => 'required',
            'department_id'       => 'required',
            'effective_date'      => 'required',
            'date'                => 'required',
        ]);

        if($validator->fails()){
            return handleErrorResponse($request, 'The following fields are required !', 'master/product', 404, null);
        }

        DB::beginTransaction();
        try {
            $file  = "";
            $photo = "";
            if ($request->hasFile('media')) {
                $file     = $request->file('media');
                $photo    = str_replace(" ", "-", $file->getClientOriginalName());
            }

            $purchaseRequest = PurchaseRequest::create([
                'code'                  => $request->code,
                'purchase_request_no'   => $request->purchase_request_no,
                'department_id'         => $request->department_id,
                'revision'              => $request->revision,
                'effective_date'        => $request->effective_date,
                'date'                  => $request->date,
                'document_status_id'    => $request->document_status_id,
                'notes'                 => $request->notes,
                "created_at"            => date("Y-m-d H:i:s"),
                "created_by"            => auth()->user()->id,
            ]);
            if($purchaseRequest){

                if ($request->purchase_request_details) {
                    foreach ($request->purchase_request_details as $key => $value) {
                        if($request->expectsJson()){
                            $product_id                    = null;
                            $qty                           = null;
                            $description                   = null;
                            $identity_required_date        = null;    
                        }
                        else{
                            $product_id                 = $value["product_id"];
                            $qty                        = $value["product_qty"];
                            $description                = $value["product_description"];
                            $identity_required_date     = $value["identity_required_date"];
                        }

                        $materialRequestDetail = PurchaseRequestDetail::create([
                            'purchase_request_id'       => $purchaseRequest->id,
                            'product_id'                => $product_id,
                            'qty'                       => $qty,
                            'description'               => $description,
                            'identity_required_date'    => $identity_required_date,
                        ]);

                        if (!$materialRequestDetail) {
                            DB::rollback();
                            return handleErrorResponse($request, "Opps, data failed created material request details", 'inventory/material-request', 404, null);
                        }
                    }
                }

                $getDocumentStatus = findAllStatusGeneral(["id"=>$request->document_status_id]);
                if($getDocumentStatus->name == "Submit"){
                    $approval = approvalTransaction($this->type_transaction_id, $purchaseRequest->id, $newDocumentStatus->id);
                    if($approval == false){
                        DB::rollback();
                        return handleErrorResponse($request, "Opps, error approval data", 'inventory/material-request', 404, null);
                    }
                }

            }
        } catch (Exception $e) {
            DB::rollback();
            return handleErrorResponse($request, $e->getMessage(), 'master/product', 404, null);
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
            return redirect()->to('purchasing/purchase-request');
        }
    }

    public function edit(Request $request, $id)
    {
        // page control
        if(!pageControl($request)){
            return redirect('/');
        }

        $check_role = session('role')->name;

        $materialRequest = PurchaseRequest::with(['purchase_request_details','purchase_request_details.product','department','document_status','createdBy','last_update'])->find($id);
        if(!$materialRequest){
            return handleErrorResponse($request, 'Opps, data not found!', 'purchasing/purchase_request', 404, null);
        }

        $getDocumentStatus = findAllStatusGeneral(["id"=>$materialRequest->document_status_id]);
        $getDocumentStatus = $getDocumentStatus->name;
        
        if($getDocumentStatus == "Draft"){
            if($check_role !== "End User"){
                return handleErrorResponse($request, 'Opps, sorry you dont have access!', 'purchasing/purchase_request', 404, null);
            }    
        }
        else if(($getDocumentStatus == "Waiting Approval Tech Support")||($getDocumentStatus == "Revisied Plant Manager")){
            if($check_role !== "Tech Support"){
                return handleErrorResponse($request, 'Opps, sorry you dont have access!', 'purchasing/purchase_request', 404, null);
            }    
        }
        else if($getDocumentStatus == "Waiting Approval Plant Manager"){
            if($check_role !== "Plant Manager"){
                return handleErrorResponse($request, 'Opps, sorry you dont have access!', 'purchasing/purchase_request', 404, null);
            }    
        }
        else{
            return handleErrorResponse($request, 'Opps, sorry you dont have access!', 'purchasing/purchase_request', 404, null);
        }

        if($request->expectsJson())
        {
            return response()->json([
                'status' => true,
                'message'=> "Data found.",
                'code'   => 200,
                'results'=> $purchaseRequest
            ], 200);
        }
        else{
            $data["title"] = "Edit PurchaseRequest";
            $data["data"]  = $purchaseRequest;

            $view = "pages.purchase_request.edit";
            
            return view($view, $data);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'purchase_request_no' => 'required',
            'code'                => 'required',
            'department_id'       => 'required',
            'effective_date'      => 'required',
            'date'                => 'required',
        ]);

        if($validator->fails()){
            return handleErrorResponse($request, 'The following fields are required !', 'master/purchase-request', 404, null);
        }

        $purchaseRequest = Product::find($id);
        if(!$purchaseRequest){
            return handleErrorResponse($request, 'Opps, data not found!', 'master/purchase-request', 404, null);
        }

        DB::beginTransaction();
        try {
            $purchaseRequest->code                          = $request->code;
            $purchaseRequest->purchase_request_no           = $request->purchase_request_no;
            $purchaseRequest->department_id                 = $request->department_id;
            $purchaseRequest->revision                      = $request->revision;
            $purchaseRequest->effective_date                = $request->effective_date;
            $purchaseRequest->date                          = $request->date;
            $purchaseRequest->document_status_id            = $request->document_status_id;
            $purchaseRequest->notes                         = $request->notes;
            $purchaseRequest->updated_at                    = date("Y-m-d H:i:s");
            $purchaseRequest->updated_by                    = auth()->user()->id;
            $purchaseRequest->save();
        }
        catch (Exception $e) {
            DB::rollback();
            return handleErrorResponse($request, $e->getMessage(), 'master/purchase-request', 404, null);
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
            return redirect()->to('master/purchase-request');
        }
    }

    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        $purchaseRequest = Product::find($id);
        if(!$purchaseRequest){
            return handleErrorResponse($request, 'Opps, data not found.', 'master/purchase_request', 404, null);
        }

        try {
            $purchaseRequest->document_status_id     = $request->document_status_id ;
            $purchaseRequest->deleted_at             = date("Y-m-d H:i:s");
            $purchaseRequest->deleted_by             = auth()->user()->id;
            $purchaseRequest->save();
            $purchaseRequest->delete();
        } catch (Exception $e) {
            DB::rollBack();
            return handleErrorResponse($request, 'Opps, data failed to delete.', 'master/purchase-request', 404, null);
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
            return redirect()->to('master/purchase-request');
        }
    }

    public function dataTables(Request $request)
    {
        $where = [];
        if($request->name != ""){
            $where[] = ["purchase_requests.name", "LIKE", "%".$request->name."%"];
        }
        if($request->status != ""){
            $where[] = ['purchase_requests.status', $request->status];
        }

        $data = PurchaseRequest::where($where)->get();
        return datatables()->of($data)->toJson();
    }

    public function print(Request $request, $id)
    {}
}
