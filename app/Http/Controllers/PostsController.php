<?php

namespace App\Http\Controllers;

use App\Post;
use App\Subreddit;
use Embed\Embed;
use Image;
use File;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\PostRequest;
use App\Http\Requests\EditPostRequest;
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

            if ($info->image == null) {
                $embed_data = ['text' => $info->description];
            } else if ($info->description == null) {
                $embed_data = ['text' => ''];
            } else {
                $extension = pathinfo($info->image, PATHINFO_EXTENSION);

                $newName = public_path() . '/images/' . str_random(8) . ".{$extension}";

                if (File::exists($newName)) {
                    $imageToken = substr(sha1(mt_rand()), 0, 5);
                    $newName = public_path() . '/images/' . str_random(8) . '-' . $imageToken . ".{$extension}";
                }

                $image = Image::make($info->image)->fit(70, 70)->save($newName);
                $embed_data = ['text' => $info->description, 'image' => basename($newName)];
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
    public function show(Post $post)
    {
        $post = Post::with('user.votes')->findOrFail($post->id);

        return view('post/show')->with('post', $post);
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
    public function update(EditPostRequest $request, Post $post)
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
