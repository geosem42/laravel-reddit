<?php

namespace App\Http\Controllers\api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{

    use AuthenticatesUsers;

    /**
     * Override the username method used to validate login
     *
     * @return string
     */
    public function username()
    {
        return 'username';
    }

    protected function authenticated(Request $request, User $user)
    {
        $user->api_token = str_random(60);
        $user->save();

        return response()->json([
            'status' => 'success',
            'user' => [
                'username'=> $user->username,
                'email' => $user->email,
                'api_token' => $user->api_token
            ]
        ], 200);
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
//        $this->validate($request, [
//            'username'    => 'required',
//            'password' => 'required',
//        ]);

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return response()->json([
                'status' => 'error',
                'msg' => 'Too many requests'
            ], 200);
        }

        $login_type = filter_var($request->input('username'), FILTER_VALIDATE_EMAIL )
            ? 'email'
            : 'username';

        $request->merge([
            $login_type => $request->input('username')
        ]);

        if (Auth::attempt($request->only($login_type, 'password'))) {
            $user = Auth::user();
            return response()->json([
                'status' => 'success',
                'user' => [
                    'username'=> $user->username,
                    'email' => $user->email,
                    'api_token' => $user->api_token
                ]
            ], 200);
        }

        $this->incrementLoginAttempts($request);

        return response()->json([
            'status' => 'error',
            'msg' => 'Invalid username or password'
        ], 200);
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
        return response()->json([
            'status' => 'error',
            'msg' => 'Validation failed'
        ], 200);
    }

}
