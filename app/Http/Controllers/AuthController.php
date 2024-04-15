<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Password;

use App\Models\User;

use Redirect;
use DB;

class AuthController extends Controller
{
    public function login()
    {
        $data["title"] = "Our Catalogue";

        $view = "pages.auth.login";
        return view($view, $data);
    }

    public function login_process(Request $request)
    {
        $user = Auth::getProvider()->retrieveByCredentials($request->only('email', 'password'));    
        
        if(!$user){
            return handleErrorResponse($request, 'Login failed.', 'login', 401, null);
        }

        if($user->status == "inactive"){
            return handleErrorResponse($request, 'Login failed.', 'login', 401, null);
        }

        Auth::login($user);

        return handleAuthResponse($request, 'Login Success', '/', 200, $user);
    }

    public function forgot_password()
    {
        $data["title"] = "Our Catalogue";

        $view = "pages.auth.forgotpassword";
        return view($view, $data);
    }

    public function forgot_password_process(Request $request)
    {}

    public function reset_password()
    {
        $data["title"] = "Our Catalogue";

        $view = "pages.auth.resetpassword";
        return view($view, $data);
    }

    public function reset_password_process(Request $request)
    {}

    public function register()
    {
        User::create([
            "name"      => "development",
            "email"     => "development@gmail.com",
            "password"  => bcrypt("12345678"),
            "status"    => "active",
            'created_at'=> date('Y-m-d H:i:s'),
        ]);
    }

    public function logout(Request $request){
        Session::flush();

        Auth::guard('web')->logout();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Logout Success'], 200);
        } else {
            return redirect('/login')->with('success', 'Logout Success');
        }
    }
}
