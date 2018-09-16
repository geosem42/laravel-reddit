<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\Math;

class Messaging extends Model
{
    protected $table = 'private_messages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'code', 'user_id', 'to_user_id', 'main_msg_id', 'subject', 'message', 'active', 'last_pm_timestamp'
    ];

    public function getNewMessagesByUser($user_id)
    {
        return $this->select( 'username', 'code', 'message')
            ->join('users', 'private_messages.user_id', '=', 'users.id')
            ->where('private_messages.to_user_id', $user_id)
            ->where('private_messages.active', 1)
            ->orderBy('private_messages.created_at', 'desc')
            ->take(20)->get();
    }

    public function getCode()
    {
        $last = $this->orderBy('id', 'DESC')->first();
        $math = new Math();
        if (!$last) {
            return $math->toBase(0 + 1000000 + 1);
        }
        return $math->toBase($last->id + 1000000 + 1);
    }

}
