<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommentVote extends Model
{
    protected $table = 'commentvotes';

    protected $fillable = [
        'value',
        'comment_id',
        'user_id'
    ];

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function posts() {
        return $this->belongsTo('App\Comment');
    }
}
