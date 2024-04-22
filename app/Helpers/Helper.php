<?php

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

use App\Models\MaterialRequest;

function generateCodeDocument($transactionType){
    if($transactionType == "MR"){
        $last_document  = MaterialRequest::where("division_id", 1)->where("date","LIKE","%".date("Y-m")."%")->orderBy("id", "DESC")->first();
    }
    else if($transactionType == "PR"){

    }

    $last_code      = substr($last_document->code, 0, 3);
    $count_string   = strlen($last_code);
    $stringToInt    = ($last_code * 1) + 1;
    $newCountString = strlen($stringToInt);
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

    $newDocumentCode = $new_code."/".$transactionType."/UMUM/".$month."/".date("Y");

    return $newDocumentCode;
}