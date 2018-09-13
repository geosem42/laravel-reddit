<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\mediaUpload;
use Illuminate\Support\Facades\Storage;

class mediaUploadController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request)
    {
        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension());
        $size = $file->getClientSize();
        $name = $file->getClientOriginalName();

        $name = explode('.'.$ext,$name)[0];
        $name = str_replace(' ', '_', $name);
        if (strlen($name) > 20) {
            $name = substr($name, 0, 20);
        }

        $allowed = array('jpg', 'png', 'gif', 'mp4', 'webm', 'jpeg');
        if (!in_array($ext, $allowed)) {
            return Response('Invalid format', 500);
        }
        if ($size > 4500000) {
            return response('File is too big', 500);
        }
        $newName = $name . '-' . str_random(30) . '.' . $ext;
        $file->move('media/', $newName);

        $delete_key = str_random(40);

        $mediaupload = new mediaUpload();
        $mediaupload->file = $newName;
        $mediaupload->delete_key = $delete_key;
        $mediaupload->valid_until = strtotime('+10 min', time());
        $mediaupload->save();

        return response()->json([
            'link' => $newName,
            'key' => $delete_key
        ], 200);
    }

    public function deleteFile($key, Request $request, mediaUpload $mediaUpload)
    {
        $file = $mediaUpload->where('delete_key', $key)->first();

        if ( (!$file) || (time() > $file->valid_until) ) {
            return response()->json([
                'error' => 'Key invalid or file expired'
            ], 401);
        }

        unlink('media/' . $file->file);

        $file->delete();
        return response()->json([
            'status' => 'success'
        ], 200);

    }
}
