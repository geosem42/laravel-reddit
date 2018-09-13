<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\Math;

class Post extends Model
{
    protected $table = 'posts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'user_display_name', 'thread_id', 'parent_id', 'upvotes', 'downvotes', 'score', 'comment', 'timestamp'
    ];

    public function postsbyUser($id, $sort, $skip, $amount)
    {
        if ($sort == 'popular') {
            return $this->select('posts.id', 'thread_id', 'username as user_display_name', 'parent_id', 'upvotes', 'downvotes', 'score', 'comment', 'posts.created_at')
                ->join('users', 'posts.user_id', '=', 'users.id')
                ->where('user_id', $id)
                ->orderBy('score', 'DESC')
                ->orderBy('created_at', 'DESC')
                ->skip($skip)->take($amount)->get();
        }
        else if ($sort == 'top') {
            return $this->select('posts.id', 'thread_id', 'username as user_display_name', 'parent_id', 'upvotes', 'downvotes', 'score', 'comment', 'posts.created_at')
                ->join('users', 'posts.user_id', '=', 'users.id')
                ->where('user_id', $id)
                ->orderBy('score', 'DESC')
                ->skip($skip)->take($amount)->get();
        }
        else if ($sort == 'new') {
            return $this->select('posts.id', 'thread_id', 'username as user_display_name', 'parent_id', 'upvotes', 'downvotes', 'score', 'comment', 'posts.created_at')
                ->join('users', 'posts.user_id', '=', 'users.id')
                ->where('user_id', $id)
                ->orderBy('created_at', 'DESC')
                ->skip($skip)->take($amount)->get();
        }
        else {
            return false;
        }
    }
}
