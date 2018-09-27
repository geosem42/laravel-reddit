<?php

namespace App\Http\Controllers\api;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;

use App\User;
use App\Mail\Welcome;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cookie;

use Illuminate\Http\Request;
use Session;

class UsersController extends Controller
{
 
    public function externalsignup(Request $request){
 
    $redirect_back=env('APP_URL').'/externalauth';    
        
    //Cookie::make('redirect_back',$redirect_back);
    setcookie('redirect_back', $redirect_back, time() + (86400 * 30), "/"); // 86400 = 1 day

    $external_site=env('DISTRIBUTION_URL').'externalsignup';
    return redirect()->to($external_site);        
  }
  
  public function externalauth(Request $request){
     
     $data=$request->all();
    
    if(!empty($data)){
       
         $user = User::create(
            [
             'username'         => $request->input('name'),
             'password'         => bcrypt($request->input('password')),
             'email'         => '',
             'api_token'         => '',
            ]);
         
      if(auth()->attempt(['username' => $data['name'], 'password' => $data['password']])){
              Session::flash('success','New user successfully created.');         
      }
      else{
       Session::flash('danger','Error in user creation.');
      }
     }
     return redirect('/');    
  }
  
  public function oauth(){
      
  }
  
}
