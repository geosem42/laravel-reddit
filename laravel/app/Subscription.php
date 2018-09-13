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
        'user_id', 'sub_plebbit_id'
    ];

    public function subscribed($user_id, $sub_plebbit_id)
    {
        return $this->where('user_id', $user_id)->where('sub_plebbit_id', $sub_plebbit_id)->first();
    }

    public function subscriptions($user_id)
    {
        return $this->select('user_id', 'sub_plebbit_id', 'name')
            ->join('sub_plebbits', 'subscriptions.sub_plebbit_id', '=', 'sub_plebbits.id')
            ->where('user_id', $user_id)->get();
    }

}
