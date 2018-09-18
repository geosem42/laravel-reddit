<?php

namespace App\Http\Controllers;

use App\Post;
use App\Subscription;
use Illuminate\Http\Request;
use App\Thread;
use App\Vote;
use Illuminate\Support\Facades\Auth;
use App\User;

class userProfileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($name, Request $request, Thread $thread, Post $post, User $user, Vote $vote, Subscription $subscription)
    {
        $user = $user->where('username', $name)->first();

        $sort = $request->segment(3);
        if (!$sort) {
            $sort = 'new';
        }
        $page = $request->input('page');
        if (!$page || !is_numeric($page)) {
            $page = 1;
        }
        $skip = 25 * $page - 25;

        $comments = null;
        $posts = null;
        $subscriptions = null;
        if ($user) {
            $comments = $post->postsbyUser($user->id, $sort, $skip, 25);
            $posts = $thread->threadsByUser($user->id, $sort, $skip, 25);
            $subscriptions = $subscription->subscriptions($user->id);
        }
        $userVotes = null;
        if (Auth::check() && $user) {
            $auth_user = Auth::user();
            $threadsArray = $posts->pluck('id')->toArray();
            $postsArray = $comments->pluck('id')->toArray();

            $thread_votes = $vote->where('user_id', $auth_user->id)->whereIn('thread_id', $threadsArray)->get();
            $post_votes = $vote->where('user_id', $auth_user->id)->whereIn('post_id', $postsArray)->get();
            $userVotes = $thread_votes->merge($post_votes);
    }
        
        return view('profile', array('sort' => $sort, 'user' => $user, 'posts' => $posts, 'comments' => $comments, 'userVotes' => $userVotes, 'page' => $page, 'subscriptions' => $subscriptions));
    }

    public function sortPackage($sort, $collection)
    {
        if ($sort == 'new') {
            return $collection->sortBy('created_at');
        } else if ($sort == 'popular' || $sort == 'top') {
            return $collection->sortBy('score');
        } else {
            return false;
        }
    }
}
