<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Thread;
use App\Vote;
use Illuminate\Support\Facades\Auth;
use App\Subscription;

class HomeController extends Controller
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
    public function index(Request $request, Thread $thread, Vote $vote, Subscription $subscription)
    {
        
        $sort = $request->segment(2);
        $sort_type = $request->segment(1);

        $user = false;
        $subscriptionsIdsArray = null;
        if (Auth::check() && $sort_type !== 'g') {
            $user = Auth::user();

            $subscriptionsIdsArray = $subscription->select('sub_lolhow_id')->where('user_id', $user->id)->get()->toArray();
        }

        $page = $request->input('page');
        if (!is_numeric($page)) {
            $page = 1;
        }

        if (!$sort) {
            if ($user) {
                $threads = $thread->whereIn('sub_lolhow_id', $subscriptionsIdsArray)->where('created_at', '>=', \Carbon\Carbon::now()->subDay(7))->take(25)->orderBy('score', 'DESC');
            } else {
                $threads = $thread->where('created_at', '>=', \Carbon\Carbon::now()->subDay(7))->take(25)->orderBy('score', 'DESC');
            }
        } else if ($sort == 'popular') {
            if ($sort_type == 'g') {
                $sort = 'popular_g';
            }
            $threads = $thread->where('created_at', '>=', \Carbon\Carbon::now()->subDay(7))->take(25)->orderBy('score', 'DESC');
        } else if ($sort == 'new') {
            if ($user) {
                $threads = $thread->whereIn('sub_lolhow_id', $subscriptionsIdsArray)->orderBy('created_at', 'DESC')->take(25);
            } else {
                $threads = $thread->orderBy('created_at', 'DESC')->take(25);
            }
        } else if ($sort == 'top') {
            if ($user) {
                $threads = $thread->whereIn('sub_lolhow_id', $subscriptionsIdsArray)->orderBy('score', 'DESC')->take(25);
            } else {
                $threads = $thread->orderBy('score', 'DESC')->take(25);
            }
        } else if ($sort == 'shekeld') {
            //coming soon
            $threads = null;
        } else {
            $threads = null;
        }

        $userVotes = null;
        if ($threads) {
            if ($page) {
                $threads = $threads->skip(25 * $page - 25);
            }
            $threads = $threads->get();

            $threadsArray = $threads->pluck('id')->toArray();
            if (Auth::check()) {
                $userVotes = $vote->where('user_id', Auth::user()->id)->whereIn('thread_id', $threadsArray)->get();
            }
        }


        return view('home', array('threads' => $threads, 'userVotes' => $userVotes, 'sort' => $sort, 'page' => $page));
    }
}
