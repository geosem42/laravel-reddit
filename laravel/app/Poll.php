<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    public function bet_result()
    {
    	return $this->hasOne('App\BetResult', 'bet_id', 'bet_id');
    }

    public function poll_result()
    {
    	return $this->hasMany('App\UserPoll', 'poll_id', 'id');	
    }
}
