<?php

namespace App\Http\Controllers\Auth;

use App\Events\Auth\UserRequestedActivationEmail;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

	$api_url = 'http://distribution.projectoblio.com/';
	$auth_url = $api_url . 'oauth/';
	$client_id = 2;
	$redirect_uri = 'http://poster.projectoblio.com/oauth.php'; ## replace with your client 
	$client_secret = 'TsmyPvNCjdX2dH3MNlBg9oUVFurtqWCC8ijXAoUg'; ## replace with your client secret
	$isUserLoggedIn = false;
	function redirectToAuthorization()
{
    global $client_id, $redirect_uri, $auth_url;
    if (!isset($_GET['code'])) {
        $query = http_build_query([
          'client_id' => $client_id,
          'redirect_uri' => $redirect_uri,
          'response_type' => 'code',
          'scope' => 'email',
        ]);
        header('Location: ' . $auth_url . 'authorize' . '?' . $query);
        exit(0);
    }
}


function getAccessToken()
{
    global $client_id, $redirect_uri, $auth_url, $client_secret;
    if (isset($_GET['code'])) {
        // code is retrived exchange it to get token
        $data = [
          'grant_type' => 'authorization_code',
          'client_id' => $client_id,
          'client_secret' => $client_secret,
          'redirect_uri' => $redirect_uri,
          'code' => $_GET['code'],
        ];
        $params = http_build_query($data);
        //open connection
        $ch = curl_init();
        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $auth_url . 'token');
        curl_setopt($ch, CURLOPT_POST, count($data));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //execute post
        $result = curl_exec($ch);
        //close connection
        curl_close($ch);
        $token = json_decode($result, true);
        $_SESSION['access_token'] = $token;
        header("Refresh:0; url=oauth.php");
    }
}
	if (isset($_SESSION['access_token'])) {
	    $isUserLoggedIn = true;
	} else {
	    if (isset($_GET['auth'])) {
		redirectToAuthorization();
	    }
	    if (isset($_GET['code'])) {
		getAccessToken();
	    }
	}

	function getUserDetails()
	{
	    global $api_url;
	    $token = $_SESSION['access_token']['access_token'];
	    $ch = curl_init();
	    //set the url, number of POST vars, POST data
	    curl_setopt($ch, CURLOPT_URL, $api_url . 'api/me');
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	      'Authorization: Bearer ' . $token,
	      'Accept: application/json'
	    ));
	    //execute post
	    $result = curl_exec($ch);
	    //close connection
	    curl_close($ch);
	    return json_decode($result, true);
	}

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->redirectTo = env('REDIRECT_CREATE_ACC');
        $this->middleware('guest');
    }

    protected function register(Request $request)
    {
        $input = $request->all();
        $validator = $this->validator($input);
        if ($validator->passes()) {
            $user = $this->create($request->all());
            if (!env('EMAIL_ACTIVATION')) {
                $this->guard()->login($user);
            } else {
                event(new UserRequestedActivationEmail($user));

                flash('Registered. Please check your email to activate your account.', 'success');
            }
            return redirect($this->redirectPath());
        }
        return redirect(route('register'))->with('errors', $validator->errors())->withInput();
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'username' => 'required|string|max:100|unique:users|alpha_dash|regex:/(^[A-Za-z0-9\.\,\+\-\?\! ]+$)+/',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'g-recaptcha-response' => 'required|recaptcha',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        if (!env('EMAIL_ACTIVATION')) {
            return User::create([
                'username' => htmlspecialchars($data['username']),
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'api_token' => str_random(60),
                'active' => true, //set to false if ur a fag
                'activation_token' => str_random(191),
            ]);
        } else {
            return User::create([
                'username' => htmlspecialchars($data['username']),
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'api_token' => str_random(60),
                'active' => false, //set to false if ur a fag
                'activation_token' => str_random(191),
            ]);
        }
    }


}
