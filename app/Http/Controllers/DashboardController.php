<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request){
        $data["title"] = "Our Catalogue";
        $data["breadcrumbs"] = "";

        $view = "pages.dashboard";
        return view($view, $data);
    }
}
