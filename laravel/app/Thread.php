<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\Math;

class Thread extends Model
{
    protected $table = 'threads';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'title', 'poster_id', 'reply_count', 'upvotes', 'downvotes', 'score', 'sub_lolhow_id', 'type', 'link', 'media_type', 'thumbnail', 'post', 'sticky'
    ];

    public function getCode()
    {
        $last = $this->orderBy('id', 'DESC')->first();
        $math = new Math();
        if (!$last) {
            return $math->toBase(0 + 1000000 + 1);
        }
        return $math->toBase($last->id + 1000000 + 1);
    }

    public function subLolhow()
    {
        return $this->hasOne('App\subLolhow', 'id', 'sub_lolhow_id');
    }

    public function threadsByUser($id, $sort, $skip, $amount)
    {
        if ($sort == 'popular') {
            return $this->where('poster_id', $id)->orderBy('score', 'DESC')->orderBy('created_at', 'DESC')->skip($skip)->take($amount)->get();
        }
        else if ($sort == 'top') {
            return $this->where('poster_id', $id)->orderBy('score', 'DESC')->skip($skip)->take($amount)->get();
        }
        else if ($sort == 'new') {
            return $this->where('poster_id', $id)->orderBy('created_at', 'DESC')->skip($skip)->take($amount)->get();
        }
        else {
            return false;
        }
    }

    public function isDeleted($thread)
    {
        if ( ($thread->type == 'text') && ($thread->link == null) && ($thread->media_type == null) && ($thread->thumbnail == null) && ($thread->post = 'Deleted') ) {
            return true;
        } else {
            return false;
        }
    }

}
