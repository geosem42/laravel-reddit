@extends('layouts.amp_app')

@section('meta')
    <script type="application/ld+json">
      {
        "@context": "http://schema.org",
        "@type": "NewsArticle",
        "headline": "{{$thread->title}}",
        "datePublished": "{{$thread->created_at}}",
        @if($thread->media_type == 'image')
        "image": [
          "{{$thread->link}}"
        ]
        @endif
      }
    </script>
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500" rel="stylesheet">
    <link rel="canonical" href="{{ url('/') }}/p/{{$subLolhow->name}}/comments/{{$thread->code}}/{{ str_slug($thread->title) }}">
    <style amp-custom>
        @php
            include 'css/amp_grid.min.css';
        @endphp
        * {
            font-family: roboto, sans-serif;
            font-weight: 400;
        }
        #app {
            max-width: 768px;
            margin:auto;
        }
        .topNav {
            height: 48px;
            position: relative;
            box-shadow: 0 0 5px 0 rgba(0,0,0,.2);
            width: 100%;
            z-index: 11;
            -webkit-user-select: none;
            border-bottom: 1px solid #efefed;
            background-color: #fff;
        }
        #nav_header {
            position: absolute;
            z-index: 1;
            top: 15px;
            left: 10px;
            color: #737373;
        }
        #nav_img {
            margin-left: 65px;
        }
        a {
            color: #3097D1;
            text-decoration: none;
            font-weight:300;
        }
        #sublolhow_name {
            text-align: center;
            margin-top: 10px;
            font-size:14px;
        }
        h3 {
            color: #222;
        }
        #title h1 {
            font-weight:300;
            font-size: 16px;
            overflow-x: hidden;
            margin-top: 5px;
            line-height: normal;
        }
        #title h1 a {
            color: #222;
        }
        #poster_date {
            margin-top: -10px;
            font-size: 12px;
        }
        #poster_date span a {
            color: #a5a4a4;
        }
        #poster_date span {
            color: #a5a4a4;
            font-weight: 100;
        }
        .main_post, .post {
            margin-top: 10px;
        }
        .main_post #post p {
            font-size: 12px;
            line-height: 1.3;
        }
        .image-wrapper .image-wrapper amp-img {
            height: auto;
            width: auto;
            max-width: 100%;
            max-height: 600px;
            min-height: 100px;
        }
        .unknown-size img {
            object-fit: contain; /* or 'fill', or 'cover', etc */
        }
        .commentsHeader {
            margin-top: 15px;
            padding: 10px 0 10px 0;
            background: #f5f5f5;
            text-align: center;
        }
        .commentsHeader a {
            font-size: 18px;
            color: #545452;
        }
        #post_count {
            margin-bottom: 10px;
        }
        .button_more, .button_more:visited {
            display: block;
            font-size: 18px;
            font-weight: 700;
            text-align: center;
            color: #fff;
            background: #24a0ed;
            margin-top: 24px;
            margin-bottom: 10px;
            padding-top: 8px;
            padding-bottom: 8px;
            line-height: 1.8;
            text-decoration: none;
        }
        .button_more span {
            color: white;
            font-weight: 700;
        }
        .padding-top {
            padding-top: 10px;
        }
        .comment {
            margin-left: -10px;
            text-align: left;
            border-top: 1px solid #efefed;
        }
        .comment_body {
            word-wrap: break-word;
            word-break: keep-all;
        }
        .comment_body p {
            font-weight: 400;
            font-size: 13px;
            margin: 2px 0 5px 0;
            color: #222;
        }
        .post {
            border-bottom: 1px solid #efefed;
        }
        .post .title a, .post .title a:visited {
            font-size: 18px;
            white-space: normal;
            overflow: hidden;
            color: #0079d3;
        }
        #post {
            font-size: 12px;
        }
        #post_wrapper {
            margin-left: 30px;
            margin-right: -30px;
        }
        .comment_header span {
            color: #a5a4a4;
            font-size: 13px;
            font-weight: 300;
        }
        .comment_header span a {
            color: #a5a4a4;
            font-size: 13px;
            font-weight: 300;
        }
        .thumbnail amp-img img {
            border-radius: 50%;
        }
        .overflow {
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
        }
        #content_wrapper {
            margin-left: -10px;
        }
    </style>
    @if($thread->media_type == 'youtube')
        <script async custom-element="amp-youtube" src="https://cdn.ampproject.org/v0/amp-youtube-0.1.js"></script>
    @endif
    @if($thread->media_type == 'vimeo')
        <script async custom-element="amp-vimeo" src="https://cdn.ampproject.org/v0/amp-vimeo-0.1.js"></script>
    @endif
    <title>{{$thread->title}}</title>
@endsection


@section('content')
    <nav class="topNav">
        <a href="{{ url('/') }}">
            <div id="nav_header">Lolhow</div>
            <amp-img id="nav_img" src="{{ url('/') }}/images/logo.png" height="48" width="86"></amp-img>
        </a>
    </nav>

    @php
        $user = new \App\User();
        $postername = $user->select('username')->where('id', $thread->poster_id)->first();
    @endphp

    <div class="container">
        <div id="content_wrapper" class="row">
            <div id="sublolhow_name">
                <a href="/p/{{$subLolhow->name}}">/p/{{$subLolhow->name}}</a>
            </div>
            <div id="title">
                <h1><a href="{{ url('/') }}/p/{{$subLolhow->name}}/comments/{{$thread->code}}/{{ str_slug($thread->title) }}">{{$thread->title}}</a></h1>
            </div>
            <div id="poster_date">
                <span><a href="/u/{{$postername->username}}">u/{{$postername->username}}</a> - {{Carbon\Carbon::parse($thread->created_at)->diffForHumans()}}</span>
            </div>
            <div id="post">
                @if($thread->link || $thread->post)
                    <div class="main_post">
                        @if($thread->link)
                            @if($thread->media_type == 'image')
                                <div class="image-wrapper">
                                    <amp-img class="unknown-size" src="{{$thread->link}}" alt="Welcome" width=300 height=200 height="1"></amp-img>
                                </div>
                            @elseif($thread->media_type == 'video')
                                <amp-video controls
                                           width="640"
                                           height="360"
                                           layout="responsive">
                                    <source src="{{$thread->link}}"
                                            type="video/mp4" />
                                    <div fallback>
                                        <p>This browser does not support the video element.</p>
                                    </div>
                                </amp-video>
                            @elseif($thread->media_type == 'youtube')
                                @php preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $thread->link, $matches); @endphp
                                <amp-youtube
                                        data-videoid="{{$matches[0]}}"
                                        layout="responsive"
                                        width="480" height="270"></amp-youtube>
                            @elseif($thread->media_type == 'vimeo')
                                @php $vimeoId = (int) substr(parse_url($thread->link, PHP_URL_PATH), 1); @endphp
                                <amp-vimeo
                                        data-videoid="{{$vimeoId}}"
                                        layout="responsive"
                                        width="500" height="281"></amp-vimeo>
                            @else
                                <a href="{{$thread->link}}">{{$thread->link}}</a>
                            @endif
                        @endif

                        @if($thread->post)
                            <div id="post">
                                {!! $thread->post !!}
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="commentsHeader">
        <div id="post_count">
            <a href="{{ url('/') }}/p/{{$subLolhow->name}}/comments/{{$thread->code}}/{{ str_slug($thread->title) }}">
                {{$thread->reply_count}} {{str_plural('comment', $thread->reply_count)}}
            </a>
        </div>
        <div class="container">
            <div class="row">
                @php
                    $comment = new \App\Post();
                    $comments = $comment->where('thread_id', $thread->id)->select('user_id', 'thread_id', 'upvotes', 'downvotes', 'score', 'comment', 'username', 'posts.created_at')
                    ->leftJoin('users', 'posts.user_id', '=', 'users.id')
                    ->where('parent_id', null)
                    ->take(30)->get();
                @endphp
                @foreach($comments as $comment)
                    <div class="comment">
                        <div class="comment_header overflow">
                            <span class="commentHeader__username"><a href="/u/{{$comment->username}}">u/{{$comment->username}}</a></span>
                            <span class="commentHeader__seperator"> • </span>
                            <span class="commentHeader__timestamp"> {{date('M d Y', $comment->created_at->timestamp)}} </span>
                        </div>
                        <div class="comment_body">
                            <p>{{$comment->comment}}</p>
                        </div>
                    </div>
                @endforeach

                <a href="{{ url('/') }}/p/{{$subLolhow->name}}/comments/{{$thread->code}}/{{ str_slug($thread->title) }}" class="button_more"><span>View more comments</span></a>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            @php
            $thread = new \App\Thread();
            $top_posts = $thread->where('sub_lolhow_id', $subLolhow->id)->orderBy('score', 'desc')->take(5)->get();
            @endphp

            @if($top_posts->count() > 0)
                <div id="title">
                    <h1 class="center padding-top">Top posts in <a href="/p/{{$subLolhow->name}}">/p/{{$subLolhow->name}}</a></h1>
                </div>
                @foreach($top_posts as $thread)
                    <div class="post row">
                        <div class="col-2-sm">
                            <div class="thumbnail">
                                <amp-img src="@if($thread->thumbnail !== null){{$thread->thumbnail}} @elseif($thread->link) {{url('/')}}/images/link_thumb.png @else {{url('/')}}/images/text_thumb.png @endif" alt="{{$thread->title}}" alt="Welcome" width=66 height=66 ></amp-img>
                            </div>
                        </div>
                        <div id="post_wrapper" class="col-9-sm">
                            <div class="title">
                                <a href="/p/{{$subLolhow->name}}/comments/{{$thread->code}}/{{str_slug($thread->title)}}">{{$thread->title}}</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
            <a href="{{ url('/') }}/p/{{$subLolhow->name}}" class="button_more">View more /p/{{$subLolhow->name}} posts</a>
        </div>
    </div>
@endsection
