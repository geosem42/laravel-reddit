<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Subreddit extends Model
{
    protected $fillable = [
        'name',
        'description'
    ];

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function posts() {
        return $this->hasMany('App\Post');
    }

    public function moderators() {
        return $this->hasMany('App\Moderator');
    }

    public function comments() {
        return $this->hasMany('App\Comment');
    }
}
