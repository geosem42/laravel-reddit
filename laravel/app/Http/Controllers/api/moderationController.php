<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Moderator;
use App\Thread;
use App\Post;
use Illuminate\Support\Facades\Auth;
use App\subLolhow;

class moderationController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteThread($code, Request $request, Thread $thread, subLolhow $subLolhow, Moderator $moderator)
    {
        $user = Auth::guard('api')->user();

        $thread = $thread->where('code', $code)->first();
        if (!$thread) {
            return response()->json([
                'status' => 'error',
                'message' => 'Thread not found'
            ], 200);
        }

        $sub_lolhow = $subLolhow->select('id', 'owner_id')->where('id', $thread->sub_lolhow_id)->first();
        if (!$subLolhow) {
            return response()->json([
                'status' => 'error',
                'message' => 'SubLolhow not found'
            ], 200);
        }

        $mod = $moderator->isMod($user->id, $sub_lolhow);
        if (!$mod) {
            return response()->json([
               'status' => 'error',
               'message' => "You are not allowed to moderate this sublolhow"
            ]);
        }

        $thread->type = 'text';
        $thread->link = null;
        $thread->media_type = null;
        $thread->thumbnail = null;
        $thread->post = 'Deleted';
        $thread->save();

        return response()->json([
           'status' => 'success'
        ]);
    }

    public function deleteComment($id,  Request $request, Post $post, subLolhow $subLolhow, Moderator $moderator, Thread $thread)
    {
        $user = Auth::guard('api')->user();

        $post = $post->where('id', $id)->first();
        if (!$post) {
            return response()->json([
                'status' => 'error',
                'message' => 'Comment not found'
            ], 200);
        }
        $thread = $thread->where('id', $post->thread_id)->first();

        $sub_lolhow = $subLolhow->select('id', 'owner_id')->where('id', $thread->sub_lolhow_id)->first();
        if (!$subLolhow) {
            return response()->json([
                'status' => 'error',
                'message' => 'SubLolhow not found'
            ], 200);
        }

        $mod = $moderator->isMod($user->id, $sub_lolhow);
        if (!$mod) {
            return response()->json([
                'status' => 'error',
                'message' => "You are not allowed to moderate this sublolhow"
            ]);
        }

        $post->comment = 'Deleted';
        $post->save();

        return response()->json([
            'status' => 'success'
        ]);
    }

}
