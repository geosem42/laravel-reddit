<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Helpers\Math;
use App\Messaging;

class Alert extends Model
{
    protected $table = 'alerts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'post_id', 'active', 'code'
    ];

    public function getAlertsByUser($user_id)
    {
        $alertsArray = [];

        $mentions = $this->select( 'username as user_display_name', 'posts.thread_id', 'alerts.thread_id as alert_thread_id', 'comment', 'parent_id', 'title AS thread_title', 'alerts.code', 'alerts.created_at')
            ->join('posts', 'alerts.reply_post_id', '=', 'posts.id')
            ->join('users', 'posts.user_id', '=', 'users.id')
            ->join('threads', 'posts.thread_id', '=', 'threads.id')
            ->where('alerts.user_id', $user_id)
            ->where('alerts.active', 1)
            ->orderBy('alerts.created_at', 'desc')
            ->take(20)->get();
        foreach($mentions as $alert) {
            $alertsArray[strtotime($alert->created_at)] = [
                'created_at' => strtotime($alert->created_at),
                'user_display_name' => $alert->user_display_name,
                'type' => 'mention',
                'thread_id' => $alert->thread_id,
                'comment' => $alert->comment,
                'thread_title' => $alert->thread_title,
                'code' => $alert->code,
            ];
        }

        $pms = new Messaging();
        $pms = $pms->select('username as user_display_name', 'code', 'private_messages.created_at')
                ->join('users', 'private_messages.user_id', '=', 'users.id')
                ->where('private_messages.active', 1)
                ->where('to_user_id', $user_id)
                ->orderBy('private_messages.created_at', 'desc')
                ->take(20)->get();
        foreach ($pms as $alert) {
            $alertsArray[strtotime($alert->created_at)] = [
                'created_at' => strtotime($alert->created_at),
                'user_display_name' => $alert->user_display_name,
                'type' => 'pm',
                'code' => $alert->code,
            ];
        }

        arsort($alertsArray);

        return $alertsArray;
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
