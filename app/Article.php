<?php

namespace App;

use Carbon\Carbon;
use phpDocumentor\Reflection\DocBlock\Tag;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'title',
        'body',
        'published_at'
    ];

    protected $dates = ['published_at'];

    public function scopePublished($query) {
        $query->where('published_at', '<=', Carbon::now());
    }

    public function setPublishedAtAttribute($date) {
        $this->attributes['published_at'] = Carbon::createFromFormat('Y-m-d', $date);
    }

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function tags() {
        return $this->belongsToMany('App\Tag')->withTimestamps();
    }

    public function image() {
        return $this->hasMany('App\Image');
    }

    public function getTagListAttribute() {
        return $this->tags()->lists('id')->all();
    }
}
