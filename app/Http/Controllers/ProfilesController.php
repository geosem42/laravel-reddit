<?php

namespace App\Http\Controllers;

use App\Subreddit;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\User;

class ProfilesController extends Controller
{

    public function __construct() {
        $this->middleware('auth', ['only' => ['edit'] ]);
    }

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($user)
    {
        $user = User::whereName($user)->with('posts.votes')->firstOrFail();

        $linkKarma = User::find($user->id)->votes()->sum('value');

        return view('user/profile')->with('user', $user)
                                    ->with('linkKarma', $linkKarma);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
