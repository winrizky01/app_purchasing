<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Models\General;
use App\Models\Vendor;

use DB;
use Redirect;
use DataTables;
use Exception;
use Validator;

class VendorController extends Controller
{
    public function select(Request $request)
    {
        $query = Vendor::select(["id", "name", "name as text"]);
        $query = $query->get();

        if($request->expectsJson() || $request->ajax()){
            return response()->json([
                'status' => true,
                'message'=> "Vendor successfuly access",
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
                $where[] = ["vendors.id", $request->id];
            }

            $offset = ($page - 1) * $limit;
            
            $query = Vendor::where($where)->orderBy("vendors.id", "ASC");
            if ($limit > 0) {
                $query = $query->offset($offset)->limit($limit)->paginate($limit);
            } 
            else {
                $query = $query->get();
            }

            return response()->json([
                        'status' => true,
                        'message'=> "Vendor successfuly access",
                        'code'   => 200,
                        'results'=> $query
                    ], 200);
        }
        else {
            $data["title"] = "List Vendor";

            $view = "pages.vendor.index";
            return view($view, $data);
        }
    }

    public function create(Request $request)
    {
        $vendor = Vendor::where("code", "LIKE", "%VEN%")->orderBy("created_at", "DESC")->first();
        $last_code = "0000";
        if($vendor){
            $last_code = substr($vendor->code, -4); // ambil 4 digit diakhir
        }    
        $count_string   = strlen($last_code); // hitung total string
        $stringToInt    = ($last_code * 1) + 1; // tambahkan 1 angka setiap kode akhir
        $newCountString = strlen($stringToInt); // hitung ulang total string
        $new_code       = "";
        for($i=0; $i<($count_string-$newCountString); $i++){
            $new_code = $new_code."0";
        }
        $new_code = $new_code.$stringToInt;

        $data["title"] = "Add Vendor";        
        $data["code"] = "VEN-".$new_code;

        $view = "pages.vendor.create";
        return view($view, $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'code'  => 'required',
            'name'  => 'required',
            'email' => 'required',
            'payment_terms_id'      => 'required',
            'contact_person'        => 'required',
            'contact_person_number' => 'required',
            'payment_method_id'     => 'required',
            'status'=> 'required'
        ]);

        if($validator->fails()){
            return handleErrorResponse($request, 'The following fields are required !', 'master/vendor', 404, null);
        }

        DB::beginTransaction();
        try {
            $file  = "";
            $photo = "";
            if ($request->hasFile('media')) {
                $file     = $request->file('media');
                $photo    = str_replace(" ", "-", $file->getClientOriginalName());
            }

            $vendor = Vendor::create([
                "code"              => $request->name,
                "name"              => $request->name,
                "email"             => $request->email,
                "tax"               => $request->tax,
                "npwp"               => $request->npwp,
                "contact_person"    => $request->contact_person,
                "contact_person_number"=> $request->contact_person_number,
                "address"           => $request->address,
                "photo"             => $photo !== "" ? 'template/assets/img/vendors/'.$photo : null,
                "description"       => $request->description,
                "bank_account_id"   => $request->bank_account_id,
                "bank_account_number"=> $request->bank_account_number,
                "payment_method_id" => $request->payment_method_id,
                "payment_terms_id"  => $request->payment_terms_id,
                "status"            => $request->status,
                "created_at"        => date("Y-m-d H:i:s"),
                "created_by"        => auth()->user()->id,
            ]);

            if($vendor){
                if($photo != ""){
                    $file->move(public_path('template/assets/img/vendors/'), $photo);
                }
            }
        } catch (Exception $e) {
            DB::rollback();
            return handleErrorResponse($request, $e->getMessage(), 'master/vendor', 404, null);
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
            return redirect()->to('master/vendor');
        }
    }

    public function edit(Request $request, $id)
    {
        $vendor = Vendor::find($id);
        if(!$vendor){
            return handleErrorResponse($request, 'Opps, data not found!', 'master/vendor', 404, null);
        }

        if($request->expectsJson())
        {
            return response()->json([
                'status' => true,
                'message'=> "Data found.",
                'code'   => 200,
                'results'=> $vendor
            ], 200);
        }
        else{
            $data["title"] = "Edit Vendor";
            $data["data"]  = $vendor;

            $view = "pages.vendor.edit";
            
            return view($view, $data);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'code'  => 'required',
            'name'  => 'required',
            'email' => 'required',
            'payment_terms_id'      => 'required',
            'contact_person'        => 'required',
            'contact_person_number' => 'required',
            'payment_method_id'     => 'required',
            'status'=> 'required'
        ]);

        if($validator->fails()){
            return handleErrorResponse($request, 'The following fields are required !', 'master/vendor', 404, null);
        }

        $vendor = Vendor::find($id);
        if(!$vendor){
            return handleErrorResponse($request, 'Opps, data not found!', 'master/vendor', 404, null);
        }

        DB::beginTransaction();
        try {
            $vendor->code   = $request->name;
            $vendor->name   = $request->name;
            $vendor->email  = $request->email;
            $vendor->tax    = $request->tax;
            $vendor->npwp   = $request->npwp;
            $vendor->contact_person         = $request->contact_person;
            $vendor->contact_person_number  = $request->contact_person_number;
            $vendor->address                = $request->address;
            $vendor->description            = $request->description;
            $vendor->bank_account_id        = $request->bank_account_id;
            $vendor->bank_account_number    = $request->bank_account_number;
            $vendor->payment_method_id      = $request->payment_method_id;
            $vendor->payment_terms_id       = $request->payment_terms_id;
            $vendor->status     = $request->status;
            $vendor->updated_at = date("Y-m-d H:i:s");
            $vendor->updated_by = auth()->user()->id;

            if ($request->hasFile('media')) {
                $file   = $request->file('media');
                $photo  = str_replace(" ", "-", $file->getClientOriginalName());
                $vendor->photo = $photo !== "" ? 'template/assets/img/vendors/'.$photo : null;
                $file->move(public_path('template/assets/img/vendors/'), $photo);
            }

            $vendor->save();
        }
        catch (Exception $e) {
            DB::rollback();
            return handleErrorResponse($request, $e->getMessage(), 'master/vendor', 404, null);
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
            return redirect()->to('master/vendor');
        }
    }

    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        $vendor = Vendor::find($id);
        if(!$vendor){
            return handleErrorResponse($request, 'Opps, data not found.', 'master/vendor', 404, null);
        }

        try {
            $vendor->status     = "inactive";
            $vendor->deleted_at = date("Y-m-d H:i:s");
            $vendor->deleted_by = auth()->user()->id;
            $vendor->save();
            $vendor->delete();
        } catch (Exception $e) {
            DB::rollBack();
            return handleErrorResponse($request, 'Opps, data failed to delete.', 'master/vendor', 404, null);
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
            return redirect()->to('master/vendor');
        }
    }

    public function dataTables(Request $request)
    {
        $where = [];
        if($request->name != ""){
            $where[] = ["vendors.name", "LIKE", "%".$request->name."%"];
        }
        if($request->contact_person != ""){
            $where[] = ["vendors.contact_person", "LIKE", "%".$request->contact_person."%"];
        }
        if($request->status != ""){
            $where[] = ['vendors.status', $request->status];
        }

        $data = Vendor::where($where)->get();
        return datatables()->of($data)->toJson();
    }
}
