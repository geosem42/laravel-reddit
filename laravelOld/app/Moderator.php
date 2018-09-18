<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Moderator extends Model
{
    //protected $table = 'moderators';

    protected $fillable = ['user_id', 'subirt_id'];

    public function subirt() {
        return $this->belongsTo('App\Subirt');
    }

    public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function posts() {
        return $this->belongsTo('App\Post');
    }
}
