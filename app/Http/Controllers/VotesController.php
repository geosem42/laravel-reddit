<?php

namespace App\Http\Controllers;

use App\User;
use App\Vote;
use App\Subreddit;
use App\Post;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Requests\PostRequest;
use App\Http\Requests\VoteRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Database\Eloquent\Model;


class VotesController extends Controller
{

    public function __construct() {
        $this->middleware('auth', ['only' => ['create', 'edit'] ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * NOTE: You are using a FormRequest without any sort of validation.. revise?
     *
     * @param Requests\VoteRequest|Request $request
     * @param $id
     * @return Response
     */
    public function store(Requests\VoteRequest $request)
    {
        $vote = \App\Vote::firstOrNew(['post_id' => $request->input('postId'), 'user_id' => $request->user()->id]);
        $vote->user_id = auth()->user()->id;
        $vote->value = $request->input('value');
        $vote->save();
        // AJAX JSON RESPONSE
        return response()->json(['status' => 'success',
            'msg' => 'Vote has been added.']);
    }
}
