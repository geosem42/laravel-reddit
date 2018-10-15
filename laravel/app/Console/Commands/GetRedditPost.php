<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use RedditAPI;
use Embed\Http\CurlDispatcher;
use App\Thread;
use App\subLolhow;
use Embed\Embed;
use Embed\Exceptions\InvalidUrlException;
use File;
use Image;
use Intervention\Image\Exception\NotReadableException;
use App\User;

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
        $postType = ["nfl", "gifs", "news", "worldnews", "politics", "sports", "movies"];

        foreach ($postType as $type) {
            $postData = RedditAPI::search($type, $type, 'top', null, null, 50);    

            if (!empty($postData->data->children)) {
                foreach ($postData->data->children as $key => $value) {
                    $valueData = (array)$value->data;
                    $user = User::where("username", "admin")->first();

                    if (!Thread::where("title", $valueData['title'])->exists() && !empty($user)) {
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
                        $subLolhow = subLolhow::where('name', $type)->first();

                        $thread->code = $thread->getCode();
                        $thread->title = $valueData['title'];
                        $thread->poster_id = $user->id;
                        $thread->sub_lolhow_id = $subLolhow->id;

                        if (!empty($valueData['url'])) {
                            $thread->type = 'link';
                            $thread->link = $valueData['url'];
                            // Make a thumbnail
                            if (env('USE_CURL_PROXY') == 'yes') {
                                try {
                                    $info = Embed::create($valueData['url'], null, $dispatcher);
                                } catch (InvalidUrlException $e) {
                                    $info = null;
                                }
                            } else {
                                try {
                                    $info = Embed::create($valueData['url']);
                                } catch (InvalidUrlException $e) {
                                    $info = null;
                                }
                            }
                            $jpg = str_contains($valueData['url'], '.jpg');
                            $png = str_contains($valueData['url'], '.png');
                            $gif = str_contains($valueData['url'], '.gif');
                            $webm = str_contains($valueData['url'], '.webm');
                            if ($jpg || $png || $gif || $webm) {
                                $orig = pathinfo($valueData['url'], PATHINFO_EXTENSION);
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
                            
                            if ($info) {
                                $media_type = $info->getResponse()->getContentType();
                                if (strpos($media_type, 'image') !== false) {
                                    $thread->media_type = 'image';
                                }
                                if (strpos($media_type, 'video') !== false) {
                                    $thread->media_type = 'video';
                                }
                                $parse = parse_url($valueData['url']);
                                if ($parse['host'] == 'www.youtube.com' || $parse['host'] == 'youtube.com' || $parse['host'] == 'youtu.be' || $parse['host'] == 'www.youtu.be') {
                                    $thread->media_type = 'youtube';
                                }
                                if ($parse['host'] == 'www.vimeo.com' || $parse['host'] == 'vimeo.com') {
                                    $thread->media_type = 'vimeo';
                                }
                            }
                        } else {
                            $thread->type = 'text';
                            $thread->post = $valueData['selftext'];
                        }


                        $thread->save();

                    }


                }
            }
        }

    }
}
