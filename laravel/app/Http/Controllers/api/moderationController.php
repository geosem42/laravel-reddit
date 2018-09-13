<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Moderator;
use App\Thread;
use App\Post;
use Illuminate\Support\Facades\Auth;
use App\subPlebbit;

class moderationController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteThread($code, Request $request, Thread $thread, subPlebbit $subPlebbit, Moderator $moderator)
    {
        $user = Auth::guard('api')->user();

        $thread = $thread->where('code', $code)->first();
        if (!$thread) {
            return response()->json([
                'status' => 'error',
                'message' => 'Thread not found'
            ], 200);
        }

        $sub_plebbit = $subPlebbit->select('id', 'owner_id')->where('id', $thread->sub_plebbit_id)->first();
        if (!$subPlebbit) {
            return response()->json([
                'status' => 'error',
                'message' => 'SubPlebbit not found'
            ], 200);
        }

        $mod = $moderator->isMod($user->id, $sub_plebbit);
        if (!$mod) {
            return response()->json([
               'status' => 'error',
               'message' => "You are not allowed to moderate this subplebbit"
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

    public function deleteComment($id,  Request $request, Post $post, subPlebbit $subPlebbit, Moderator $moderator, Thread $thread)
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

        $sub_plebbit = $subPlebbit->select('id', 'owner_id')->where('id', $thread->sub_plebbit_id)->first();
        if (!$subPlebbit) {
            return response()->json([
                'status' => 'error',
                'message' => 'SubPlebbit not found'
            ], 200);
        }

        $mod = $moderator->isMod($user->id, $sub_plebbit);
        if (!$mod) {
            return response()->json([
                'status' => 'error',
                'message' => "You are not allowed to moderate this subplebbit"
            ]);
        }

        $post->comment = 'Deleted';
        $post->save();

        return response()->json([
            'status' => 'success'
        ]);
    }

}
