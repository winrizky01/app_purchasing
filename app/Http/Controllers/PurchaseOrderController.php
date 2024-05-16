<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Models\User;
use App\Models\Role;
use App\Models\General;
use App\Models\Product;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestDetail;


use DB;
use Redirect;
use DataTables;
use Exception;
use Validator;
use File;
use PDF;

class PurchaseOrderController extends Controller
{
    protected $type_transaction_id;

    public function __construct()
    {
        $this->type_transaction_id = findAllStatusGeneral(["name"=>"PO"]);
        $this->type_transaction_id = $this->type_transaction_id->id;
    }

    public function select(Request $request)
    {
    }

    public function index(Request $request)
    {
        if ($request->expectsJson()) {
        }
        else {
            $data["title"] = "List Purchase Order";

            $view = "pages.purchase_order.index";
            return view($view, $data);
        }
    }

    public function create(Request $request)
    {
        $check_role = Role::find(auth()->user()->role);
        if(($check_role->name !== "Superadmin")&&($check_role->name !== "Administrator Inventory")){
            return handleErrorResponse($request, 'Opps, sorry you dont have access!', 'purchasing/purchase-request', 404, null);
        }

        $data["title"] = "Add Purchase Order";
        // $data["document_number"] = generateCodeDocument("PO",null);
        $data["document_number"] = "012/PO/MEPPO/V/2024";
        $view = "pages.purchase_order.create";
        return view($view, $data);
    }

    public function store(Request $request)
    {
        var_dump($request->all());die();
        $validator = Validator::make($request->all(),[
            'code'                => 'required',
            'effective_date'      => 'required',
            'max_date_delivery'   => 'required',
            'department_id'       => 'required',
            'division_id'         => 'required',
            'warehouse_id'        => 'required',
            'remark_id'           => 'required',
            'document_status_id'  => 'required',
        ]);

        if($validator->fails()){
            return handleErrorResponse($request, 'The following fields are required !', 'purchasing/purchase-order', 404, null);
        }

        try {
            //code...
        } catch (Exception $e) {
            //throw $th;
        }

    }

    public function edit(Request $request, $id)
    {}

    public function show(Request $request, $id)
    {}

    public function update(Request $request, $id)
    {}

    public function destroy(Request $request, $id)
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

    public function history(Request $request, $id)
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
    {
    }
}
