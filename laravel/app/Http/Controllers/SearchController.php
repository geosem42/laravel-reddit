<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\subPlebbit;
use App\Thread;
use App\Vote;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function search($subplebbit = null, Request $request, subPlebbit $subPlebbit, Thread $thread, Vote $vote)
    {
        $page = $request->input('page');
        if (!is_numeric($page)) {
            $page = 1;
        }
        $rpage = $page;

        $query = $request->input('q');
        if ($subplebbit) {
            $take = 25;
            $skip = $page * $take - $take;
            $subplebbit = $subPlebbit->where('name', $subplebbit)->first();
            if ($subplebbit) {
                $subplebbit_id = $subplebbit->id;
            } else {
                $subplebbit_id = null;
            }
            $threads = $thread->where('sub_plebbit_id', $subplebbit_id)->where('title', 'LIKE', '%' . $query . '%')->orderBy('title', 'asc')->skip($skip)->take($take)->get();
            $userVotes = null;
            if (Auth::check()) {
                $threads_ids_array = $threads->pluck('id')->toArray();
                $userVotes = $vote->where('user_id', Auth::user()->id)->whereIn('thread_id', $threads_ids_array)->get();
            }

            return view('search.results_subplebbit')->with([
                'threads' => $threads,
                'subPlebbit' => $subplebbit,
                'userVotes' => $userVotes,
                'page' => $page
            ]);
        }

        if ($page == 1) {
            $skip = 0;
            $take = 5;
        } else if ($page == 2) {
            $skip = 5;
            $take = 20;
        } else {
            $page = $page - 1;
            $skip = 25 * $page - 25;
            $take = 25;
        }

        $type = $request->input('type');
        if ($type !== 'posts' && $type !== 'subplebbits') {
            $type = 'all';
        }

        if ($type == 'all' || $type == 'subplebbits') {
            $subplebbits = $subPlebbit->select('id', 'name', 'title', 'created_at')->where('name', 'LIKE', '%' . $query . '%')->orderBy('name', 'asc')->skip($skip)->take($take)->get();
        } else {
            $subplebbits = collect(new subPlebbit());
        }
        if ($type == 'all' || $type == 'posts') {
            $threads = $thread->where('title', 'LIKE', '%' . $query . '%')->orderBy('title', 'asc')->skip($skip)->take($take)->get();
        } else {
            $threads = collect(new Thread());
        }
        $userVotes = null;
        if (Auth::check()) {
            $threads_ids_array = $threads->pluck('id')->toArray();
            $userVotes = $vote->where('user_id', Auth::user()->id)->whereIn('thread_id', $threads_ids_array)->get();
        }
        return view('search.results_global')->with([
            'threads' => $threads,
            'subplebbits' => $subplebbits,
            'userVotes' => $userVotes,
            'page' => $rpage,
            'q' => $query
        ]);
    }
}
