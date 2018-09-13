<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    protected $table = 'votes';

    protected $fillable = [
        'value',
        'post_id',
        'user_id'
    ];

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function posts() {
        return $this->belongsTo('App\Post');
    }
}
