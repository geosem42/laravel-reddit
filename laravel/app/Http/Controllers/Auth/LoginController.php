<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Lang;
use Illuminate\Validation\Rule;
use App\User;
use Illuminate\Support\Facades\Auth;
use Socialite; 


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';


    /**
     * Override the username method used to validate login
     *
     * @return string
     */
    public function username()
    {
        return 'username';
    }

  public function redirectToProvider()
    {
	$user = Socialite::driver('oblio')->stateless()->redirect();
	error_log("here is $user");
	error_log((string)($user));
	//return Socialite::driver('oblio')->scopes(['last_dub_time','point','karma','name'])->redirect();
       return $user;
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function handleProviderCallback(Request $request)
    {
	
	error_log("here is the code:");
	$code=$_GET['code'];
	error_log($code);
      	$response=Socialite::driver('oblio')->getAccessTokenResponse($code);
	error_log("here is the response from get access token response");
	
	var_dump($response);
	$something=$response['response'];
	error_log("got response");
	$token=$response['access_token'];	
	error_log($token);
    }

    protected function authenticated(Request $request, User $user){
        $user->api_token = str_random(60);
        $user->save();

        return redirect()->intended($this->redirectPath());
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    
    public function __construct(Request $request)
    {
        if ($request->input('redirect') !== null) {
            $this->redirectTo = $request->input('redirect');
        }
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
	error_log("redirected here");
	$user = Socialite::driver('laravel-irt')->stateless()->redirect();
	error_log((string)($user));	
	dd($user);
	
        /*$this->validate($request, [
            'username'    => 'required',
            'password' => 'required',
        ]);

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        $login_type = filter_var($request->input('username'), FILTER_VALIDATE_EMAIL )
            ? 'email'
            : 'username';

        $request->merge([
            $login_type => $request->input('username')
        ]);

        if (Auth::attempt($request->only($login_type, 'password'))) {
            return redirect()->intended($this->redirectPath());
        }

        $this->incrementLoginAttempts($request);
	*/
        //return $this->sendFailedLoginResponse($request);
    }


    public function validateLogin(Request $request)
    {
        $this->validate($request, [
           $this->username() => [
               'required', 'string',
               Rule::exists('users')->where(function ($query) {
                   $query->where('active', true);
               })
           ],

            'password' => 'required:string',
        ], [
            $this->username() . '.exists' => 'No account found, or you need to activate your account'
        ]);
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        return redirect()->to('/login')
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors([
                $this->username() => Lang::get('auth.failed'),
            ]);
    }
}
