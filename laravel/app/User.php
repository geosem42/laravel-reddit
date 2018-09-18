<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

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

    public function searchByName($query)
    {
        return $this->select('username AS name')->where('username', 'LIKE', '%' . $query . '%')->orderBy('username', 'asc')->take(10)->get();
    }
}
