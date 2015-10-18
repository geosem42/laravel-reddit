<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comments';

    protected $fillable = ['comment', 'user_id'];

    public function posts() {
        return $this->belongsTo('App\Post');
    }

    public static function root_comments($postId){
        return self::child_comments(0, 'desc')->where('post_id', $postId);
    }

    public static function child_comments($parent_id, $order='asc'){
        return self::where('parent_id', $parent_id)->orderBy('created_at', $order)->get();
    }

    public function parent(){
        $result = null;
        if($this->parent_id > 0)
            $result = self::find($this->parent_id);
        return $result;
    }
}
