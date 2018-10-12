<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Moderator extends Model
{
    protected $table = 'moderators';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'sub_lolhow_id'
    ];

    public function getBySubLolhowId($id)
    {
        return $this->select('user_id', 'sub_lolhow_id', 'username')
            ->join('users', 'moderators.user_id', '=', 'users.id')
            ->where('sub_lolhow_id', $id)->get();
    }

    public function validateMods($mods_string) {
        $mods = explode(',', $mods_string);

        $invalid = '';
        foreach ($mods as $mod) {
            $u = User::where('username', $mod)->first();
            if (!$u) {
                $invalid.= $mod . ',';
            }
        }

        if ($invalid == '') {
            return true;
        }
        return false;
    }

    public function isMod($user_id, $sub_lolhow)
    {
        if (env('ADMIN_ID') == $user_id) {
            return true;
        }
        if ($user_id == $sub_lolhow->owner_id) {
            return true;
        }
        $check = $this->where('user_id', $user_id)->where('sub_lolhow_id', $sub_lolhow->id)->first();
        if ($check) {
            return true;
        } else {
            return false;
        }
    }

}
