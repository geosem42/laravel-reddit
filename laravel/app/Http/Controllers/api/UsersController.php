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
use DB;
use App\Thread;
use App\Subscription;

class UsersController extends Controller
{
 
    public function externalsignup(Request $request){
 
    $redirect_back=env('APP_URL').'/externalauth';
        
    //Cookie::make('redirect_back',$redirect_back);
    setcookie("redirect_back", $redirect_back, time() + (86400 * 30), '/', 'https://distribution.projectoblio.com');

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

        //Add auto suscribe top 25 sublolhow
        $insertBatchForSubscribe = array();
        $topsublolhow = Thread::select('sub_lolhow_id', DB::raw('count(*) as thread_count'))->where('sub_lolhow_id', '!=', 'null')->GroupBy('sub_lolhow_id')->orderBy('thread_count', 'DESC')->limit(25)->get();      
        foreach($topsublolhow as $key => $sub_lolhow_id) {
            $insertBatchForSubscribe[$key]['sub_lolhow_id'] = $sub_lolhow_id->sub_lolhow_id;
            $insertBatchForSubscribe[$key]['user_id']       = Auth::user()->id;
            $insertBatchForSubscribe[$key]['created_at']    = date('Y-m-d H:i:s');
            $insertBatchForSubscribe[$key]['updated_at']    = date('Y-m-d H:i:s');
        }
        DB::beginTransaction();
        Subscription::insert($insertBatchForSubscribe);
        DB::commit();
     }
     return redirect('/');    
  }
  
  public function oauth(){
      
  }
  
}
