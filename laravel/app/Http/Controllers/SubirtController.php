<?php

namespace App\Http\Controllers;

use App\Subirt;
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

class SubirtController extends Controller
{
    public function __construct() {
        $this->middleware('auth', ['only' => ['create', 'edit']]);
    }

    public function index()
    {
        $subirt = Subirt::latest('created_at')->paginate(15);
        $subirt->setPath('subirt');

        return view('subirt/index')->with('subirt', $subirt);
    }

    public function create()
    {
        return view('subirt/create');
    }

    public function store(Requests\SubirtRequest $request, Moderator $moderator, Subirt $subirt)
    {
        $sub = Auth::user()->subirt()->create($request->all());

        $moderator = new Moderator;
        $moderator->user_id = Auth::id();
        $moderator->subirt_id = $sub->id;
        $moderator->save();

        return redirect('/');
    }

    public function show(Subirt $subirt, Post $post, User $user)
    {
        // OLD METHOD
        // $subirt = Subirt::with('posts.votes')->with('user')->findOrFail($subirt->id);
        //
        $subirt = Subirt::with('posts.votes')->with('moderators.user')->where('id', $subirt->id)->first();
        $posts = $subirt->posts()->orderBy('created_at', 'desc')->paginate(15);
        $posts->setPath($subirt->id);
        $isModerator = $subirt->moderators()->where('user_id', Auth::id())->exists();
        $user = User::where('id', '=', Auth::id())->get();
        $modList = Moderator::where('subirt_id', '=', $subirt->id)->get();


        return view('subirt/show')->with('subirt', $subirt)
            ->with('user', $user)
            ->with('isModerator', $isModerator)
            ->with('modList', $modList)
            ->with('posts', $posts);

    }

    public function edit(User $user, Subirt $subirt)
    {
        if(Gate::denies('update-sub', $subirt)) {
            return 'no';
        } else {
            return view('subirt/edit')->with('subirt', $subirt);
        }
    }

    public function update(Requests\SubirtRequest $request, Subirt $subirt)
    {
        if(Gate::denies('update-sub', $subirt)) {
            return 'no';
        } else {
            $subirt->update($request->all());
            return redirect('/subirt');
        }
    }

    public function destroy($id)
    {
        //
    }

    public function mySubirts(User $user) {
        $subirt = Subirt::where('user_id', '=', Auth::id())->get();
        return view('user/mysubirts')->with('user', $user)->with('subirt', $subirt);
    }

    public function createModerators(Subirt $subirt) {
        $subirt = Subirt::with('posts.votes')->findOrFail($subirt->id);
        return view('user/moderators')->with('subirt', $subirt);
    }

    public function search(Subirt $subirt, Request $request)
    {
        $query = $request->input('search');
        $subirt = Subirt::with('posts.votes')->with('moderators.user')->where('id', $subirt->id)->first();
        $posts = $subirt->posts()->where('title', 'LIKE', '%' . $query . '%')->get();
        $isModerator = $subirt->moderators()->where('user_id', Auth::id())->exists();
        $modList = Moderator::where('subirt_id', '=', $subirt->id)->get();

        return view('subirt.search', compact('query', 'subirt', 'posts', 'isModerator', 'modList'));
    }
}
