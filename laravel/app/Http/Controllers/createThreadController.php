<?php

namespace App\Http\Controllers;

use App\Moderator;
use Embed\Exceptions\InvalidUrlException;
use Illuminate\Http\Request;
use App\subLolhow;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Factory as ValidationFactory;
use Intervention\Image\Exception\NotReadableException;
use Validator;
use Image;
use Embed\Embed;
use File;
use App\Thread;
use Embed\Http\CurlDispatcher;

class createThreadController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ValidationFactory $validationFactory)
    {
        $this->middleware('auth');

        $validationFactory->extend(
            'sublolhow',
            function ($attribute, $value, $parameters) {
                $lolhow = new subLolhow();
                $mod = new Moderator();
                $lolhow = $lolhow->select('id', 'name')->where('name', $value)->first();
                if (!$lolhow) {
                    return false;
                } else {
                    if ($lolhow->name == env('OFFICIAL_SUB_PLEBBIT') && !$mod->isMod(Auth::user()->id, $lolhow)) {
                        return false;
                    } else {
                        return true;
                    }
                }
            },
            'This sublolhow does not exist'
        );
        $validationFactory->extend(
            'safe_url',
            function ($attribute, $value, $parameters) {
                $link = $value;
                if ( (!preg_match("~^(?:f|ht)tps?://~i", $link)) && (!empty($link)) ) {
                    $link = "http://" . $link;
                }
                $parse = parse_url($link);
                if ($parse['scheme'] != 'http' && $parse['scheme'] != 'https') {
                    return false;
                }
                return true;
            },
            'Url not allowed'
        );
    }


    public function getCreateThread(Request $request)
    {
        $name = $request->segment(2);

        return view('submitThread', array('name' => $name));
    }

    public function postCreateThread(Request $request, subLolhow $subLolhow)
    {
        if (env('USE_CURL_PROXY') == 'yes') {
            $dispatcher = new CurlDispatcher([
                CURLOPT_MAXREDIRS => 20,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_ENCODING => '',
                CURLOPT_AUTOREFERER => false,
                CURLOPT_USERAGENT => 'Lolhow bot',
                CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                CURLOPT_PROXY => env('CURL_PROXY')
            ]);
        } else {
            $dispatcher = new CurlDispatcher([
                CURLOPT_MAXREDIRS => 20,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_ENCODING => '',
                CURLOPT_AUTOREFERER => false,
                CURLOPT_USERAGENT => 'Lolhow bot',
                CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            ]);
        }

        $validator = Validator::make($request->all(), [
            'title' => "required|min:5:max:100",
            'sublolhow' => 'sublolhow',
            'url' => 'safe_url',
            'post' => 'max:100000'
        ]);

        if ($request->input('type') == 'link') {
            $link = $request->input('url');

            if ( (!preg_match("~^(?:f|ht)tps?://~i", $link)) && (!empty($link)) ) {
                $link = "http://" . $link;
            }

            if ($validator->fails())
            {
                return redirect('/submit?type=link')->withErrors($validator)->withInput();
            }
        }
        else if ($request->input('type') == 'text') {
            if ($validator->fails())
            {
                return redirect('/submit?type=text')->withErrors($validator)->withInput();
            }
        }
        else {
            return Response('no...', 404);
        }


        $thread = new Thread();

        $subLolhow = $subLolhow->where('name', $request->input('sublolhow'))->first();

        $thread->code = $thread->getCode();
        $thread->title = $request->input('title');
        $thread->poster_id = Auth::user()->id;
        $thread->sub_lolhow_id = $subLolhow->id;
        if ($request->input('type') == 'link') {
            $thread->type = 'link';
            $thread->link = $link;
            // Make a thumbnail
            if (env('USE_CURL_PROXY') == 'yes') {
                try {
                    $info = Embed::create($link, null, $dispatcher);
                } catch (InvalidUrlException $e) {
                    $info = null;
                }
            } else {
                try {
                    $info = Embed::create($link);
                } catch (InvalidUrlException $e) {
                    $info = null;
                }
            }
            $jpg = str_contains($link, '.jpg');
            $png = str_contains($link, '.png');
            $gif = str_contains($link, '.gif');
            $webm = str_contains($link, '.webm');
            if ($jpg || $png || $gif || $webm) {
                $orig = pathinfo($link, PATHINFO_EXTENSION);
                $qmark = str_contains($orig, '?');
                if($qmark == false) {
                    $extension = $orig;
                } else {
                    $extension = substr($orig, 0, strpos($orig, '?'));
                }
                $newName = 'images/lolhows/thumbnails/' . str_random(10) . '.' .  $extension;
                if (File::exists($newName)) {
                    $imageToken = substr(sha1(mt_rand()), 0, 5);
                    $newName = 'images/lolhows/thumbnails/' . str_random(10) . '-' . $imageToken . ".{$extension}";
                }
                try {
                    $image = Image::make($link)->fit(78, 78)->save($newName);
                    $thread->thumbnail = url('/') . '/' . $newName;
                } catch(NotReadableException $e) {
                    // If error, stop and continue looping to next iteration
                }
            }
            if (isset($info->image) && $info->image !== null) {
                $orig = pathinfo($info->image, PATHINFO_EXTENSION);
                $qmark = str_contains($orig, '?');
                if($qmark == false) {
                    $extension = $orig;
                } else {
                    $extension = substr($orig, 0, strpos($orig, '?'));
                }
                $newName =  'images/lolhows/thumbnails/' . str_random(8) . ".{$extension}";
                if (File::exists($newName)) {
                    $imageToken = substr(sha1(mt_rand()), 0, 5);
                    $newName = 'images/lolhows/thumbnails/' . str_random(8) . '-' . $imageToken . ".{$extension}";
                }
                try {
                    $image = Image::make($info->image)->fit(78, 78)->save($newName);
                    $thread->thumbnail = url('/') . '/' . $newName;
                } catch(NotReadableException $e) {
                    // If error, stop and continue looping to next iteration
                }
            }
            if ($info) {
                $media_type = $info->getResponse()->getContentType();
                if (strpos($media_type, 'image') !== false) {
                    $thread->media_type = 'image';
                }
                if (strpos($media_type, 'video') !== false) {
                    $thread->media_type = 'video';
                }
                $parse = parse_url($link);
                if ($parse['host'] == 'www.youtube.com' || $parse['host'] == 'youtube.com' || $parse['host'] == 'youtu.be' || $parse['host'] == 'www.youtu.be') {
                    $thread->media_type = 'youtube';
                }
                if ($parse['host'] == 'www.vimeo.com' || $parse['host'] == 'vimeo.com') {
                    $thread->media_type = 'vimeo';
                }
            }
        }

        if ($request->input('type') == 'text') {
            $thread->type = 'text';
            $thread->post = $request->input('text');
        }
        $thread->save();
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $thread->title)));

        return Redirect('/p/' . $subLolhow->name . '/comments/' . $thread->code . '/' . str_slug($slug));
    }

}
