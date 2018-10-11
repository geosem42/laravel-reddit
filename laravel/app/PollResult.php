<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PollResult extends Model
{
    public function option_name()
    {
    	return $this->hasOne('App\PollOption', 'id', 'choise_id');
    }

    public function poll_details()
    {
    	return $this->hasOne('App\Poll', 'id', 'poll_id');
    }
}
