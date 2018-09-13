<?php

namespace App\Http\Controllers;

use App\Moderator;
use Illuminate\Http\Request;
use App\subPlebbit;
use App\Thread;
use App\Vote;
use Illuminate\Support\Facades\Auth;
use App\Subscription;

class subPlebbitsController extends Controller
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


    public function subPlebbit($name, Request $request, subPlebbit $subPlebbit, Thread $thread, Vote $vote, Subscription $subscription, Moderator $moderator)
    {
        $subPlebbit = $subPlebbit->where('name', $name)->first();

        $page = $request->input('page');
        if (!is_numeric($page)) {
            $page = 1;
        }

        if (!$subPlebbit) {
            return view('subPlebbits.subPlebbit', array('subPlebbit' => $subPlebbit));
        }

        $readers = $subscription->where('sub_plebbit_id', $subPlebbit->id)->count();

        $sort = $request->segment(3);
        if (!$sort) {
            $threads = $thread->where('sub_plebbit_id', $subPlebbit->id)->where('created_at', '>=', \Carbon\Carbon::now()->subDay(7))->take(25)->orderBy('score', 'DESC');
        } else if ($sort == 'new') {
            $threads = $thread->where('sub_plebbit_id', $subPlebbit->id)->orderBy('created_at', 'DESC')->take(25);
        } else if ($sort == 'top') {
            $threads = $thread->where('sub_plebbit_id', $subPlebbit->id)->orderBy('score', 'DESC')->take(25);
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
                $subscribed = $subscription->subscribed($user->id, $subPlebbit->id);
                $userVotes = $vote->where('user_id', $user->id)->whereIn('thread_id', $threadsArray)->get();
            }
        }

        return view('subPlebbits.subPlebbit', array(
            'subPlebbit' => $subPlebbit,
            'threads' => $threads,
            'userVotes' => $userVotes,
            'sort' => $sort,
            'subscribed' => $subscribed,
            'readers' => $readers,
            'moderators' => $moderator->getBySubPlebbitId($subPlebbit->id))
        );
    }

}
