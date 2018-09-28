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
         $user = User::where("username", htmlspecialchars($request->input('name')))->first();
         if (empty($user)) {
          $user = User::create([
            'username' => htmlspecialchars($request->input('name')),
            'email' => 'notallowed_'.htmlspecialchars($request->input('name')),
            'password' => bcrypt($request->input('password')),
            'api_token' => str_random(60),
            'active' => false, //set to false if ur a fag
            'activation_token' => str_random(191),
          ]);    
         }

         auth()->login($user);
     }
     return redirect('/');    
  }
  
  public function oauth(){
      
  }
  
}
