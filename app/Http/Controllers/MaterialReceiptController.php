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
use App\Models\Approval;

use DB;
use Redirect;
use DataTables;
use Exception;
use Validator;
use File;
use PDF;

class MaterialReceiptController extends Controller
{
    protected $type_transaction_id;

    public function __construct()
    {
        $this->type_transaction_id = findAllStatusGeneral(["name"=>"REC"]);
        $this->type_transaction_id = $this->type_transaction_id->id;
    }

    public function index(Request $request){
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
                        'message'=> "Material Receipt successfuly access",
                        'code'   => 200,
                        'results'=> $query
                    ], 200);
        }
        else {
            // page control
            if(!pageControl($request)){
                return redirect('/');
            }
            $data["title"] = "List Material Receipt";

            $view = "pages.material_receipt.index";
            return view($view, $data);
        }
    }

    public function create(Request $request){
        $check_role = Role::find(auth()->user()->role);
        if(($check_role->name !== "Superadmin")){
            return handleErrorResponse($request, 'Opps, sorry you dont have access!', 'inventory/material-receipt', 404, null);
        }

        if($request->expectsJson() || $request->ajax()){
            return response()->json([
                'status' => true,
                'message'=> "Material Receipt successfuly access",
                'code'   => 200,
                'results'=> ["code"=>generateCodeDocument("REC",auth()->user()->division_id)]
            ], 200);
        }
        else{
            // page control
            if(!pageControl($request)){
                return redirect('/');
            }            

            $data["title"] = "Add Material Receipt";
            $data["document_number"] = generateCodeDocument("MR",auth()->user()->division_id);
            $view = "pages.material_receipt.create";
            return view($view, $data);
        }

    }

    public function store(Request $request){}

    public function edit(Request $request){}

    public function update(Request $request){}

    public function destroy(Request $request){}

    public function dataTables(Request $request){}

    public function print(Request $request){}

}