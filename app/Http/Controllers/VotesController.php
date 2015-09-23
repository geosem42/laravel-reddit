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
        $postId = $request->input('postId');
        $userId = $request->user()->id;
        $value = $request->input('value');

        // Check to see if there is an existing vote
        $vote = Vote::wherePostId($postId)->whereUserId($userId)->first();
        if (!$vote)
        {
            // First time the user is voting
           Vote::create(['post_id' => $postId, 'user_id' => $userId, 'value' => $value]);
        } else {
            $vote->value == $value ? $vote->delete() : $vote->update(['value' => $value]);
        }
        // AJAX JSON RESPONSE
        return response()->json(['status' => 'success',
            'msg' => 'Vote has been added.']);
    }
}
