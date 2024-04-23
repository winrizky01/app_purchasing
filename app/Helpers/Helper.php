<?php

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Models\MaterialRequest;
use App\Models\Division;

function generateCodeDocument($transactionType, $division=false){
    if($division == false){
        $division = 1;
        $division_name = "UMUM";
    }
    else{
        $division = $division;
        $division_name = Division::find($division);
        $division_name = strtoupper($division_name->code);
    }

    if($transactionType == "MR"){
        $last_document  = MaterialRequest::where("division_id", $division);
    }
    else if($transactionType == "PR"){

    }
    $last_document = $last_document->where("date","LIKE","%".date("Y-m")."%")
                    ->orderBy("id", "DESC")
                    ->first();

    $last_code = "000";
    if($last_document){
        $last_code = substr($last_document->code, 0, 3); // ambil 3 digit diawal
    }

    $count_string   = strlen($last_code); // hitung total string
    $stringToInt    = ($last_code * 1) + 1; // tambahkan 1 angka setiap kode akhir
    $newCountString = strlen($stringToInt); // hitung ulang total string
    $new_code       = "";
    for($i=0; $i<($count_string-$newCountString); $i++){
        $new_code = $new_code."0";
    }
    $new_code = $new_code.$stringToInt;

    $month = date("m");
    switch ($month){
        case 1:$month="I";break;
        case 2:$month="II";break;
        case 3:$month="III";break;
        case 4:$month="IV";break;
        case 5:$month="V";break;
        case 6:$month="VI";break;
        case 7:$month="VII";break;
        case 8:$month="VIII";break;
        case 9:$month="IX";break;
        case 10:$month="X";break;
        case 11:$month="XI";break;
        case 12:$month="XII";
    }

    $newDocumentCode = $new_code."/".$transactionType."/".$division_name."/".$month."/".date("Y");

    return $newDocumentCode;
}

function findAllStatusGeneral($param)
{
    /**
     * note :
     * document_status 
     * type_id (for employee type)
     * id
     * 
     */

    $query = DB::table("generals")
                ->select("id","name","extra")
                ->where("status","active")
                ->whereNull("deleted_at");

    if(isset($param["id"])){
        $query->where("id",$param["id"]);
    }

    if(isset($param["name"])){
        $query->where("name",$param["name"]);
    }
    
    if(isset($param["type"])){
        $query->where("type",$param["type"]);
    }

    return $query->first();
}

function pageControl($request){
    $role   = session('role')->name;
    $modul  = DB::table("menu_parents")->where("seo",$request->segments()[0])->get()[0]->id;
    $feature= DB::table("menu_childrens")->where("seo",$request->segments()[1])->get()[0]->id;

    $check_user_role = DB::table("roles")
                        ->join("role_details","role_details.role_id","=","roles.id")
                        ->where("roles.name", $role)
                        ->where("role_details.menu_parent_id", $modul)
                        ->where("role_details.menu_children_id",$feature)
                        ->get();
    if(count($check_user_role) > 0){
        return true;
    }else{
        return false;
    }
}

function accessControl(){}