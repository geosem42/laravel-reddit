<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Thread;
use App\Vote;
use Illuminate\Http\Request;
use App\mediaUpload;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Post;
use App\Alert;

class commentsController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function addComment(Request $request, Thread $thread, Post $post)
    {
        $user = Auth::guard('api')->user();

        $comment = htmlspecialchars($request->input('comment'));
        if (strlen($comment) < 10) {
            return Response()->json([
                'warning' => "comment must be at least 10 characters"
            ], 200);
        }

        $last_comment = $post->where('user_id', $user->id)->orderBy('timestamp', 'desc')->first();

        if ($last_comment) {
            if (time() - 60 < $last_comment->timestamp) {
                $left = 60 - (time() - $last_comment->timestamp);
                return Response()->json([
                    'warning' => "You are going to fast, try again in " . $left . " seconds"
                ], 200);
            }
        }

        $comment = preg_replace("/(\r?\n){2,}/", "\n\n", $comment);

        $thread = $thread->where('id', $request->input('thread'))->first();
        if (!$thread) {
            return Response()->json([
                'error' => 'thread not found'
            ], 404);
        }
        $alert = new Alert();
        $alert->thread_id = $thread->id;

        $parent = $request->input('parent');
        if ($parent) {
            $parent = $post->where('id', $parent)->first();
            if (!$parent) {
                return Response()->json([
                    'error' => 'parent not found'
                ], 404);
            }
            $alert->user_id = $parent->user_id;
            $alert->post_id = $parent->id;
            $alert->active = true;
            $alert->code = $alert->getCode();
        } else {
            $alert->user_id = $thread->poster_id;
            $alert->active = true;
            $alert->code = $alert->getCode();
        }

        $post = new Post();
        $post->user_id = $user->id;
        $post->thread_id = $thread->id;
        if ($parent) {
            $post->parent_id = $parent->id;
        }
        $post->comment = $comment;
        $post->timestamp = time();
        $post->score = 0;
        $post->save();

        if ($alert) {
            $alert->reply_post_id = $post->id;
            $alert->save();
        }

        $thread->reply_count = $thread->reply_count + 1;
        $thread->save();

        return Response()->json([
            'status' => 'success',
            'post' => $post->toArray()
        ], 200);
    }

    public function loadComments(Request $request, Thread $thread, Post $post, Vote $vote)
    {
        $sort = $request->input('sort');
        $page = $request->input('page');

        if (!$page || !is_numeric($page)) {
            $page = 1;
        }
        $take = $page * 200;
        $skip = $take - 200;

        $thread = $thread->where('id', $request->input('thread'))->first();
        if (!$thread) {
            return Response()->json([
                'error' => 'thread not found'
            ], 404);
        }

        if ( (!$sort) || $sort == 'popular' ) {
            $comments = $post->select('posts.id', 'username as user_display_name', 'parent_id', 'upvotes', 'downvotes', 'score', 'comment', 'posts.created_at')
                ->join('users', 'posts.user_id', '=', 'users.id')
                ->where('thread_id', $thread->id)
                ->orderBy('score', 'desc')
                ->skip($skip)->take($take)->get();
            $commentParentIds = $comments->pluck('parent_id')->toArray();
            $head_comments = $post->whereIn('id', $commentParentIds)->orderBy('score', 'desc')->orderBy('created_at', 'desc')->get();
            $comments = $head_comments->merge($comments);
            //this trash needs a lot of improvement i know ffs
        } else if ($sort == 'new') {
            $comments = $post->select('posts.id', 'username as user_display_name', 'parent_id', 'upvotes', 'downvotes', 'score', 'comment', 'posts.created_at')
                ->join('users', 'posts.user_id', '=', 'users.id')
                ->where('thread_id', $thread->id)
                ->orderBy('created_at', 'DESC')
                ->skip($skip)->take($take)->get();
        } else {
            return Response()->json([
                'error' => 'no such sorting method'
            ], 502);
        }

        $user = Auth::guard('api')->user();
        $userVotes = null;
        if ($user) {
            $commentsId = $comments->pluck('id')->toArray();
            $userVotes = $vote->where('user_id', $user->id)->whereIn('post_id', $commentsId)->get()->toArray();
        }

        return Response()->json([
            'status' => 'success',
            'posts' => $comments->toArray(),
            'upvotes' => $userVotes
        ], 200);
    }

}
