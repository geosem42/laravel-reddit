<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Events\Auth\UserRequestedActivationEmail;

class ActivationController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function activate(Request $request, User $user)
    {
        $user = $user->where('email', $request->email)->where('activation_token', $request->token)->firstOrFail();

        $user->update([
            'active' => true,
            'activation_token', null
        ]);

        Auth::loginUsingId($user->id, false);

        flash('Your account has been activated', 'success');
        return redirect(env('REDIRECT_ACTIVATION'));
    }

    public function showResendForm()
    {
        return view('auth.activate.resend');
    }

    public function resend(Request $request, User $user)
    {
        $this->validateResendRequest($request);

        $user = $user->where('email', $request->email)->first();

        event(new UserRequestedActivationEmail($user));

        flash('Account activation email has been resent', 'success');

        return redirect()->route('login');
    }

    protected function validateResendRequest(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|exists:users,email',
            'g-recaptcha-response' => 'required|recaptcha',
        ], [
            'email.exists' => "Could not find that account"
        ]);
    }
}
