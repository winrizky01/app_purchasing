<?php

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Models\MaterialRequest;
use App\Models\MaterialRequestDetail;
use App\Models\MRHistory;
use App\Models\MRDHistory;

use App\Models\AdjustmentStock;

use App\Models\MaterialUsage;
use App\Models\MaterialUsageDetail;

use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestDetail;
use App\Models\PRHistory;
use App\Models\PRDHistory;

use App\Models\Division;
use App\Models\Approval;
use App\Models\Product;
use App\Models\ProductStock;

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
        $last_document  = MaterialRequest::where("division_id", $division)->where("request_date","LIKE","%".date("Y-m")."%");
    }
    else if($transactionType == "PR"){
        $last_document  = PurchaseRequest::where("date","LIKE","%".date("Y-m")."%");
        $division_name  = "PROJ";
    }
    else if($transactionType == "ADJ"){
        $last_document  = AdjustmentStock::where("date","LIKE","%".date("Y-m")."%");
    }
    else if($transactionType == "USG"){
        // "USG/MEPPO/ECI/XII/".date("Y");
        $last_document  = MaterialUsage::where("usage_date","LIKE","%".date("Y-m")."%");
        $division_name  = "MEPPO/ECI";
    }
    else if($transactionType == "GR"){
        $last_document  = MaterialUsage::where("usage_date","LIKE","%".date("Y-m")."%");
        $division_name  = "MEPPO/ECI";
    }

    $last_document = $last_document->orderBy("id", "DESC")->first();

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

function approvalTransaction($type_transaction_id, $transaction_id, $document_status_id){
    try{
        $approval = Approval::create([
            "type_transaction_id"   => $type_transaction_id,
            "transaction_id"        => $transaction_id,
            "document_status"       => $document_status_id,
            "user_id"               => auth()->user()->id,
            "date"                  => date("Y-m-d"),
            "created_at"            => date("Y-m-d H:i:s")
        ]);
    }catch(Exception $e){
        return false;
    }
    return true;
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

function transactionHistoryRevision($transactionType, $transaction_id){
    $countRevision = 0;

        if ($transactionType == "MR") {
            $materialRequest = MaterialRequest::find($transaction_id);
            $materialRequestHistory = MRHistory::create([
                'from_material_request_id'  => $transaction_id,
                'type_material_request'     => $materialRequest->material_request_type,
                'code'                      => $materialRequest->code,
                'request_date'              => $materialRequest->request_date,
                'department_id'             => $materialRequest->department_id,
                'division_id'               => $materialRequest->division_id,
                'justification'             => $materialRequest->justification,
                'remark_id'                 => $materialRequest->remark_id,
                'document_photo'            => $materialRequest->document_photo,
                'document_pdf'              => $materialRequest->document_pdf,
                "last_reason"               => $materialRequest->last_reason,
                "revisied_at"               => date("Y-m-d H:i:s"),
                "revisied_by"               => auth()->user()->id,
            ]);
    
            if(!$materialRequestHistory){
                return false;
            }

            $materialRequestDetail = MaterialRequestDetail::where("material_request_id",$transaction_id)->get();
            foreach($materialRequestDetail as $item){
                $materialRequestDetailHistory = MRDHistory::create([
                    'mr_history_id' => $materialRequestHistory->id,
                    'product_id' => $item->product_id,
                    'qty'        => $item->qty,
                    'notes'      => $item->notes,
                ]);

                if(!$materialRequestDetailHistory){
                    return false;
                }
            }
    
            $countRevision = $materialRequest->revision + 1;
        }
        else if ($transactionType == "PR") {
            $purchaseRequest = PurchaseRequest::find($transaction_id);
            $purchaseRequestHistory = PRHistory::create([
                'from_purchase_request_id'  => $transaction_id,
                'type_purchase_request'     => $purchaseRequest->type_purchase_request,
                'code'                      => $purchaseRequest->code,
                'date'                      => $purchaseRequest->date,
                'effective_date'            => $purchaseRequest->effective_date,
                'department_id'             => $purchaseRequest->department_id,
                'division_id'               => $purchaseRequest->division_id,
                'notes'                     => $purchaseRequest->notes,
                'remark_id'                 => $purchaseRequest->remark_id,
                'document_photo'            => $purchaseRequest->document_photo,
                'document_pdf'              => $purchaseRequest->document_pdf,
                "last_reason"               => $purchaseRequest->last_reason,
                "revisied_at"               => date("Y-m-d H:i:s"),
                "revisied_by"               => auth()->user()->id,
            ]);
    
            if(!$purchaseRequestHistory){
                return false;
            }

            $purchaseRequestDetail = PurchaseRequestDetail::where("purchase_request_id",$transaction_id)->get();
            foreach($purchaseRequestDetail as $item){
                $purchaseRequestDetailHistory = PRDHistory::create([
                    'pr_history_id' => $purchaseRequestHistory->id,
                    'product_id' => $item->product_id,
                    'qty'        => $item->qty,
                    'description'=> $item->description,                    
                ]);

                if(!$purchaseRequestDetailHistory){
                    return false;
                }
            }
    
            $countRevision = $purchaseRequest->revision + 1;
        }

    return $countRevision;
}

/**
 * stock history
 * stock warehouse
 * counter stock on hand in product table
 */
function productStock($transactionType, $transaction_id, $warehouse_id, $stock_type_id, $product_id, $qty){
    $warehouse = DB::table("warehouses")->where("id", $warehouse_id)->get()[0];
    $stock_type_name = findAllStatusGeneral(['id'=>$stock_type_id]);
    $productStock = ProductStock::create([
        "date"                => date("Y-m-d"),
        "type_transaction_id" => $transactionType,
        "transaction_id"      => $transaction_id,
        "warehouse_id"        => $warehouse_id,
        "warehouse_type"      => $warehouse->warehouse_type,
        "stock_type_id"       => $stock_type_id,
        "stock_type_name"     => $stock_type_name->name,
        "product_id"          => $product_id,
        "qty"                 => $qty,
        "created_at"          => date("Y-m-d H:i:s"),
        "created_by"          => auth()->user()->id
    ]);

    if(!$productStock){
        return false;
    }

    // recounter stock on hand in product table
    $product = Product::find($product_id);
    $current_stock = $product->stock;
    if($stock_type_name->name == "IN"){
        $new_stock = ($current_stock * 1) + ($qty * 1);
    }
    else{
        $new_stock = ($current_stock * 1) - ($qty * 1);

        if($new_stock < 0){
            return false;
        }
    }
    $product->stock = $new_stock;
    $product->save();
    if(!$product){
        return false;
    }

    return true;
}

/**
 * option_warehouse (
 * global => stok keseleruhunan (default query)
 * general=> stok yang bisa digunakan
 * extra-countable => stok yang belum tentu bisa digunakan
 * ) -> parsing paramater ini secara hardcode karena
 * pada dasarnya sudah default di master warehouse
 */
function checkStock($warehouse_id, $product_id, $option_warehouse=false){
    $start_date = date("Y-m-01");
    $end_date   = date("Y-m-d");
    $periode    = date("Y-m");

    $query = DB::table('products as p')
                ->select(
                    'p.id as product_id', 
                    'p.name as product_name',
                    DB::raw('(COALESCE(SUM(cs.closing_quantity), 0) + 
                        COALESCE(SUM(CASE WHEN ts.stock_type_name = "IN" THEN ts.qty ELSE 0 END), 0) - 
                        COALESCE(SUM(CASE WHEN ts.stock_type_name = "OUT" THEN ts.qty ELSE 0 END), 0)
                    ) AS final_stock')
                )
                ->leftJoin('product_stocks as ts', function($j) use ($start_date, $end_date, $warehouse_id, $option_warehouse){
                    // global stok
                    $j->on('p.id', '=', 'ts.product_id')
                        ->whereBetween('ts.date',[$start_date, $end_date]);
                    // global stok

                    if($warehouse_id != ""){
                        $j->where("ts.warehouse_id", $warehouse_id);
                    }

                    if($option_warehouse != false){
                        $j->where("ts.warehouse_type", $option_warehouse);
                    }
                })
                ->leftJoin('closing_stocks as cs', function($j) use ($periode, $warehouse_id, $option_warehouse){
                    // global stok
                    $j->on('p.id', '=', 'cs.product_id')
                        ->where('cs.period', '=', $periode);
                    // global stok

                    if($warehouse_id != ""){
                        $j->where("cs.warehouse_id", $warehouse_id);
                    }

                    if($option_warehouse != false){
                        $j->where("cs.warehouse_type", $option_warehouse);
                    }
                })
                ->where("p.id", $product_id)
                ->groupBy('p.id','p.name')
                ->get();

    return $query;
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

/**
 * change status closed if
 * transaction has been
 * processed  
 */
function closingTransaction($transactionType, $transaction_id, $old_transaction_id){}

function accessControl(){}