<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Models\User;
use App\Models\Role;
use App\Models\General;
use App\Models\Approval;
use App\Models\Product;
use App\Models\AdjustmentStock;
use App\Models\AdjustmentStockDetail;

use DB;
use Redirect;
use DataTables;
use Exception;
use Validator;
use File;
use PDF;

class AdjustmentStockController extends Controller
{
    protected $type_transaction_id;

    public function __construct()
    {
        $this->type_transaction_id = findAllStatusGeneral(["name"=>"ADJ"]);
        $this->type_transaction_id = $this->type_transaction_id->id;
    }

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
            
            $query = AdjustmentStock::where($where)->orderBy("adjustment_stocks.id", "ASC");
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
            // page control
            if(!pageControl($request)){
                return redirect('/');
            }
            $data["title"] = "List Adjustment Stock";

            $view = "pages.adjustment_stock.index";
            return view($view, $data);
        }
    }

    public function create(Request $request)
    {
        $check_role = Role::find(auth()->user()->role);
        if(($check_role->name !== "Superadmin")){
            return handleErrorResponse($request, 'Opps, sorry you dont have access!', 'inventory/material-request', 404, null);
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
            // page control
            if(!pageControl($request)){
                return redirect('/');
            }            

            $data["title"] = "Add Adjustment Stock";
            $data["document_number"] = generateCodeDocument("ADJ",false);
            $view = "pages.adjustment_stock.create";
            return view($view, $data);
        }
    }

    public function store(Request $request)
    {
        // page control
        if(!pageControl($request)){
            return redirect('/');
        }
        
        $validator = Validator::make($request->all(),[
            'stock_type_id' => 'required',
            'code' => 'required',
            'date' => 'required',
            'warehouse_id'  => 'required',
            'document_status_id'=> 'required',
        ]);

        if($validator->fails()){
            return handleErrorResponse($request, 'The following fields are required !', 'inventory/adjustment-stock', 404, null);
        }

        DB::beginTransaction();
        try {
            // $newDocumentStatus = findAllStatusGeneral(["name"=>"Waiting Approval Tech Support"]);
            $getDocumentStatus = findAllStatusGeneral(["id"=>$request->document_status_id]);
            $doc_status = $getDocumentStatus->id;

            if($getDocumentStatus->name == "Submit"){
                //$doc_status = $newDocumentStatus->id;
            }

            $adjustment = AdjustmentStock::create([
                'code'              => $request->code,
                'date'              => date("Y-m-d H:i:s", strtotime($request->date)),
                'stock_type_id'     => $request->stock_type_id,
                'warehouse_id'      => $request->warehouse_id,
                'description'       => $request->description,
                'document_status_id'=> $doc_status,
                "created_at"        => date("Y-m-d H:i:s"),
                "created_by"        => auth()->user()->id,
            ]);
            if($adjustment){
                if ($request->adjustment_details) {
                    foreach ($request->adjustment_details as $key => $value) {
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

                        $adjustmentDetail = AdjustmentStockDetail::create([
                            'adjustment_stock_id' => $adjustment->id,
                            'stock_type_id' => $request->stock_type_id,
                            'product_id'    => $product_id,
                            'qty'           => $qty,
                            'notes'         => $notes,
                        ]);

                        if (!$adjustmentDetail) {
                            DB::rollback();
                            return handleErrorResponse($request, "Opps, data failed created adjustment details", 'inventory/adjustment-stock', 404, null);
                        }

                        $productStock = productStock(
                            $this->type_transaction_id, 
                            $adjustment->id, 
                            $request->warehouse_id, 
                            $request->stock_type_id, 
                            $product_id, 
                            $qty);
                        if($productStock == false){
                            DB::rollback();
                            return handleErrorResponse($request, "Opps, error product stock", 'inventory/adjustment-stock', 404, null);
                        }    
                    }
                }
            }
        } catch (Exception $e) {
            DB::rollback();
            return handleErrorResponse($request, $e->getMessage(), 'inventory/adjustment-stock', 404, null);
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
            return redirect()->to('inventory/adjustment-stock');
        }
    }

    public function edit(Request $request, $id)
    {
        // page control
        if(!pageControl($request)){
            return redirect('/');
        }

        $check_role = session('role')->name;

        $materialRequest = MaterialRequest::with(['material_request_details','material_request_details.product','department','document_status','createdBy','last_update'])->find($id);
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
        else if(($getDocumentStatus == "Waiting Approval Tech Support")||($getDocumentStatus == "Revisied From Plant Manager")){
            if($check_role !== "Tech Support"){
                return handleErrorResponse($request, 'Opps, sorry you dont have access!', 'inventory/material-request', 404, null);
            }    
        }
        else if($getDocumentStatus == "Waiting Approval Plant Manager"){            
            if($check_role !== "Plant Manager"){
                return handleErrorResponse($request, 'Opps, sorry you dont have access!', 'inventory/material-request', 404, null);
            }    
        }
        else{
            return handleErrorResponse($request, 'Opps, sorry you dont have access!', 'inventory/material-request', 404, null);
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
        // page control
        if(!pageControl($request)){
            return redirect('/');
        }            
        
        $materialRequest = MaterialRequest::with([
            'material_request_details',
            'material_request_details.product',
            'material_request_details.product.product_category',
            'material_request_details.product.product_unit' ,
            'department',
            'document_status',
            'createdBy',
            'last_update'])
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

    /**
     * hanya handle update revisi langsung dari pihak level atas
     * dan
     * persetujuan
     * untuk reject dan permintaan revisi dari pihak level atas 
     * function ada di class berbeda
     */
    public function update(Request $request, $id)
    {}

    /**
     * permintaan pembatalan data
     */
    public function reject(Request $request, $id)
    {}

    /**
     * permintaan pembenaran data dari level atas (Tech Support Up)
     */
    public function revision(Request $request, $id)
    {}

    public function destroy(Request $request, $id)
    {}

    public function dataTables(Request $request)
    {
        $where = [];
        if($request->date != ""){
            $date = explode(" to ", $request->date);
            $start_date = $date[0];
            if(count($date) > 1){
                $end_date = $date[1];
                $where[]  = ["material_requests.request_date", ">=", date("Y-m-d H:i:s", strtotime($start_date." 00:00:00"))];
                $where[]  = ["material_requests.request_date", "<=", date("Y-m-d H:i:s", strtotime($end_date." 23:59:59"))];
            }
            else{
                $where[] = ["material_requests.request_date", "LIKE", "%".$start_date."%"];
            }
        }        
        if($request->code != ""){
            $where[] = ["material_requests.code", "LIKE", "%".$request->code."%"];
        }
        if($request->status != ""){
            $where[] = ['material_requests.document_status_id', $request->status];
        }

        $data = AdjustmentStock::with(['warehouse','document_status'])->where($where)->get();

        return datatables()->of($data)->toJson();
    }

    public function print(Request $request, $id)
    {
        // page control
        if(!pageControl($request)){
            return redirect('/');
        }            

        $adjustment = AdjustmentStock::with([
                'warehouse',
                'document_status',
                'type_adjustment',
                'detail.product',
                'detail.product.product_unit',
            ])
            ->find($id);

        $createdBy = User::with(['roleId'])->find($adjustment->created_by);
        $createdBy = ["name"=>$createdBy->name, "role"=>$createdBy->roleId->name];

        $signature = ["created"=>$createdBy, "riviwed"=>"", "approved"=>""];

        $data = [
            'title'     => 'Adjustment Stock', 
            'data'      => $adjustment,
            'signature' => $signature
        ];

        $pdfContent = view('pdf.adjustment_stock.print', compact('data'));

        $pdf = PDF::loadHtml($pdfContent);
        
        return $pdf->download('document.pdf');
    }
}
