<?php

namespace App\Http\Controllers;

use App\Subreddit;
use App\User;
use App\Post;
use App\Moderator;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Gate;

class SubredditController extends Controller
{
    public function __construct() {
        $this->middleware('auth', ['only' => ['create', 'edit']]);
    }

    public function index()
    {
        $subreddit = Subreddit::latest('created_at')->paginate(3);

        return view('subreddit/index')->with('subreddit', $subreddit);
    }

    public function create()
    {
        return view('subreddit/create');
    }

    public function store(Requests\SubredditRequest $request)
    {
        Auth::user()->subreddit()->create($request->all());

        return redirect('/');
    }

    public function show(Subreddit $subreddit, Post $post)
    {
        $subreddit = Subreddit::with('posts.votes')->findOrFail($subreddit->id);
        $moderators = Moderator::where('subreddit_id', '=', $subreddit->id)->get();

        return view('subreddit/show')->with('subreddit', $subreddit)
                                    ->with('moderators', $moderators);
    }

    public function edit(User $user, Subreddit $subreddit)
    {
        if(Gate::denies('update-sub', $subreddit)) {
            return 'no';
        } else {
            return view('subreddit/edit')->with('subreddit', $subreddit);
        }
    }

    public function update(Requests\SubredditRequest $request, Subreddit $subreddit)
    {
        if(Gate::denies('update-sub', $subreddit)) {
            return 'no';
        } else {
            $subreddit->update($request->all());
            return redirect('/subreddit');
        }
    }

    public function destroy($id)
    {
        //
    }

    public function mySubreddits(User $user, Subreddit $subreddit) {
        $subreddit = Subreddit::where('user_id', '=', Auth::id())->get();
        return view('user/mysubreddits')->with('user', $user)->with('subreddit', $subreddit);
    }

    public function createModerators(Subreddit $subreddit) {
        $subreddit = Subreddit::with('posts.votes')->findOrFail($subreddit->id);
        dd($subreddit);
        return view('user/moderators')->with('subreddit', $subreddit);
    }
}
