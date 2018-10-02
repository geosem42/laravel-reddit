<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\Math;

class Subscription extends Model
{
    protected $table = 'subscriptions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'sub_lolhow_id'
    ];

    public function subscribed($user_id, $sub_lolhow_id)
    {
        return $this->where('user_id', $user_id)->where('sub_lolhow_id', $sub_lolhow_id)->first();
    }

    public function subscriptions($user_id)
    {
        return $this->select('user_id', 'sub_lolhow_id', 'name')
            ->join('sub_lolhows', 'subscriptions.sub_lolhow_id', '=', 'sub_lolhows.id')
            ->where('user_id', $user_id)->get();
    }

    public function sublolname()
    {
        return $this->hasMany('App\subLolhow', 'id', 'sub_lolhow_id');
    }

    public function threadcount()
    {
        return $this->hasMany('App\Thread', 'sub_lolhow_id', 'sub_lolhow_id');
    }

}
