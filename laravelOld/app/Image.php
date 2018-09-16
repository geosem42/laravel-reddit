<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class Image extends Model
{
    public static $rules = [
        'file' => 'required|mimes:png,gif,jpeg,jpg,bmp'
    ];
    public static $messages = [
        'file.mimes' => 'Uploaded file is not in image format',
        'file.required' => 'Image is required'
    ];

    public function posts() {
        return $this->belongsTo('App\Post');
    }
}