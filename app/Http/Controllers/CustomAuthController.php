<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\CarService;
use App\Models\BlockUser;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Hash;

class CustomAuthController extends Controller
{


    public function home(){
        $car_list=CarService ::paginate(6);
        return view('home')->with('car_lists',$car_list);
    }
    public function login(){

        return view('auth.login');
    }
    public function registration(){
        return view('auth.registration');
    }
    public function registerUser(Request $request){
        $request->validate([
          'role'=>'required',
          'first_name'=>'required',
          'last_name'=>'required',
          'username'=>'required|unique:users',
          'email'=>'required|email|unique:users',
          'Date_of_birth'=>'required',
          'gender'=>'required',
          'phone_number'=>'required',
          'address'=>'required',
          'nid_number'=>'required',
          'dl_number'=>'required',
          'password'=>'required|min:3|max:12',
          'pp'=>'mimes:jpg,jpeg,png',


        ]);
        $user = new User();
        $file_name = time().".".$request->file('pp')->getClientOriginalExtension();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->dob = $request->Date_of_birth;
        $user->gender = $request->gender;
        $user->phone_number = $request->phone_number;
        $user->address = $request->address;
        $user->nid_number = $request->nid_number;
        $user->dl_number = $request->dl_number;
        $user->password = Hash::make ($request->password);
        $user->type = $request->role;
        $user->block_status = 0;
        $request->file('pp')->move(public_path('pro_images'),$file_name);
        $user->pp = $file_name;
        $res = $user->save();
        if($res){
            return back()->with('success','You have registered successfully');
        }else{
            return back()->with('fail', 'Something Went Wrong');
        }
    }
    public function loginUser(Request $request){
        $request->validate([
          'email'=>'required|email',
          'password'=>'required|min:3|max:12'
        ]);
        $b_user = BlockUser::where('email','=',$request->email)->first();
        if($b_user){
            return back()->with('fail', 'This Account got Blocked');
        }
        else{

        $user = User::where('email','=',$request->email)->first();

        // if($user){

        //     if(Hash::check($request->password, $user->password) && ($user->type='Customer')){
        //        $request->session()->put('loginId', $user->id);
        //        return redirect('dashboard_customer');
        //     }
        //     elseif(Hash::check($request->password, $user->password) && ($user->type='Renter')){
        //         $request->session()->put('loginId', $user->id);
        //         return redirect('dashboard_renter');
        //     }
        //     elseif(Hash::check($request->password, $user->password) && ($user->type='Admin')){
        //         $request->session()->put('loginId', $user->id);
        //         return redirect('dashboard_admin');
        //     }
        //     else{

        //         return back()->with('fail', 'Incorrect Password');
        //     }

        // }else{
        //     return back()->with('fail', 'this email is not registered');
        // }

        if($user){
            if(Hash::check($request->password, $user->password)){
              if($user->type=='Customer'){
                $request->session()->put('Customer', $user->type);
                   $request->session()->put('CUname', $user->username);
                 return redirect('dashboard_customer');
              }
              elseif($user->type=='Renter'){
                $request->session()->put('Renter', $user->type);
                $request->session()->put('email', $user->email);
                $request->session()->put('RUname', $user->username);
                $request->session()->put('Rid', $user->id);
                $request->session()->put('PP', $user->pp);
                  return redirect('dashboard_renter');
              }
              else{
                $request->session()->put('Admin', $user->type);
                $request->session()->put('email', $user->email);
                  return redirect(route('admin dashboard'));
              }


            }
             else{

                  return back()->with('fail', 'Incorrect Password');
              }

          }else{
              return back()->with('fail', 'this email is not registered');
          }
        }
    }

    public function dashboard_admin()
    {

        return view('Admin_Pages.dashboard_admin');
    }

    public function dashboard_customer()
    {

        return view('dashboard_customer');
    }

    public function dashboard_renter()
    {

        return view('Renter_Pages.dashboard_renter');
    }

    public function logout()
    {

        session()->flush();

        return redirect(route('login'));
    }
}
