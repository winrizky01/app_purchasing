<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\ProductMachine;
use App\Models\General;
use App\Models\ProductStock;

use DB;
use Redirect;
use DataTables;
use Exception;
use Validator;
use File;

class ReportProductController extends Controller
{
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
            $data["title"] = "Report Stock Product";

            $view = "pages.product_stock.index";
            return view($view, $data);
        }

    }

    public function dataTables(Request $request){
        //default
        $start_date = date("Y-m-01");
        $end_date   = date("Y-m-d");
        $periode    = date("Y-m");
        //default
        if($request->date != ""){
            $dateRange = explode(" to ", $request->date);

            $periode = date("Y-m", strtotime($dateRange[0]));
            if(count($dateRange) > 1){
                $start_date = date("Y-m-d", strtotime($dateRange[0]));
                $end_date   = date("Y-m-d", strtotime($dateRange[1]));
            }
            else{
                $start_date = date("Y-m-01", strtotime($dateRange[0]));
                $end_date   = date("Y-m-d", strtotime($dateRange[0]));
            }
        }

        if($request->option == "summary"){
            $query = DB::table('products as p')
                        ->select(
                            'p.id as product_id', 
                            'p.code as product_code', 
                            'p.name as product_name',
                            DB::raw('COALESCE(SUM(cs.closing_quantity), 0) AS total_stock_init'),
                            DB::raw('COALESCE(SUM(CASE WHEN ts.stock_type_name = "IN" THEN ts.qty ELSE 0 END), 0) AS total_stock_in'),
                            DB::raw('COALESCE(SUM(CASE WHEN ts.stock_type_name = "OUT" THEN ts.qty ELSE 0 END), 0) AS total_stock_out'),
                            DB::raw('(COALESCE(SUM(cs.closing_quantity), 0) + 
                                COALESCE(SUM(CASE WHEN ts.stock_type_name = "IN" THEN ts.qty ELSE 0 END), 0) - 
                                COALESCE(SUM(CASE WHEN ts.stock_type_name = "OUT" THEN ts.qty ELSE 0 END), 0)
                            ) AS final_stock')
                        )
                        ->leftJoin('product_stocks as ts', function($j) use ($start_date, $end_date, $request){
                            if($request->warehouse_id != ""){
                                $j->on('p.id', '=', 'ts.product_id')
                                    ->whereBetween('ts.date',[$start_date, $end_date])
                                    ->where("ts.warehouse_id", $request->warehouse_id);
                            }
                            else{
                                $j->on('p.id', '=', 'ts.product_id')
                                    ->whereBetween('ts.date',[$start_date, $end_date]);
                            }
                        })
                        ->leftJoin('closing_stocks as cs', function($j) use ($periode, $request){
                            if($request->warehouse_id != ""){
                                $j->on('p.id', '=', 'cs.product_id')
                                    ->where('cs.period', '=', $periode)
                                    ->where("cs.warehouse_id", $request->warehouse_id);
                            }
                            else{
                                $j->on('p.id', '=', 'cs.product_id')
                                    ->where('cs.period', '=', $periode);
                            }
                        });
        }
        else if($request->option == "Detail"){}  

        if($request->product_id != ""){
            $query = $query->whereIn("p.id", $request->product_id);
        }

        $query = $query->groupBy('p.id','p.code','p.name')->get();

        return datatables()->of($query)->toJson();
    }
}
