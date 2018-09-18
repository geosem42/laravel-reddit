<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class mediaUpload extends Model
{
    protected $table = 'media_upload';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'file', 'delete_key', 'valid_until'
    ];

}
