<?php

namespace App\Http\Controllers;

use App\Subreddit;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\User;
use App\Post;
use Illuminate\Support\Facades\Auth;
use Input;
use Validator;
use Illuminate\Support\Facades\Hash;

class ProfilesController extends Controller
{

    public function __construct() {
        $this->middleware('auth', ['only' => ['edit'] ]);
    }

    public function show($user, Post $post)
    {
        $user = User::whereName($user)->with('posts.votes')->firstOrFail();
        //dd($user);
        $linkKarma = User::find($user->id)->votes()->sum('value');

        $isModerator = false;

        return view('user/profile')->with('user', $user)
                                    ->with('linkKarma', $linkKarma)
                                    ->with('isModerator', $isModerator);
    }

    public function edit(User $user)
    {
        $user = User::where('id', '=', Auth::id())->first();
        return view('user/edit')->with('user', $user);

    }

    public function update(Request $request, User $user, Requests\EditUserRequest $request)
    {
        $user = User::where('id', '=', Auth::id())->first();
        $user->name = Input::get('name');
        $user->email = Input::get('email');

        if(!empty(Input::get('password'))) {
            $user->password = Hash::make(Input::get('password'));
        }

        $user->save();

        return view('user/edit')->with('user', $user);
    }
}
