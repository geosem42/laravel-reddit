<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Thread;
use App\Vote;
use Illuminate\Support\Facades\Auth;
use App\subLolhow;
use GrahamCampbell\Markdown\Facades\Markdown;
use App\PollOption;
use App\Moderator;
use App\BetOption;
use App\UserPoll;
use App\Bet;
use App\Poll;
use DB;

class commentsController extends Controller
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
    public function index($name, $code, Request $request, Thread $thread, Vote $vote, subLolhow $lolhow, Moderator $moderator)
    {
        $thread = $thread->where('code', $code)->first();
        $bets = [];
        $polls = [];
        if($thread->type == 'bet')
        {
            $bets = Bet::where('thread_id', $thread->id)->first();
            $bets['options'] = BetOption::where('bet_id', $bets->id)->get();
            $bets['user_id'] = (isset(Auth::user()->id) && Auth::user()->id > 0) ? Auth::user()->id : '0';
            $bets['results'] = DB::table('user_bets')
                                ->select(DB::raw('SUM(`amount`) as total, choise_id, bet_options.choice'))
                                ->join('bet_options', 'bet_options.id', '=','user_bets.choise_id')
                                ->where('user_bets.bet_id', $bets->id)
                                ->groupBy('choise_id', 'bet_options.choice')
                                ->get();
        }
        elseif($thread->type == 'poll'){
            // We will cosider bets as poll here 
            $polls = Poll::where('thread_id', $thread->id)->first();
            $polls['options'] = PollOption::where('poll_id', $polls->id)->orderBy('id', 'ASC')->get();
            $polls['user']    = Auth::user();
            $polls['results'] = UserPoll::select(DB::raw('count(*) as total, option_id'))->where('poll_id', $polls->id)->groupBy('option_id')->get();
            $polls['count']   = UserPoll::where('poll_id', $polls->id)->count();
        }
        
        $subLolhow = $lolhow->where('name', $name)->first();
        $mod = false;

        if ( (!$subLolhow) || (!$thread) ) {
            return view('threads.not_found');
        }
        if ($subLolhow->id != $thread->sub_lolhow_id) {
            flash('The thread was not found in the sublolhow', 'warning');
            return redirect('/');
        }
        $userVotes = false;
        if (Auth::check()) {
            $user = Auth::user();
            $userVotes = $vote->where('user_id', $user->id)->where('thread_id', $thread->id)->get();
            $mod = $moderator->isMod($user->id, $subLolhow);
        }
        if ($thread->post) {
            $thread->post = Markdown::convertToHtml($thread->post);

            if ($userVotes && isset($userVotes->first()->thread_id) && $userVotes->first()->vote == 1) {
                // unlock hidden content
                $replace = $this->get_string_between($thread->post, '[hide]', '[/hide]');
            } else {
                $replace = "<p class='hidden-content'><strong>Upvote this post to view the hidden content</strong></p>";
            }
            $pattern = "/\[hide](.*)\[\/hide]/siU";
            $thread->post = preg_replace($pattern, $replace, $thread->post);
        }
        
        if ($request->segment(1) == 'amp') {
            return view('threads.amp_thread', array('thread' => $thread, 'subLolhow' => $subLolhow, 'userVotes' => $userVotes, 'mod' => $mod, 'bet' => $bets));
        } else {
            return view('threads.thread', array('thread' => $thread, 'subLolhow' => $subLolhow, 'userVotes' => $userVotes, 'mod' => $mod, 'bet' => $bets, 'poll' => $polls));
        }
    }

    function get_string_between($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }
}
