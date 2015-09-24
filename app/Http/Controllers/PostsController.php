<?php

namespace App\Http\Controllers;

use App\Post;
use App\Subreddit;
use Embed\Embed;
use Image;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\PostRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Gate;

class PostsController extends Controller
{
    public function __construct() {
        $this->middleware('auth', ['only' => ['create', 'edit'] ]);
    }


    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $subreddits = Subreddit::lists('name', 'id')->toArray();

        return view('post/create')->with('subreddits', $subreddits);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PostRequest|Request $request
     * @return Response
     */
    public function store(PostRequest $request)
    {
        if (Input::has('link')) {
            $input['link'] = Input::get('link');
            $info = Embed::create($input['link']);

            if (!empty($info->image)) {
                $image = Image::make($info->image)->resize(120, 120)->save('C:\xampp\htdocs\reddit\public\images' . '/' . str_random(8) . '.jpg');

                $embed_data = ['text' => $info->description, 'image' => $image->filename . '.jpg'];
            } else {
                $embed_data = ['text' => $info->description];
            }

            Auth::user()->posts()->create(array_merge($request->all(), $embed_data));

            return redirect('/articles');
        }
        Auth::user()->posts()->create($request->all());

        return redirect('/');
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
    public function edit(Post $post)
    {
        if (Gate::denies('update-post', $post)) {
            return view('home')->withErrors('This is not your article');
        }

        return view('post/edit')->with('post', $post);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(PostRequest $request, Post $post)
    {
        if (Gate::denies('update-post', $post)) {
            return view('home')->withErrors('This is not your article');
        }

        $post->update($request->all());

        return redirect('/');
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

    public function autocomplete(){

    }
}
