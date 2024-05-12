<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestDetail;
use App\Models\PurchaseRequestRevision;
use App\Models\PRHistory;
use App\Models\PRDHistory;
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
    protected $type_transaction_id;

    public function __construct()
    {
        $this->type_transaction_id = findAllStatusGeneral(["name"=>"PR"]);
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
        $data["document_number"] = generateCodeDocument("PR",null);
        $view = "pages.purchase_request.create";
        return view($view, $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'code'                => 'required',
            'effective_date'      => 'required',
            'department_id'       => 'required',
            'division_id'         => 'required',
            'warehouse_id'        => 'required',
            'document_status_id'  => 'required',
        ]);

        if($validator->fails()){
            return handleErrorResponse($request, 'The following fields are required !', 'purchasing/purchase-request', 404, null);
        }

        DB::beginTransaction();
        try {
            $file  = "";
            $photo = "";
            if ($request->hasFile('media')) {
                $filephoto= $request->file('document_photo');
                $photo    = str_replace(" ", "-", $filephoto->getClientOriginalName());
                $filephoto->move(public_path('template/assets/purchase_request/'), $photo);
            }

            $filepdf  = "";
            $pdf = "";
            if ($request->hasFile('document_pdf')) {
                $filepdf= $request->file('document_pdf');
                $pdf    = str_replace(" ", "-", $filepdf->getClientOriginalName());
                $filepdf->move(public_path('template/assets/purchase_request/'), $pdf);
            }

            $newDocumentStatus = findAllStatusGeneral(["name"=>"Waiting Approval Tech Support"]);
            $getDocumentStatus = findAllStatusGeneral(["id"=>$request->document_status_id]);
            $doc_status = $getDocumentStatus->id;
            if($getDocumentStatus->name == "Submit"){
                $doc_status = $newDocumentStatus->id;
            }

            $purchaseRequest = PurchaseRequest::create([
                'code'                  => $request->code,
                'type_purchase_request' => $request->purchase_type_id,
                'date'                  => date("Y-m-d"),
                'effective_date'        => date("Y-m-d H:i:s", strtotime($request->effective_date)),
                'department_id'         => $request->department_id,
                'division_id'           => $request->division_id,
                'warehouse_id'          => $request->warehouse_id,
                'remark_id'             => $request->remark_id,
                'document_status_id'    => $doc_status,
                'document_photo'        => $photo !== "" ? 'template/assets/purchase_request/'.$photo : null,
                'document_pdf'          => $pdf !== "" ? 'template/assets/purhcase_request/'.$pdf : null,
                'notes'                 => $request->description,
                'revision'              => 0,
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
                            // $identity_required_date        = null;    
                        }
                        else{
                            $product_id                 = $value["product_id"];
                            $qty                        = $value["qty"];
                            $description                = $value["product_note"];
                            // $identity_required_date     = $value["identity_required_date"];
                        }

                        $purchaseRequestDetail = PurchaseRequestDetail::create([
                            'purchase_request_id'       => $purchaseRequest->id,
                            'product_id'                => $product_id,
                            'qty'                       => $qty,
                            'description'               => $description,
                            // 'identity_required_date'    => $request->effective_date,
                            'document_status_id'        => 1,
                        ]);

                        if (!$purchaseRequestDetail) {
                            DB::rollback();
                            return handleErrorResponse($request, "Opps, data failed created purchase request details", 'purchasing/purchase-request', 404, null);
                        }
                    }
                }

                $getDocumentStatus = findAllStatusGeneral(["id"=>$request->document_status_id]);
                if($getDocumentStatus->name == "Submit"){
                    $approval = approvalTransaction($this->type_transaction_id, $purchaseRequest->id, $newDocumentStatus->id);
                    if($approval == false){
                        DB::rollback();
                        return handleErrorResponse($request, "Opps, error approval data", 'purchasing/purchase-request', 404, null);
                    }
                }
            }
        } catch (Exception $e) {
            DB::rollback();
            var_dump($e->getMessage());die();
            //return handleErrorResponse($request, $e->getMessage(), 'purchasing/purchase-request', 404, null);
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

        $purchaseRequest = PurchaseRequest::with([
                'detail',
                'detail.product',
                'department',
                'document_status',
                'createdBy',
                'last_update'
            ])->find($id);
        if(!$purchaseRequest){
            return handleErrorResponse($request, 'Opps, data not found!', 'purchasing/purchase_request', 404, null);
        }

        $getDocumentStatus = findAllStatusGeneral(["id"=>$purchaseRequest->document_status_id]);
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
            $data["title"] = "Edit Purchase Request";
            $data["data"]  = $purchaseRequest;
            $data["mode"]  = "edit";

            $view = "pages.purchase_request.edit";
            
            return view($view, $data);
        }
    }

    public function show(Request $request, $id)
    {
        // page control
        if(!pageControl($request)){
            return redirect('/');
        }            
        
        $purchaseRequest = PurchaseRequest::with([
                'detail',
                'detail.product',
                'department',
                'document_status',
                'createdBy',
                'last_update'
            ])
            ->find($id);
        if(!$purchaseRequest){
            return handleErrorResponse($request, 'Opps, data not found!', 'purchasing/purchase-request', 404, null);
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
            $data["title"] = "Show Purchase Request";
            $data["data"]  = $purchaseRequest;
            $data["mode"]  = "show";
            
            $view = "pages.purchase_request.edit";
            
            return view($view, $data);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'code'                => 'required',
            'effective_date'      => 'required',
            'department_id'       => 'required',
            'division_id'         => 'required',
            'warehouse_id'        => 'required',
            'document_status_id'  => 'required',
        ]);

        if($validator->fails()){
            return handleErrorResponse($request, 'The following fields are required !', 'purchasing/purchase-request', 404, null);
        }

        $purchaseRequest = PurchaseRequest::find($id);
        if(!$purchaseRequest){
            return handleErrorResponse($request, 'Opps, data not found!', 'purchasing/purchase-request', 404, null);
        }

        // akses
        $getDocumentStatus = findAllStatusGeneral(["id"=>$purchaseRequest->document_status_id]);
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
            return handleErrorResponse($request, "Opps, error approval data!", 'purchasing/purchase-request', 404, null);
        }
        // akses

        DB::beginTransaction();
        try {
            $revision = $purchaseRequest->revision; // nilai revisi ke
            // tambahkan histori revisi jika user melakukan revisi
            if($request->isChange == "true"){
                $addRevision = transactionHistoryRevision("PR", $id);
                if($addRevision == false){
                    return handleErrorResponse($request, "Opps, error approval data!", 'purchasing/purchase-request', 404, null);
                }
                $revision = $addRevision;
            }
            // tambahkan histori revisi jika user melakukan revisi

            $filephoto  = "";
            $photo = "";
            if ($request->hasFile('document_photo')) {
                $filephoto= $request->file('document_photo');
                $photo    = str_replace(" ", "-", $filephoto->getClientOriginalName());
                $filephoto->move(public_path('template/assets/purchase_request/'), $photo);
            }

            $filepdf  = "";
            $pdf = "";
            if ($request->hasFile('document_pdf')) {
                $filepdf= $request->file('document_pdf');
                $pdf    = str_replace(" ", "-", $filepdf->getClientOriginalName());
                $filepdf->move(public_path('template/assets/purchase_request/'), $pdf);
            }

            $purchaseRequest->effective_date      = date("Y-m-d H:i:s", strtotime($request->effective_date));
            $purchaseRequest->department_id       = $request->department_id;
            $purchaseRequest->division_id         = $request->division_id;
            $purchaseRequest->warehouse_id        = $request->warehouse_id;
            $purchaseRequest->remark_id           = $request->remark_id;
            $purchaseRequest->document_photo      = $photo !== "" ? 'template/assets/purchase_request/'.$photo : null;
            $purchaseRequest->document_pdf        = $pdf !== "" ? 'template/assets/purchase_request/'.$pdf : null;
            $purchaseRequest->document_status_id  = $newDocumentStatus->id;
            $purchaseRequest->notes               = $request->description;
            $purchaseRequest->revision            = $revision;
            $purchaseRequest->updated_at          = date("Y-m-d H:i:s");
            $purchaseRequest->updated_by          = auth()->user()->id;
            $purchaseRequest->save();

            if($purchaseRequest){
                // regenerate new
                if ($request->purchase_request_details) {
                    // delete all 
                    $purchaseRequestDetail = PurchaseRequestDetail::where("purchase_request_id", $id)->forceDelete();

                    foreach ($request->purchase_request_details as $key => $value) {
                        if($request->expectsJson()){
                            $product_id = null;
                            $qty        = null;
                            $notes      = null;    
                        }
                        else{
                            $product_id = $value["product_id"];
                            $qty        = $value["qty"];
                            $notes      = $value["product_note"];
                        }

                        $purchaseRequestDetail = PurchaseRequestDetail::create([
                            'product_id' => $product_id,
                            'qty'        => $qty,
                            'description'=> $notes,
                            'purchase_request_id'=> $id,
                            'document_status_id' => 1 // default status before used
                        ]);

                        if (!$purchaseRequestDetail) {
                            DB::rollback();
                            return handleErrorResponse($request, "Opps, data failed created purchase request details", 'purchasing/purchase-request', 404, null);
                        }
                    }
                }
            }

            // tambahkan approval untuk pihak tech support di db 
            // tapi tidak perlu ditampilkan di frontend
            if($getDocumentStatus == "Waiting Approval Tech Support"){
                $sid = findAllStatusGeneral(["name"=>"Approved Tech Support"]);
                $app = approvalTransaction($this->type_transaction_id, $purchaseRequest->id, $sid->id);
            }
            // tambahkan approval untuk pihak tech support di db 
            // tapi tidak perlu ditampilkan di frontend

            $approval = approvalTransaction($this->type_transaction_id, $purchaseRequest->id, $newDocumentStatus->id);
            if($approval == false){
                DB::rollback();
                return handleErrorResponse($request, "Opps, error approval data", 'purchasing/purchase-request', 404, null);
            }
        }
        catch (Exception $e) {
            DB::rollback();
            return handleErrorResponse($request, $e->getMessage(), 'purchasing/purchase-request', 404, null);
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
            return redirect()->to('purchasing/purchase-request');
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

    /**
     * permintaan pembatalan data
     */
    public function reject(Request $request, $id)
    {
        var_dump($request->all(), $id);die();

        // page control
        if(!pageControl($request)){
            return redirect('/');
        }            
        
        $validator = Validator::make($request->all(),[
            'reason' => 'required',
        ]);
        if($validator->fails()){
            return handleErrorResponse($request, 'The following fields are required !', 'purchasing/purchase-request', 404, null);
        }

        DB::beginTransaction();
        $purchaseRequest = PurchaseRequest::find($id);
        if(!$purchaseRequest){
            return handleErrorResponse($request, 'Opps, data not found!', 'purchasing/purchase-request', 404, null);
        }

        $getDocumentStatus = findAllStatusGeneral(["id"=>$purchaseRequest->document_status_id]);
        if($getDocumentStatus->name == "Waiting Approval Tech Support"){
            $newDocumentStatus = findAllStatusGeneral(["name"=>"Rejected Tech Support"]);
        }
        else if($getDocumentStatus->name == "Waiting Approval Plant Manager"){
            $newDocumentStatus = findAllStatusGeneral(["name"=>"Rejected Plant Manager"]);
        }
        try {
            $purchaseRequest->document_status_id = $newDocumentStatus->id;
            $purchaseRequest->last_reason= $request->reason;
            $purchaseRequest->updated_at = date("Y-m-d H:i:s");
            $purchaseRequest->updated_by = auth()->user()->id;
            $purchaseRequest->save();

            if(!$purchaseRequest){
                return handleErrorResponse($request, 'Opps, error purchase request.', 'purchasing/purchase-request', 404, null);
            }

            $approval = approvalTransaction($this->type_transaction_id, $purchaseRequest->id, $newDocumentStatus->id);
            if($approval == false){
                DB::rollback();
                return handleErrorResponse($request, "Opps, error approval data", 'purchasing/purchase-request', 404, null);
            }

        } catch (Exception $e) {
            DB::rollBack();
            return handleErrorResponse($request, $e->getMessage(), 'purchasing/purchase-request', 404, null);
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
            return redirect()->to('purchasing/purchase-request');
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
            return handleErrorResponse($request, 'The following fields are required !', 'purchasing/purchase-request', 404, null);
        }

        DB::beginTransaction();
        $purhcaseRequest = PurchaseRequest::find($id);
        if(!$purhcaseRequest){
            return handleErrorResponse($request, 'Opps, data not found!', 'purchasing/purchase-request', 404, null);
        }

        $getDocumentStatus = findAllStatusGeneral(["name"=>"Revisied Plant Manager"]);
        $getDocumentStatus = $getDocumentStatus->id;

        try {
            $purhcaseRequest->document_status_id = $getDocumentStatus;
            $purhcaseRequest->last_reason = $request->reason;
            $purhcaseRequest->updated_at = date("Y-m-d H:i:s");
            $purhcaseRequest->updated_by = auth()->user()->id;
            $purhcaseRequest->save();

            $revision = PurchaseRequestRevision::create([
                "purchase_request_id" => $purhcaseRequest->id,
                "reasons"       => $request->reason,
                "user_id"       => auth()->user()->id,
                "date"          => date("Y-m-d"),
                "created_at"    => date("Y-m-d H:i:s"),
                "created_by"    => auth()->user()->id,
            ]);
            if(!$revision){
                DB::rollback();
                return handleErrorResponse($request, "Opps, error created purchase request revision data", 'purchasing/purchase-request', 404, null);
            }

            $approval = approvalTransaction($this->type_transaction_id, $purhcaseRequest->id, $getDocumentStatus);
            if($approval == false){
                DB::rollback();
                return handleErrorResponse($request, "Opps, error approval data", 'purchasing/purchase-request', 404, null);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return handleErrorResponse($request, $e->getMessage(), 'purchasing/purchase-request', 404, null);
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
            return redirect()->to('purchasing/purchase-request');
        }
    }

    public function history(Request $request, $id){
        $purchaseRequest = PurchaseRequest::find($id);
        $code = $purchaseRequest->code;

        $history = PRHistory::with([
                            'material_type',
                            'remark', 
                            'revisiedBy', 
                            'detail', 
                            'detail.product', 
                            'detail.product.product_category', 
                            'detail.product.product_unit'
                        ])
                        ->where("from_purchase_request_id",$id)
                        ->get();
        $data["title"] = "History Revision Purchase Request - ".$code;
        $data["data"]  = $history;

        $view = "pages.purchase_request.history";
        return view($view, $data);
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
            $where[] = ["purchase_requests.code", "LIKE", "%".$request->code."%"];
        }
        if($request->status != ""){
            $where[] = ['purchase_requests.document_status_id', $request->status];
        }

        $data = PurchaseRequest::with([
                                    'detail',
                                    'detail.product',
                                    'detail.product.product_category',
                                    'detail.product.product_unit',
                                    'department',
                                    'division',
                                    'warehouse',
                                    'document_status'
                                ])
                                ->where($where)
                                ->get();
        return datatables()->of($data)->toJson();
    }

    public function print(Request $request, $id)
    {}
}
