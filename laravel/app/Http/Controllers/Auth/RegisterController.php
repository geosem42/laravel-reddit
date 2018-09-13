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
