<?php

namespace App\Http\Controllers;

use App\Post;
use App\Subreddit;
use Embed\Embed;
use Image;
use File;
use App\User;
use App\Moderator;
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

    public function index()
    {
        //
    }

    public function create()
    {
        $subreddits = Subreddit::lists('name', 'id')->toArray();

        return view('post/create')->with('subreddits', $subreddits);
    }

    public function getSubreddits($query = '') {
        $q = Subreddit::select('id', 'name');
        if ($query) {
            $q->where('name', 'LIKE', '%' . $query . '%');
        }
        return Response::json($q->get());
    }

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

            return redirect('/subreddit');
        }
        Auth::user()->posts()->create($request->all());

        return redirect('/subreddit');
    }

    public function show(Post $post, Subreddit $subreddit)
    {
        $post = Post::with('user.votes')->findOrFail($post->id);

        return view('post/show')->with('post', $post)->with('subreddit', $subreddit);
    }

    public function edit(User $user, Post $post)
    {
        if (Gate::denies('update-post', $post)) {
            return redirect('subreddit')->withErrors('You cannot edit this post.');
        } else {
            return view('post/edit')->with('post', $post);
        }
    }

    public function update(EditPostRequest $request, Post $post)
    {
        if (Gate::denies('update-post', $post)) {
            return redirect('subreddit')->withErrors('You cannot edit this post.');
        } else {
            $post->update($request->all());
            return redirect('/subreddit');
        }
    }

    public function destroy($id)
    {
        //
    }
}
