@extends('layouts.app')

@section('title') @if(isset($subPlebbit->name)) p/{{ $subPlebbit->name }} @else What happened? @endif @endsection

@include('layouts.partials.twitter_cards')

@section('stylesheets')
    <link rel="stylesheet" href="{{ asset('css/subplebbit.css') }}">
    @if($subPlebbit)
        <style>
            .header {
                @if($subPlebbit->header)
                    background: linear-gradient(rgba(0,0,0,0.2),rgba(0,0,0,0.2)),url("/images/plebbits/headers/{{$subPlebbit->header}}");
                @endif
                background-position: center;
                @if($subPlebbit->header_type == 'fit')
                   background-size: cover;
                @endif
                width: 100%;
                @if(!$subPlebbit->header)
                background: {{$subPlebbit->header_color}};
                @else
                margin-top: 0;
                @endif
            }
            #stripe {
                background-color: @if($subPlebbit->header_color) {{ $subPlebbit->header_color }} @else grey @endif;
                height: 20px;
                width: 100%;
                position: sticky;
                z-index: 3;
            }
            @if($subPlebbit->header_color)
                #header_name {
                    color: {{$subPlebbit->color}};
                }
                #header_title {
                    color: {{$subPlebbit->color}};
                }
            @endif
            .notsubscribed {
                background-color: #4CAF50 !important;
                color:white;
                border: 1px solid #4CAF50 !important;
                border-top: 1px solid #4CAF50 !important;
            }
            .subscribed {
                background-color: #F44336 !important;
                color:white;
                border: 1px solid #F44336 !important;
                border-top: 1px solid #F44336 !important;
            }
            .subscribe {
                transition: 200ms;
            }
            .subscribe:hover{
                cursor: pointer;
                padding-left: 20px;
                padding-right: 20px;
            }
        </style>
        @if($subPlebbit->custom_css)
            <link rel="stylesheet" href="{{asset('cdn/css/'.$subPlebbit->name.'.css')}}">
        @endif
    @endif
@endsection

@section('content')

    @if($subPlebbit)

        @if($subPlebbit->header)
            <div id="stripe" data-spy="affix"></div>
        @endif
        <div class="header">
              <h1 id="header_name">{{$subPlebbit->name}}</h1>
              <p id="header_title">{{ $subPlebbit->title }}</p>
        </div>

        <div class="container">
            <ul class="tabmenu">
                <li @if(!$sort) class="selected" @endif><a href="/p/{{$subPlebbit->name}}">popular</a></li>
                <li @if($sort == 'new') class="selected" @endif><a href="/p/{{$subPlebbit->name}}/new">new</a></li>
                <li @if($sort == 'top') class="selected" @endif><a href="/p/{{$subPlebbit->name}}/top">top</a></li>
                {{--<li @if($sort == 'shekeld') class="selected" @endif><a href="/p/{{$subPlebbit->name}}/shekeld">shekeld</a></li>--}}
            </ul>

            <ul class="tabmenu rightmenu">
                <li class="selected tabmenu_bottom"><a href="/p/{{$subPlebbit->name}}/submit">Submit a post</a></li>
                @if(Auth::check())
                    @if($subPlebbit->owner_id == Auth::user()->id)
                        <li class="selected tabmenu_bottom" id="edit_plebbit"><a href="/p/{{$subPlebbit->name}}/edit">Edit subplebbit</a></li>
                    @endif
                @endif
                <li @if(!$subscribed) data-subscribed="no" @else data-subscribed="yes" @endif class="@if(!$subscribed) notsubscribed @else subscribed @endif selected subscribe" id="subscribebtn">@if(!$subscribed) Subscribe @else  Unsubsribe @endif</li>
            </ul>


            @php
                $first = true;
                $user = new \App\User();
            @endphp
            <div class="row">

                <div class="col-sm-4 col-sm-push-8">
                    <div class="well search_box">
                        <h4 class="overflow">Search in <a href="/p/{{$subPlebbit->name}}">/p/{{$subPlebbit->name}}</a></h4>
                        <form method="GET" action="/search/{{$subPlebbit->name}}">
                            <div id="custom-search-input">
                                <div class="input-group col-md-12">
                                    <input type="text" name="q" class="search-query form-control" placeholder="Search" />
                                    <span class="input-group-btn">
                                        <button class="btn btn-primary" type="submit">
                                            <span class="fa fa-search"></span>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div style="margin-top: -8px;" class="well">
                        <a style="color: #636b6f;" data-toggle="collapse" href="#about"><h4 class="overflow">About <a data-toggle="collapse" href="#about">/p/{{$subPlebbit->name}}</a></h4></a>
                        <div id="about" class="panel-collapse collapse">
                            <p>{{$readers}} {{str_plural('reader', $readers)}}</p>
                            <p style="margin:0; word-wrap: break-word">{!! nl2br(e(htmlspecialchars($subPlebbit->description))) !!}</p>
                        </div>
                    </div>

                    <div style="margin-top: -8px;" class="well">
                        <a style="color: #636b6f;" data-toggle="collapse" href="#mods"><h4 class="overflow">Moderators for <a data-toggle="collapse" href="#mods">/p/{{$subPlebbit->name}}</a></h4></a>
                        <div id="mods" class="panel-collapse collapse">
                            @if($moderators->count() < 1)
                                <p>There are no mods for this subplebbit yet.</p>
                            @endif
                            @foreach($moderators as $moderator)
                                <a href="/u/{{$moderator->username}}">{{$moderator->username}}</a> <br>
                            @endforeach
                        </div>
                    </div>
                </div>

            @if($threads)
                <div class="col-sm-8 col-sm-pull-4">
                    @foreach($threads as $thread)
                        @php $postername = $user->select('username')->where('id', $thread->poster_id)->first(); @endphp

                        <div class="thread @if($first) first  @php $first = false @endphp @endif">
                            <div style="min-width: 40px;" class="votes col-xs-1">
                                <div style="margin-bottom: -5px; font-size: 20px;" class="row stack">
                                    <i id="{{$thread->id}}_up" data-voted="no" data-vote="up" data-thread="{{$thread->code}}" class="fa fa-sort-asc vote"></i>
                                </div>
                                <div class="row stack">
                                    <span id="{{$thread->id}}_counter" class="stack count">{{$thread->upvotes - $thread->downvotes}}</span>
                                </div>
                                <div style="margin-top: -5px; font-size: 20px;" class="row stack">
                                    <i id="{{$thread->id}}_down" data-voted="no" data-vote="down" data-thread="{{$thread->code}}" class="fa fa-sort-desc stack vote"></i>
                                </div>
                            </div>
                            <div style="min-width: 90px;" class="image col-xs-1">
                                <div class="row">
                                    <a href="@if($thread->link) {{$thread->link}} @else {{url('/')}}/p/{{$subPlebbit->name}}/comments/{{$thread->code}}/{{str_slug($thread->title)}} @endif"><img style="max-height: 76px; max-width: 76px;" src="@if($thread->thumbnail !== null){{$thread->thumbnail}} @elseif($thread->link) {{url('/')}}/images/link_thumb.png @else {{url('/')}}/images/text_thumb.png @endif" alt="{{$thread->title}}"></a>
                                </div>
                            </div>
                            <div class="thread_info">
                                <a style="color: #636b6f;" href="@if($thread->link) {{$thread->link}} @else {{url('/')}}/p/{{$subPlebbit->name}}/comments/{{$thread->code}}/{{str_slug($thread->title)}} @endif"><h3 class="thread_title overflow">{{$thread->title}}</h3></a>
                                <p class="overflow" style="margin-top: -10px;">placed by <a href="/u/{{$postername->username}}">{{$postername->username}}</a> {{Carbon\Carbon::parse($thread->created_at)->diffForHumans()}}</p>
                                <a href="{{url('/')}}/p/{{$subPlebbit->name}}/comments/{{$thread->code}}/{{str_slug($thread->title)}}"><p class="overflow" style="margin-top: -10px;"><strong>{{$thread->reply_count}} {{str_plural('reply', $thread->reply_count)}}</strong></p></a>
                            </div>
                        </div>
                    @endforeach

                    <div  id="page_control">
                        @if(Request::input('page') > 1 && $threads->count() > 0)
                            <a href="?page={{Request::input('page')-1}}">Previous</a> -
                        @endif
                        @if($threads->count() > 24)
                            <a href="@if(!(Request::input('page'))) ?page=2 @else ?page={{Request::input('page')+1}} @endif">Next</a>
                        @endif
                    </div>
                </div>

            @php unset($thread); @endphp
            @endif

                @if($threads == null || $threads && $threads->count() == 0 && !Request::input('page') && !Request::input('after'))
                    <div class="col-sm-8 col-sm-pull-4">
                        <h2 id="looks_like" style="font-weight: lighter; text-align: center">Looks like this subplebbit is still empty.</h2>
                        <h4 style="font-weight: lighter; text-align: center">Go <a href="/p/{{$subPlebbit->name}}/submit">submit</a> something awesome.</h4>
                    </div>
                    @php $no_res = true; @endphp
                @elseif(Request::input('page') || Request::input('after'))
                    @if($threads == null || $threads && $threads->count() == 0 )
                        <div class="col-sm-8 col-sm-pull-4">
                            <div class="welcome" style="font-weight: lighter; margin-top: 50px; text-align: center">
                                <h2 style="font-weight: lighter">No results found for that search criteria</h2>
                                <h4 style="font-weight: lighter; text-align: center">Looks like we ran out of stolen memes</h4>
                                <a href="@if(Request::input('page') == '2') /p/{{$subPlebbit->name}} @elseif(Request::input('after')) ?page={{Request::input('page')-1}}&after={{Request::input('after')}} @else ?page={{Request::input('page')-1}} @endif">Go back a page</a>
                            </div>
                        </div>
                    @endif
                @endif
            </div>


        </div>

        @include('layouts.partials.loginModal')

    @else
        <div class="container">
            <p style="margin-top: 20px;">It looks like this plebbit does not exist. Make it yours!</p>
        </div>
    @endif


@endsection

@section('scripts')
    <script>
        $('#stripe').affix({
            offset: {
                top: $('#nav').height()
            }
        });
    </script>

    @include('layouts.partials.vote')

    @include('layouts.partials.subscriptions')

    <script src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>
    <script>
        $('.count').each(function() {
            _this = $(this);
            if (_this.text() > 1000) {
                _this.text(numeral(_this.text()).format('0.0a'));
            }
        });
        if ($(window).width() > 768) {
            $( ".collapse" ).each(function( el ) {
                $(this).addClass('in');
            });
        }
    </script>

@endsection