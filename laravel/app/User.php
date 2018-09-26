<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $appends = array('karma_color');

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password', 'api_token', 'active', 'activation_token', 'thread_karma', 'post_karma'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getKarmaColorAttribute()
    {
        return self::getUsernameColorByKaram($this->thread_karma);  
    }

    public function searchByName($query)
    {
        return $this->select('username AS name')->where('username', 'LIKE', '%' . $query . '%')->orderBy('username', 'asc')->take(10)->get();
    }


    public static function getUsernameColorByKaram($karma)
    {
        $min = config('constant.KARMA_COLOR_MIN_VALUE');
        $max = config('constant.KARMA_COLOR_MAX_VALUE');

        if ($karma < $min) {
            return "text-danger";
        } else if ($karma >= $min && $karma <= $max) {
            return "text-orange";
        } else if ($karma > $max) {
            return "text-success";
        }

        return "";
    }
}
