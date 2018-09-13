<?php

namespace App\Http\Controllers;

use App\Post;
use App\Subirt;
use App\Http\Controllers\CommentController;
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
use Illuminate\Http\Request;
use App\Comment;
use Illuminate\Pagination\LengthAwarePaginator;
use DB;
use Validator;
use Log;
use App\Config;
use App\Flair;

class PostsController extends Controller
{
    public function __construct() {
        $this->middleware('auth', ['only' => ['create', 'edit'] ]);
    }

    public function create()
    {
        $subirts = Subirt::lists('name', 'id')->toArray();

        return view('post/create')->with('subirts', $subirts);
    }

    public function getSubirts($query = '') {
        $q = Subirt::select('id', 'name');
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
            
            $jpg = str_contains($input['link'], '.jpg');
            $png = str_contains($input['link'], '.png');
            $gif = str_contains($input['link'], '.gif');

            if ($jpg || $png || $gif) {
                $orig = pathinfo($input['link'], PATHINFO_EXTENSION);
                $qmark = str_contains($orig, '?');
                if($qmark == false) {
                    $extension = $orig;
                } else {
                    $extension = substr($orig, 0, strpos($orig, '?'));
                }

                $newName = public_path() . '/images/' . str_random(8) . ".{$extension}";

                if (File::exists($newName)) {
                    $imageToken = substr(sha1(mt_rand()), 0, 5);
                    $newName = public_path() . '/images/' . str_random(8) . '-' . $imageToken . ".{$extension}";
                }

                $image = Image::make($input['link'])->fit(70, 70)->save($newName);
                $embed_data = ['image' => basename($newName)];
                Auth::user()->posts()->create(array_merge($request->all(), $embed_data));
            }

            if ($info->image == null) {
                $embed_data = ['text' => $info->description];
            } else if ($info->description == null) {
                $embed_data = ['text' => ''];
            } else {
                $orig = pathinfo($info->image, PATHINFO_EXTENSION);
                $qmark = str_contains($orig, '?');
                if($qmark == false) {
                	$extension = $orig;
                } else {
                	$extension = substr($orig, 0, strpos($orig, '?'));
                }

                $newName = public_path() . '/images/' . str_random(8) . ".{$extension}";

                if (File::exists($newName)) {
                    $imageToken = substr(sha1(mt_rand()), 0, 5);
                    $newName = public_path() . '/images/' . str_random(8) . '-' . $imageToken . ".{$extension}";
                }

                $image = Image::make($info->image)->fit(70, 70)->save($newName);
                $embed_data = ['text' => $info->description, 'image' => basename($newName)];
                
                Auth::user()->posts()->create(array_merge($request->all(), $embed_data));
            }
            return redirect('/subirt');
        }
        Auth::user()->posts()->create($request->all());

        return redirect('/subirt');
    }

    public function show(Post $post, User $user, Request $request, Comment $comment)
    {
        $post = Post::with('user.votes')->with('subirt.moderators')->findOrFail($post->id);
        $ids = $post->subirt;
        $check = $ids->moderators()->where('user_id', Auth::id())->first();
        $isModerator = $check ? true:false;
        $modList = Moderator::where('subirt_id', '=', $post->subirt->id)->get();
        $view_data = CommentController::view_data($request, $post, $comment, $isModerator);

        return view('post/show', $view_data)->with('post', $post)
                                ->with('modList', $modList)
                                ->with('isModerator', $isModerator);
    }

    public function edit(Post $post)
    {
        $post = Post::with('user.votes')->with('subirt.moderators')->findOrFail($post->id);
        $ids = $post->subirt;
        $isModerator = $ids->moderators()->where('user_id', Auth::id())->exists();
        if (Gate::denies('update-post', [$post, $isModerator])) {
            return redirect('subirt')->withErrors('You cannot edit this post.');
        } else {
            return view('post/edit')->with('post', $post)->with('isModerator', $isModerator);
        }
    }

    public function update(EditPostRequest $request, Post $post)
    {
        $post = Post::with('user.votes')->with('subirt.moderators')->findOrFail($post->id);
        $ids = $post->subirt;
        $isModerator = $ids->moderators()->where('user_id', Auth::id())->exists();
        if (Gate::denies('update-post', [$post, $isModerator])) {
            return redirect('subirt')->withErrors('You cannot edit this post.');
        } else {
            $post->update($request->all());
            return redirect('/subirt');
        }
    }

    public function destroy($id)
    {
        //
    }

    public function search(Post $post, Request $request)
    {
        $query = $request->input('search');
        $subirtId = $request->input('subirt_id');
        $subirt = Subirt::with('posts.votes')->with('moderators.user')->where('id', $subirtId)->first();
        $posts = $subirt->posts()->where('title', 'LIKE', '%' . $query . '%')->get();
        $isModerator = $subirt->moderators()->where('user_id', Auth::id())->exists();
        $modList = Moderator::where('subirt_id', '=', $subirtId)->get();

        return view('subirt.search', compact('query', 'subirt', 'posts', 'isModerator', 'modList'));
    }
}
