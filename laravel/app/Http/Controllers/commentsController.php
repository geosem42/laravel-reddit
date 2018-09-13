<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Thread;
use App\Vote;
use Illuminate\Support\Facades\Auth;
use App\subPlebbit;
use GrahamCampbell\Markdown\Facades\Markdown;
use App\Moderator;

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
    public function index($name, $code, Request $request, Thread $thread, Vote $vote, subPlebbit $plebbit, Moderator $moderator)
    {
        $thread = $thread->where('code', $code)->first();
        $subPlebbit = $plebbit->where('name', $name)->first();
        $mod = false;

        if ( (!$subPlebbit) || (!$thread) ) {
            return view('threads.not_found');
        }
        if ($subPlebbit->id != $thread->sub_plebbit_id) {
            flash('The thread was not found in the subplebbit', 'warning');
            return redirect('/');
        }
        $userVotes = false;
        if (Auth::check()) {
            $user = Auth::user();
            $userVotes = $vote->where('user_id', $user->id)->where('thread_id', $thread->id)->get();
            $mod = $moderator->isMod($user->id, $subPlebbit);
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
            return view('threads.amp_thread', array('thread' => $thread, 'subPlebbit' => $subPlebbit, 'userVotes' => $userVotes, 'mod' => $mod));
        } else {
            return view('threads.thread', array('thread' => $thread, 'subPlebbit' => $subPlebbit, 'userVotes' => $userVotes, 'mod' => $mod));
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
