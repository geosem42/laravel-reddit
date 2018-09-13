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
use Input;
use DB;

class SubredditController extends Controller
{
    public function __construct() {
        $this->middleware('auth', ['only' => ['create', 'edit']]);
    }

    public function index()
    {
        $subreddit = Subreddit::latest('created_at')->paginate(15);
        $subreddit->setPath('subreddit');

        return view('subreddit/index')->with('subreddit', $subreddit);
    }

    public function create()
    {
        return view('subreddit/create');
    }

    public function store(Requests\SubredditRequest $request, Moderator $moderator, Subreddit $subreddit)
    {
        $sub = Auth::user()->subreddit()->create($request->all());

        $moderator = new Moderator;
        $moderator->user_id = Auth::id();
        $moderator->subreddit_id = $sub->id;
        $moderator->save();

        return redirect('/');
    }

    public function show(Subreddit $subreddit, Post $post, User $user)
    {
        // OLD METHOD
        // $subreddit = Subreddit::with('posts.votes')->with('user')->findOrFail($subreddit->id);
        //
        $subreddit = Subreddit::with('posts.votes')->with('moderators.user')->where('id', $subreddit->id)->first();
        $posts = $subreddit->posts()->orderBy('created_at', 'desc')->paginate(15);
        $posts->setPath($subreddit->id);
        $isModerator = $subreddit->moderators()->where('user_id', Auth::id())->exists();
        $user = User::where('id', '=', Auth::id())->get();
        $modList = Moderator::where('subreddit_id', '=', $subreddit->id)->get();


        return view('subreddit/show')->with('subreddit', $subreddit)
            ->with('user', $user)
            ->with('isModerator', $isModerator)
            ->with('modList', $modList)
            ->with('posts', $posts);

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

    public function mySubreddits(User $user) {
        $subreddit = Subreddit::where('user_id', '=', Auth::id())->get();
        return view('user/mysubreddits')->with('user', $user)->with('subreddit', $subreddit);
    }

    public function createModerators(Subreddit $subreddit) {
        $subreddit = Subreddit::with('posts.votes')->findOrFail($subreddit->id);
        return view('user/moderators')->with('subreddit', $subreddit);
    }

    public function search(Subreddit $subreddit, Request $request)
    {
        $query = $request->input('search');
        $subreddit = Subreddit::with('posts.votes')->with('moderators.user')->where('id', $subreddit->id)->first();
        $posts = $subreddit->posts()->where('title', 'LIKE', '%' . $query . '%')->get();
        $isModerator = $subreddit->moderators()->where('user_id', Auth::id())->exists();
        $modList = Moderator::where('subreddit_id', '=', $subreddit->id)->get();

        return view('subreddit.search', compact('query', 'subreddit', 'posts', 'isModerator', 'modList'));
    }
}
