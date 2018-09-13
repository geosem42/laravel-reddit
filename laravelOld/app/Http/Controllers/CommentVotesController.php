<?php

namespace App\Http\Controllers;

use App\CommentVote;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class CommentVotesController extends Controller
{
    public function __construct() {
        $this->middleware('auth', ['only' => ['create', 'edit'] ]);
    }

    public function store(Requests\CommentVoteRequest $request)
    {
        $commentId = $request->input('commentId');
        $userId = $request->user()->id;
        $value = $request->input('value');

        // Check to see if there is an existing vote
        $vote = CommentVote::whereCommentId($commentId)->whereUserId($userId)->first();
        if (!$vote)
        {
            // First time the user is voting
            CommentVote::create(['comment_id' => $commentId, 'user_id' => $userId, 'value' => $value]);
        } else {
            $vote->value == $value ? $vote->delete() : $vote->update(['value' => $value]);
        }
        // AJAX JSON RESPONSE
        return response()->json(['status' => 'success',
            'msg' => 'Vote has been added.']);
    }
}
