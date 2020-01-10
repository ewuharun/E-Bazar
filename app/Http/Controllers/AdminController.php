<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Session;
use App\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\User;


class AdminController extends Controller
{
    public function login(Request $request){
        if($request->isMethod('post')){
            $credentials = $request->only('email', 'password');
            Session::put('adminSession',$credentials['email']);

            if (Auth::attempt($credentials)) {
                // Authentication passed...
                //return redirect()->intended('dashboard');
                
                return redirect('admin/dashboard');
                die;
            }
            else{
                return redirect('/admin')->with('flash_message_error','Invalid Username or Password');
                die;
            }
    	}
    	return view('admin.admin_login');

    }

    public function dashboard(){
        if(Session::has('adminSession')){
            //perform all dashboard task
        }else{
            return redirect('/admin')->with('flash_message_error','Please login to access');
        }
        return view('admin.dashboard');
    }

    public function logout(){
        Session::flush();
        return redirect('/admin')->with('flash_message_success', 'Logged out successfully.');
       
    }

    public function settings(){

       

        //echo "<pre>"; print_r($adminDetails); die;

        return view('admin.settings');
    }

    public function chkPassword(Request $request){
        $data = $request->all();
        //echo "<pre>"; print_r($data); die;
        $current_password=$data['current_pwd'];
        $check_password=User::where(['admin'=>'1'])->first();
        if(Hash::check($current_password,$check_password->password)){
            echo "true";die;
        }
        else{
            echo "false";die;
        }

    }

    public function updatePassword(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();
            //echo "<pre>"; print_r($data); die;
            $password = Hash::make($data['new_pwd']);
            //dd($password);
            $query=DB::table('users')
            ->where('email', Session::get('adminSession'))
            ->update(['password' => $password]);
            if($query){
                return redirect('/admin/settings')->with('flash_message_success', 'Password updated successfully.');
            }
            else{
                return redirect('/admin/settings')->with('flash_message_error', 'Current Password entered is incorrect.');
            }

            
        }
    }
    





}
