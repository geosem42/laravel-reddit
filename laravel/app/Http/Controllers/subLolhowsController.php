<?php

namespace App\Http\Controllers;

use App\Moderator;
use Illuminate\Http\Request;
use App\subLolhow;
use App\Thread;
use App\Vote;
use Illuminate\Support\Facades\Auth;
use App\Subscription;

class subLolhowsController extends Controller
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


    public function subLolhow($name, Request $request, subLolhow $subLolhow, Thread $thread, Vote $vote, Subscription $subscription, Moderator $moderator)
    {
        $subLolhow = $subLolhow->where('name', $name)->first();

        $page = $request->input('page');
        if (!is_numeric($page)) {
            $page = 1;
        }

        if (!$subLolhow) {
            return view('subLolhows.subLolhow', array('subLolhow' => $subLolhow));
        }

        $readers = $subscription->where('sub_lolhow_id', $subLolhow->id)->count();

        $sort = $request->segment(3);
        if (!$sort) {
            $threads = $thread->where('sub_lolhow_id', $subLolhow->id)->where('created_at', '>=', \Carbon\Carbon::now()->subDay(7))->take(25)->orderBy('score', 'DESC');
        } else if ($sort == 'new') {
            $threads = $thread->where('sub_lolhow_id', $subLolhow->id)->orderBy('created_at', 'DESC')->take(25);
        } else if ($sort == 'top') {
            $threads = $thread->where('sub_lolhow_id', $subLolhow->id)->orderBy('score', 'DESC')->take(25);
        } else if ($sort == 'shekeld') {
            //coming soon
            $threads = null;
        } else {
            $threads = null;
        }

        $userVotes = null;
        $subscribed = null;
        if ($threads) {
            if ($page) {
                $threads = $threads->skip(25 * $page - 25);
            }
            $threads = $threads->get();

            $threadsArray = $threads->pluck('id')->toArray();
            if (Auth::check()) {
                $user = Auth::user();
                $subscribed = $subscription->subscribed($user->id, $subLolhow->id);
                $userVotes = $vote->where('user_id', $user->id)->whereIn('thread_id', $threadsArray)->get();
            }
        }

        return view('subLolhows.subLolhow', array(
            'subLolhow' => $subLolhow,
            'threads' => $threads,
            'userVotes' => $userVotes,
            'sort' => $sort,
            'subscribed' => $subscribed,
            'readers' => $readers,
            'moderators' => $moderator->getBySubLolhowId($subLolhow->id))
        );
    }

}
