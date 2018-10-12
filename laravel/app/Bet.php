<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bet extends Model
{
    //
    public function options()
    {
    	return $this->hasMany('App\BetOption', 'bet_id', 'id');
    }
}
