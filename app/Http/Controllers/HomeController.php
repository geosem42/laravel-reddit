<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Subreddit;
use App\User;
use App\Post;
use App\Moderator;
use App\Http\Controllers\Posts;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Subreddit $subreddit, Post $post, Moderator $moderator, User $user)
    {
        //$posts = Post::with('user.votes')->get();
        $posts = Post::with('user.votes')->with('subreddit.moderators')->orderBy('created_at', 'desc')->get();
        //$ids = $posts->subreddit;
        $isModerator = false;

        //dd($ids);

        return view('home')->with('posts', $posts)->with('isModerator', $isModerator);
    }

    public function search(Post $post, Subreddit $subreddit, Request $request)
    {
        $query = $request->input('search');
        $subreddit = Subreddit::with('posts.votes')->with('moderators.user')->first();
        $posts = Post::where('title', 'LIKE', '%' . $query . '%')->get();
        $isModerator = false;

        return view('site.search', compact('query', 'subreddit', 'posts', 'isModerator'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
