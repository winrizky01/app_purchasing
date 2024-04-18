<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\ProductMachine;
use App\Models\General;

use DB;
use Redirect;
use DataTables;
use Exception;
use Validator;
use File;

class ProductController extends Controller
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
                $where[] = ["products.id", $request->id];
            }

            $offset = ($page - 1) * $limit;
            
            $query = Product::where($where)->orderBy("products.id", "ASC");
            if ($limit > 0) {
                $query = $query->offset($offset)->limit($limit)->paginate($limit);
            } 
            else {
                $query = $query->get();
            }

            return response()->json([
                        'status' => true,
                        'message'=> "Product successfuly access",
                        'code'   => 200,
                        'results'=> $query
                    ], 200);
        }
        else {
            $data["title"] = "List Product";

            $view = "pages.product.index";
            return view($view, $data);
        }
    }

    public function create(Request $request)
    {        
        $data["title"] = "Add Product";

        $view = "pages.product.create";
        return view($view, $data);
    }

    public function store(Request $request)
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

        $check_product_category = General::find($request->product_category_id);
        if($check_product_category->name == "Sparepart"){
            $validator = Validator::make($request->all(),[
                'dimension'     => 'required',
                'part_number'   => 'required',
                'machine_id'    => 'required',
                'spesification' => 'required',
            ]);
    
            if($validator->fails()){
                return handleErrorResponse($request, 'The following fields are required (Dimension, Part Number, For Machine) !', 'master/product', 404, null);
            }
        }

        DB::beginTransaction();
        try {
            $file  = "";
            $photo = "";
            if ($request->hasFile('media')) {
                $file     = $request->file('media');
                $photo    = str_replace(" ", "-", $file->getClientOriginalName());
            }

            $product = Product::create([
                'product_category_id' => $request->product_category_id,
                'name'          => $request->name,
                'code'          => $request->code,
                'sku'           => $request->sku,
                'unit_id'       => $request->unit_id,
                'is_inventory'  => $request->is_inventory,
                'dimension'     => $request->dimension,
                'part_number'   => $request->part_number,
                "description"   => $request->description,
                "spesification" => $request->spesification,
                "photo"         => $photo !== "" ? 'template/assets/img/products/'.$photo : null,
                "status"        => $request->status,
                "created_at"    => date("Y-m-d H:i:s"),
                "created_by"    => auth()->user()->id,
            ]);

            if($product){
                if($photo != ""){
                    $file->move(public_path('template/assets/img/products/'), $photo);
                }

                if($request->machine_id){
                    foreach($request->machine_id as $machine){
                        ProductMachine::create([
                            'product_id' => $product->id,
                            'machine_id' => $machine,
                            "created_at" => date("Y-m-d H:i:s"),
                        ]);
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
            return redirect()->to('master/product');
        }
    }

    public function edit(Request $request, $id)
    {
        $product = Product::with(['product_unit','product_category','product_machine.machine'])->find($id);
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

        $check_product_category = General::find($request->product_category_id);
        if($check_product_category->name == "Sparepart"){
            $validator = Validator::make($request->all(),[
                'dimension'     => 'required',
                'part_number'   => 'required',
                'machine_id'    => 'required',
                'spesification' => 'required',
            ]);
    
            if($validator->fails()){
                return handleErrorResponse($request, 'The following fields are required (Dimension, Part Number, For Machine) !', 'master/product', 404, null);
            }
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
            $product->description   = $request->description;
            $product->spesification = $request->spesification;
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

            if($request->machine_id){
                ProductMachine::where("product_id", $id)->delete();
                foreach($request->machine_id as $machine){
                    ProductMachine::create([
                        'product_id' => $id,
                        'machine_id' => $machine,
                        "created_at" => date("Y-m-d H:i:s"),
                    ]);
                }
            }

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
        if($request->name != ""){
            $where[] = ["products.name", "LIKE", "%".$request->name."%"];
        }
        if($request->status != ""){
            $where[] = ['products.status', $request->status];
        }
        if($request->product_category_id != ""){
            $where[] = ['products.product_category_id', $request->product_category_id];
        }

        $data = Product::with(['product_unit','product_category','product_machine.machine'])->where($where)->get();
        return datatables()->of($data)->toJson();
    }
}