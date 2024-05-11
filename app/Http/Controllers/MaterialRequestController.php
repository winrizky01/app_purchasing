<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Models\User;
use App\Models\Role;
use App\Models\General;
use App\Models\Product;
use App\Models\MaterialRequest;
use App\Models\MaterialRequestDetail;
use App\Models\MaterialRequestRevision;
use App\Models\MRHistory;
use App\Models\MRDHistory;
use App\Models\Approval;

use DB;
use Redirect;
use DataTables;
use Exception;
use Validator;
use File;
use PDF;

class MaterialRequestController extends Controller
{
    protected $type_transaction_id;

    public function __construct()
    {
        $this->type_transaction_id = findAllStatusGeneral(["name"=>"MR"]);
        $this->type_transaction_id = $this->type_transaction_id->id;
    }

    public function select(Request $request)
    {
        // $query = Product::select(["id", "name", "name as text"]);
        // if($request->product_category_id != ""){
        //     $query = $query->where("product_category_id",$request->product_category_id);
        // }
        // $query = $query->get();

        // if($request->expectsJson() || $request->ajax()){
        //     return response()->json([
        //         'status' => true,
        //         'message'=> "Product successfuly access",
        //         'code'   => 200,
        //         'results'=> $query
        //     ], 200);
        // }
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
            // page control
            if(!pageControl($request)){
                return redirect('/');
            }
            $data["title"] = "List Material Request";

            $view = "pages.material_request.index";
            return view($view, $data);
        }
    }

    public function create(Request $request)
    {
        $check_role = Role::find(auth()->user()->role);
        if(($check_role->name !== "Superadmin")&&($check_role->name !== "End User")){
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

            $data["title"] = "Add Material Request";
            $data["document_number"] = generateCodeDocument("MR",auth()->user()->division_id);
            $view = "pages.material_request.create";
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
            'material_request_type_id' => 'required',
            'justification'      => 'required',
            'document_status_id' => 'required',
            'request_date'       => 'required',
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
                $filephoto->move(public_path('template/assets/material_request/'), $photo);
            }

            $filepdf  = "";
            $pdf = "";
            if ($request->hasFile('document_pdf')) {
                $filepdf= $request->file('document_pdf');
                $pdf    = str_replace(" ", "-", $filepdf->getClientOriginalName());
                $filepdf->move(public_path('template/assets/material_request/'), $pdf);
            }

            $newDocumentStatus = findAllStatusGeneral(["name"=>"Waiting Approval Tech Support"]);
            $getDocumentStatus = findAllStatusGeneral(["id"=>$request->document_status_id]);
            $doc_status = $getDocumentStatus->id;
            if($getDocumentStatus->name == "Submit"){
                $doc_status = $newDocumentStatus->id;
            }

            $materialRequest = MaterialRequest::create([
                'type_material_request'     => $request->material_request_type_id,
                'code'                      => $request->code,
                'request_date'              => date("Y-m-d H:i:s"),
                'department_id'             => auth()->user()->department_id,
                'division_id'               => auth()->user()->division_id,
                'justification'             => $request->justification,
                'remark_id'                 => $request->remark_id,
                'document_photo'            => $photo !== "" ? 'template/assets/material_request/'.$photo : null,
                'document_pdf'              => $pdf !== "" ? 'template/assets/material_request/'.$pdf : null,
                'document_status_id'        => $doc_status,
                "created_at"                => date("Y-m-d H:i:s"),
                "created_by"                => auth()->user()->id,
            ]);
            if($materialRequest){

                if ($request->material_request_details) {
                    foreach ($request->material_request_details as $key => $value) {
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
                            'document_status_id' => 1 // default status detail before used
                        ]);

                        if (!$materialRequestDetail) {
                            DB::rollback();
                            return handleErrorResponse($request, "Opps, data failed created material request details", 'inventory/material-request', 404, null);
                        }
                    }
                }

                $getDocumentStatus = findAllStatusGeneral(["id"=>$request->document_status_id]);
                if($getDocumentStatus->name == "Submit"){
                    $approval = approvalTransaction($this->type_transaction_id, $materialRequest->id, $newDocumentStatus->id);
                    if($approval == false){
                        DB::rollback();
                        return handleErrorResponse($request, "Opps, error approval data", 'inventory/material-request', 404, null);
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
        else if(($getDocumentStatus == "Waiting Approval Tech Support")||($getDocumentStatus == "Revisied Plant Manager")){
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
    {
        // page control
        if(!pageControl($request)){
            return redirect('/');
        }
        
        // validasi
        $validator = Validator::make($request->all(),[
            'material_request_type_id' => 'required',
            'justification'      => 'required',
            'document_status_id' => 'required',
            'request_date'       => 'required',
        ]);
        if($validator->fails()){
            return handleErrorResponse($request, 'The following fields are required !', 'inventory/material-request', 404, null);
        }
        // validasi

        $materialRequest = MaterialRequest::find($id);
        if(!$materialRequest){
            return handleErrorResponse($request, 'Opps, data not found!', 'inventory/material-request', 404, null);
        }

        // akses
        $getDocumentStatus = findAllStatusGeneral(["id"=>$materialRequest->document_status_id]);
        $getDocumentStatus = $getDocumentStatus->name;

        if($getDocumentStatus == "Waiting Approval Tech Support"){
            $newDocumentStatus = findAllStatusGeneral(["name"=>"Waiting Approval Plant Manager"]);
        }
        else if($getDocumentStatus == "Waiting Approval Plant Manager"){
            $newDocumentStatus = findAllStatusGeneral(["name"=>"Approved Plant Manager"]);
        }
        else if($getDocumentStatus == "Revisied Plant Manager"){
            $newDocumentStatus = findAllStatusGeneral(["name"=>"Waiting Approval Plant Manager"]);
        }
        else{
            return handleErrorResponse($request, "Opps, error approval data!", 'inventory/material-request', 404, null);
        }
        // akses

        DB::beginTransaction();
        try {
            $revision = $materialRequest->revision; // nilai revisi ke
            // tambahkan histori revisi jika user melakukan revisi
            if($request->isChange == "true"){
                $addRevision = transactionHistoryRevision("MR", $id);
                if($addRevision == false){
                    return handleErrorResponse($request, "Opps, error approval data!", 'inventory/material-request', 404, null);
                }
                $revision = $addRevision;
            }
            // tambahkan histori revisi jika user melakukan revisi

            $filephoto  = "";
            $photo = "";
            if ($request->hasFile('document_photo')) {
                $filephoto= $request->file('document_photo');
                $photo    = str_replace(" ", "-", $filephoto->getClientOriginalName());
                $filephoto->move(public_path('template/assets/material_request/'), $photo);
            }

            $filepdf  = "";
            $pdf = "";
            if ($request->hasFile('document_pdf')) {
                $filepdf= $request->file('document_pdf');
                $pdf    = str_replace(" ", "-", $filepdf->getClientOriginalName());
                $filepdf->move(public_path('template/assets/material_request/'), $pdf);
            }

            $materialRequest->type_material_request = $request->material_request_type_id;
            $materialRequest->justification         = $request->justification;
            $materialRequest->remark_id             = $request->remark_id;
            $materialRequest->document_photo        = $photo !== "" ? 'template/assets/material_request/'.$photo : null;
            $materialRequest->document_pdf          = $pdf !== "" ? 'template/assets/material_request/'.$pdf : null;
            $materialRequest->document_status_id    = $newDocumentStatus->id;
            $materialRequest->updated_at            = date("Y-m-d H:i:s");
            $materialRequest->updated_by            = auth()->user()->id;
            // $materialRequest->last_reason           = null;
            $materialRequest->revision              = $revision;
            $materialRequest->save();

            if($materialRequest){
                // delete all 
                $materialRequestDetail = MaterialRequestDetail::where("material_request_id", $id)->forceDelete();
                // regenerate new
                if ($request->material_request_details) {
                    foreach ($request->material_request_details as $key => $value) {
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
                            'material_request_id' => $id,
                            'product_id' => $product_id,
                            'qty'        => $qty,
                            'notes'      => $notes,
                            'document_status_id' => 1 // default status before used
                        ]);

                        if (!$materialRequestDetail) {
                            DB::rollback();
                            return handleErrorResponse($request, "Opps, data failed created material request details", 'inventory/material-request', 404, null);
                        }
                    }
                }

                // tambahkan approval untuk pihak tech support di db 
                // tapi tidak perlu ditampilkan di frontend
                if($getDocumentStatus == "Waiting Approval Tech Support"){
                    $sid = findAllStatusGeneral(["name"=>"Approved Tech Support"]);
                    $app = approvalTransaction($this->type_transaction_id, $materialRequest->id, $sid->id);
                }
                // tambahkan approval untuk pihak tech support di db 
                // tapi tidak perlu ditampilkan di frontend

                $approval = approvalTransaction($this->type_transaction_id, $materialRequest->id, $newDocumentStatus->id);
                if($approval == false){
                    DB::rollback();
                    return handleErrorResponse($request, "Opps, error approval data", 'inventory/material-request', 404, null);
                }
            }
        }
        catch (Exception $e) {
            DB::rollback();
            return handleErrorResponse($request, $e->getMessage(), 'inventory/material-request', 404, null);
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
            return redirect()->to('inventory/material-request');
        }
    }

    /**
     * permintaan pembatalan data
     */
    public function reject(Request $request, $id)
    {
        // page control
        if(!pageControl($request)){
            return redirect('/');
        }            
        
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

        $getDocumentStatus = findAllStatusGeneral(["id"=>$materialRequest->document_status_id]);
        if($getDocumentStatus->name == "Waiting Approval Tech Support"){
            $newDocumentStatus = findAllStatusGeneral(["name"=>"Rejected Tech Support"]);
        }
        else if($getDocumentStatus->name == "Waiting Approval Plant Manager"){
            $newDocumentStatus = findAllStatusGeneral(["name"=>"Rejected Plant Manager"]);
        }
        try {
            $materialRequest->document_status_id = $newDocumentStatus->id;
            $materialRequest->last_reason= $request->reason;
            $materialRequest->updated_at = date("Y-m-d H:i:s");
            $materialRequest->updated_by = auth()->user()->id;
            $materialRequest->save();

            if(!$materialRequest){
                return handleErrorResponse($request, 'Opps, error material request.', 'inventory/material-request', 404, null);
            }

            $approval = approvalTransaction($this->type_transaction_id, $materialRequest->id, $newDocumentStatus->id);
            if($approval == false){
                DB::rollback();
                return handleErrorResponse($request, "Opps, error approval data", 'inventory/material-request', 404, null);
            }

        } catch (Exception $e) {
            DB::rollBack();
            return handleErrorResponse($request, $e->getMessage(), 'inventory/material-request', 404, null);
        }

        DB::commit();

        if($request->expectsJson()){
            return response()->json([
                'status' => true,
                'message'=> "Data successfuly rejected.",
                'code'   => 200,
                'results'=> []
            ], 200);
        }
        else {
            Session::put('success','Data successfuly rejected.');
            return redirect()->to('inventory/material-request');
        }
    }

    /**
     * permintaan pembenaran data dari level atas (Tech Support Up)
     */
    public function revision(Request $request, $id)
    {
        // page control
        if(!pageControl($request)){
            return redirect('/');
        }            
        
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

        $getDocumentStatus = findAllStatusGeneral(["name"=>"Revisied Plant Manager"]);
        $getDocumentStatus = $getDocumentStatus->id;

        try {
            $materialRequest->document_status_id = $getDocumentStatus;
            $materialRequest->last_reason = $request->reason;
            $materialRequest->updated_at = date("Y-m-d H:i:s");
            $materialRequest->updated_by = auth()->user()->id;
            $materialRequest->save();

            $revision = MaterialRequestRevision::create([
                "material_request_id" => $materialRequest->id,
                "reasons"       => $request->reason,
                "user_id"       => auth()->user()->id,
                "date"          => date("Y-m-d"),
                "created_at"    => date("Y-m-d H:i:s"),
                "created_by"    => auth()->user()->id,
            ]);
            if(!$revision){
                DB::rollback();
                return handleErrorResponse($request, "Opps, error created material request revision data", 'inventory/material-request', 404, null);
            }

            $approval = approvalTransaction($this->type_transaction_id, $materialRequest->id, $getDocumentStatus);
            if($approval == false){
                DB::rollback();
                return handleErrorResponse($request, "Opps, error approval data", 'inventory/material-request', 404, null);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return handleErrorResponse($request, $e->getMessage(), 'inventory/material-request', 404, null);
        }

        DB::commit();

        if($request->expectsJson()){
            return response()->json([
                'status' => true,
                'message'=> "Data successfuly revisied.",
                'code'   => 200,
                'results'=> []
            ], 200);
        }
        else {
            Session::put('success','Data successfuly revisied.');
            return redirect()->to('inventory/material-request');
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

        $data = MaterialRequest::with([
            'material_request_details',
            'material_request_details.product',
            'material_request_details.product.product_category',
            'material_request_details.product.product_unit',
            'department',
            'division',
            'document_status'])->where($where)->get();

        return datatables()->of($data)->toJson();
    }

    public function print(Request $request, $id)
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
            'material_request_details.product.product_machine.machine' ,
            'department',
            'division',
            'remark',
            'document_status',
            'createdBy',
            'last_update'])
            ->find($id);

        $createdBy = User::with(['roleId'])->find($materialRequest->created_by);
        $createdBy = ["name"=>$createdBy->name, "role"=>$createdBy->roleId->name];

        $historyTechSupport = findAllStatusGeneral(["name"=>"Approved Tech Support"]);
        $riviwedBy = DB::table("approvals")
                        ->where("type_transaction_id", $this->type_transaction_id)
                        ->where("transaction_id", $id)
                        ->where("document_status", $historyTechSupport->id)
                        ->orderBy("created_at", "DESC")
                        ->get();
        if(count($riviwedBy) > 0){
            $riviwedBy = User::with(["roleId"])->find($riviwedBy[0]->user_id);
            $riviwedBy = ["name"=>$riviwedBy->name, "role"=>$riviwedBy->roleId->name];
        }
        else{
            $riviwedBy = [];
        }

        $historyPlantManager = findAllStatusGeneral(["name"=>"Approved Plant Manager"]);
        $approvedBy = DB::table("approvals")
                        ->where("type_transaction_id", $this->type_transaction_id)
                        ->where("transaction_id", $id)
                        ->where("document_status", $historyPlantManager->id)
                        ->orderBy("created_at", "DESC")
                        ->get();
        if(count($approvedBy) > 0){
            $approvedBy = User::with(["roleId"])->find($approvedBy[0]->user_id);
            $approvedBy = ["name"=>$approvedBy->name, "role"=>$approvedBy->roleId->name];
        }
        else{
            $approvedBy = [];
        }

        $signature = ["created"=>$createdBy, "riviwed"=>$riviwedBy, "approved"=>$approvedBy];

        $data = [
            'title'     => 'Material Request', 
            'data'      => $materialRequest,
            'signature' => $signature
        ];

        $pdfContent = view('pdf.material_request.print', compact('data'));

        $pdf = PDF::loadHtml($pdfContent);
        
        return $pdf->download('material_request.pdf');
    }

    public function history(Request $request, $id){
        $materialRequest = MaterialRequest::find($id);
        $code = $materialRequest->code;

        $history = MRHistory::with([
            'material_type',
            'remark', 
            'revisiedBy', 
            'detail', 
            'detail.product', 
            'detail.product.product_category', 
            'detail.product.product_unit'])
            ->where("from_material_request_id",$id)
            ->get();
        $data["title"] = "History Revision Material Request - ".$code;
        $data["data"]  = $history;

        $view = "pages.material_request.history";
        return view($view, $data);
    }
}
