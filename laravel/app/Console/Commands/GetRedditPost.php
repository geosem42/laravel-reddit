<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use RedditAPI;

class GetRedditPost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reddit:post';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
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

        $thread = new Thread();

        $subLolhow = $subLolhow->where('name', "admin")->first();

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



    }
}
