<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{

    protected $fillable = [
        'title',
        'link',
        'text',
        'image',
        'subirt_id'
    ];

    // this was preventing the Votes relation from working..
    //    protected $primaryKey = 'subirt_id';

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function subirt() {
        return $this->belongsTo('App\Subirt');
    }

    public function votes() {
        return $this->hasMany('App\Vote');
    }

    public function moderators() {
        return $this->hasMany('App\Moderator');
    }

    public function comments() {
        return $this->hasMany('App\Comment');
    }
}