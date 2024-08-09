<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminLoginController extends Controller
{
    public function index(){
        return view('admin.login');
    }

    public function authenticate(REQUEST $request){
        $validator = Validator::make($request->all(),[
            'email'=>'required|email',
            'password'=>'required',
        ]);
        if($validator->passes()){
            if(Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember'))){
                // checking for if the user is admin or not
                $admin = Auth::guard('admin')->user();
                if($admin->role == 2){ // we had put value of 2 for admin and 1 for client
                    return redirect()->route('admin.dashboard');
                }else{
                    Auth::guard('admin')->logout(); // this is for destroying the session that is created
                    return redirect()->route('admin.login')->with('error', 'You are not Me!');
                }
            }else{
                return redirect()->route('admin.login')->with('error', 'Either Email/Password is incorrect');
            }
        }else{
            return redirect()->route('admin.login')->withErrors($validator)->withInput($request->only('email'));
        }
    }
}
