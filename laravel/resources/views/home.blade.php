@extends('layouts.app')

@section('title') Lolhow: Post your c O n S p I r A c I e s  here lol @endsection

@include('layouts.partials.twitter_cards')

@section('stylesheets')
    <link rel="stylesheet" href="{{ asset('css/sublolhow.css') }}">
    <style>
        #stripe {
            background-color:#2779A8;
            /*background-color:#2D8CC2;*/
            height: 20px;
            width: 100%;
            position: sticky;
            z-index: 3;
        }
    </style>
@endsection

@section('content')
    <div id="stripe" data-spy="affix"></div>

    <div class="container">
        <ul class="tabmenu">
            <li @if(!$sort || $sort == 'popular') class="selected" @endif><a href="{{url('/')}}">popular</a></li>
            <li @if($sort == 'new') class="selected" @endif><a href="{{url('/s/new')}}">new</a></li>
            <li @if($sort == 'top') class="selected" @endif><a href="{{url('/s/top')}}">top</a></li>
            {{--<li @if($sort == 'shekeld') class="selected" @endif><a href="/s/shekeld">shekeld</a></li>--}}
        </ul>

	<ul class="tabmenu rightmenu">
            <li class="selected tabmenu_bottom"><a href="{{url('/sublolhows/create')}}">Start a sublolhow</a></li>
        </ul>

        <ul class="tabmenu rightmenu">
            <li class="selected tabmenu_bottom"><a href="{{url('/submit')}}">Submit a post</a></li>
        </ul>

        @php
            $first = true;
            $user = new \App\User();
        @endphp
        <div class="row">

            <div class="col-sm-4 col-sm-push-8">
                <div style="padding-bottom: 20px;" class="well search_box">
                    <h4>Search Lolhow</h4>
                    <form method="GET" action="{{url('/search')}}">
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
                    <p style="text-align: right; margin-bottom: -12px; margin-top: 4px;"><a href="{{url('/sublolhows/create')}}">Create your own sublolhow</a></p>
                </div>

            </div>

        @if($threads)
            <div class="col-sm-8 col-sm-pull-4">
                <div class="welcome" style="font-weight: lighter; margin-top: 50px; text-align: center">
                    <h2 style="font-weight: lighter">@if(Auth::check()) <strong class="{{Auth::user()->karma_color}}">{{Auth::user()->username}},</strong> @endif here's stuff from your sublolhows</h2>
                </div>
                <div onclick="window.location.href='{{url('/')}}/g/popular'" style="display: block; margin-left: auto;  margin-right: auto; width:210px;" class="btn btn-primary">Don't like? Check out what's popular</div>
            </div>

           <div class="col-sm-8 col-sm-pull-4">
                @foreach($threads as $thread)
                    @php $postername = $user->select('username')->where('id', $thread->poster_id)->first(); @endphp
                    @php $lolhow = \App\subLolhow::select('id', 'name')->where('id', $thread->sub_lolhow_id)->first(); @endphp

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
                                <a href="@if($thread->link) {{$thread->link}} @else {{url('/')}}/p/{{$lolhow->name}}/comments/{{$thread->code}}/{{str_slug($thread->title)}} @endif"><img style="max-height: 76px; max-width: 76px;" src="@if($thread->thumbnail !== null){{$thread->thumbnail}} @elseif($thread->link) {{url('/')}}/images/link_thumb.png @else {{url('/')}}/images/text_thumb.png @endif" alt="{{$thread->title}}"></a>
                            </div>
                        </div>
                        <div class="thread_info">
                            <a style="color: #636b6f;" href="@if($thread->link) {{$thread->link}} @else {{url('/')}}/p/{{$lolhow->name}}/comments/{{$thread->code}}/{{str_slug($thread->title)}} @endif"><h3 class="thread_title overflow">{{$thread->title}}</h3></a>
                            <p class="overflow" style="margin-top: -10px;">placed by <a href="/u/{{$postername->username}}">{{$postername->username}}</a> {{Carbon\Carbon::parse($thread->created_at)->diffForHumans()}} in
                                <a href="/p/{{$lolhow->name}}">{{$lolhow->name}}</a></p>
                            <a href="{{url('/')}}/p/{{$lolhow->name}}/comments/{{$thread->code}}/{{str_slug($thread->title)}}"><p class="overflow" style="margin-top: -10px;"><strong>{{$thread->reply_count}} {{str_plural('reply', $thread->reply_count)}}</strong></p></a>
                        </div>
                    </div>
                @endforeach
           </div>
                @php unset($thread); // Unset variable so it doesn't get confused with a normal thread @endphp
        @endif

        @if($threads == null || $threads && $threads->count() == 0 && !Request::input('page') && !Request::input('after'))
		window.location.href='{{url('/')}}/g/popular'
            <div class="col-sm-8 col-sm-pull-4">
                <div class="welcome" style="font-weight: lighter; margin-top: 50px; text-align: center">
                    <h2 style="font-weight: lighter">@if(Auth::check()) <strong class="{{Auth::user()->karma_color}}">{{Auth::user()->username}},</strong> @endif this is your homepage</h2>
                    <h4 style="font-weight: lighter; text-align: center">Fill it up by subscribing to some sublolhows</h4>
                    <p style="margin-top: 50px;">Find some communities by searching or...</p>
                </div>
                <div onclick="window.location.href='{{url('/')}}/g/popular'" style="display: block; margin-left: auto;  margin-right: auto; width:210px;" class="btn btn-primary">Check out what's popular</div>
            </div>
            @php $no_res = true; @endphp
        @elseif(Request::input('page') || Request::input('after'))
            @if($threads == null || $threads && $threads->count() == 0 )
                <div class="col-sm-8 col-sm-pull-4">
                    <div class="welcome" style="font-weight: lighter; margin-top: 50px; text-align: center">
                        <h2 style="font-weight: lighter">No results found for that search criteria</h2>
                        <h4 style="font-weight: lighter; text-align: center">Looks like we ran out of stolen memes</h4>
                        <a href="@if(Request::input('page') == '2') / @elseif(Request::input('after')) ?page={{Request::input('page')-1}}&after={{Request::input('after')}} @else ?page={{Request::input('page')-1}} @endif">Go back a page</a>
                    </div>
                </div>
                @php $no_res = true; @endphp
            @endif
        @endif

        @if(!isset($no_res))
            <div  id="page_control">
                @if(Request::input('page') > 1)
                    <a href="?page={{Request::input('page')-1}}">Previous</a> -
                @endif
                @if($threads->count() > 24)
                    <a href="@if(!(Request::input('page'))) ?page=2 @else ?page={{$page+1}} @endif">Next</a>
                @endif
            </div>
        @endif

    </div>

    @include('layouts.partials.loginModal')

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
    <script src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>
    <script>
        $('.count').each(function() {
            _this = $(this);
            if (_this.text() > 1000) {
                _this.text(numeral(_this.text()).format('0.0a'));
            }
        });
    </script>


@endsection
