<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Thread;
use Illuminate\Support\Facades\Auth;
use App\Vote;
use App\Post;
use App\User;

class votesController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function vote($code, Request $request, Thread $thread, Vote $vote, Post $post_model, User $user)
    {
        $type = $request->input('type');

        $user = Auth::guard('api')->user();

        if ($type == 'thread') {
            $post = $thread->where('code', $code)->first();
            if (!$post) {
                return response()->json([
                    'error' => 'Thread not found'
                ], 404);
            }
            $user_to_receive = $user->where('id', $post->poster_id)->first();
            $newVote = $vote->where('thread_id', $post->id)->where('user_id', $user->id)->first();
        }
        else if ($type == 'post') {
            $post = $post_model->where('id', $code)->first();
            if (!$post) {
                return response()->json([
                    'error' => 'Post not found'
                ], 404);
            }
            $user_to_receive = $user->where('id', $post->user_id)->first();
            $newVote = $vote->where('post_id', $post->id)->where('user_id', $user->id)->first(); //change to post
        }
        else {
            return Response()->json([
                'error' => 'Invalid type'
            ], 405);
        }

        $voteType = $request->input('vote');

        if (!$newVote) {
            $newVote = new Vote();
            $newVote->user_id = $user->id;
            if ($type == 'thread') {
                $newVote->thread_id = $post->id;
                if ($voteType == 'up') {
                    $user_to_receive->thread_karma = $user_to_receive->thread_karma + 1;
                }
                if ($voteType == 'down') {
                    $user_to_receive->thread_karma = $user_to_receive->thread_karma - 1;
                }
            } else {
                $newVote->post_id = $post->id; // change to post id
                if ($voteType == 'up') {
                    $user_to_receive->post_karma = $user_to_receive->post_karma + 1;
                }
                if ($voteType == 'down') {
                    $user_to_receive->post_karma = $user_to_receive->post_karma - 1;
                }
            }
        } else {
            if ($type == 'thread') {
                if ($voteType == 'up') {
                    if ($newVote->vote !== 1) {
                        $user_to_receive->thread_karma = $user_to_receive->thread_karma + 2;
                    }
                }
                if ($voteType == 'down') {
                    if ($newVote->vote !== 0) {
                        $user_to_receive->thread_karma = $user_to_receive->thread_karma - 2;
                    }
                }
            }
            if ($type == 'post') {
                if ($voteType == 'up') {
                    if ($newVote->vote !== 1) {
                        $user_to_receive->post_karma = $user_to_receive->post_karma + 2;
                    }
                }
                if ($voteType == 'down') {
                    if ($newVote->vote !== 0) {
                        $user_to_receive->post_karma = $user_to_receive->post_karma - 2;
                    }
                }
            }
        }

        if ($voteType == 'up') {
            $newVote->vote = 1;
        }
        if ($voteType == 'down') {
            $newVote->vote = 0;
        }
        $newVote->save();

        if ($type == 'thread') {
            $post->upvotes = $vote->where('thread_id', $post->id)->where('vote', 1)->count();
            $post->downvotes = $vote->where('thread_id', $post->id)->where('vote', 0)->count();
            $post->score = $post->upvotes - $post->downvotes;
        }
        if ($type == 'post') {
            $post->upvotes = $vote->where('post_id', $post->id)->where('vote', 1)->count();
            $post->downvotes = $vote->where('post_id', $post->id)->where('vote', 0)->count();
            $post->score = $post->upvotes - $post->downvotes;
        }
        $post->save();
        $user_to_receive->save();

        return Response()->json([
            'status' => 'success',
            'post' => $post->id,
            'votes' => $post->score
        ]);
    }
}
