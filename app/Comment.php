<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'comments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['post_id', 'comment', 'user_id'];

    /**
     * Get comments that have no parents
     * 
     * @return Collection
     */
    public static function root_comments($postId) {
        return Comment::child_comments(0, 'desc')->where('post_id', $postId);
    }

    /**
     * Get Child Comments for the given $parent_id
     * 
     * @return Collection
     */
    public static function child_comments($parent_id, $order='asc'){
        return Comment::where('parent_id', $parent_id)->orderBy('created_at', $order)->get();
    }

    /**
     * Gets Parent Comment object
     * 
     * @return null/Comment
     */
    public function parent(){
        $result = null;
        if($this->parent_id > 0)
            $result = self::find($this->parent_id);
        return $result;
    }

    public function posts() {
        return $this->belongsTo('App\Post');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }
}