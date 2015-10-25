<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    protected $table = 'users';

    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password', 'remember_token'];

    public function subreddit() {
        return $this->hasMany('App\Subreddit');
    }

    public function posts() {
        return $this->hasMany('App\Post');
    }

    public function votes() {
        return $this->hasManyThrough('App\Vote','App\Post');
    }

    public function commentvotes() {
        return $this->hasManyThrough('App\CommentVote','App\Comment');
    }

    public function moderators() {
        return $this->hasMany('App\Moderator');
    }

    public function comments() {
        return $this->hasMany('App\Comment');
    }
}
