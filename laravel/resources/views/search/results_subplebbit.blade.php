@extends('layouts.app')

@section('title') @if(isset($subPlebbit->name)) p/{{ $subPlebbit->name }} @else What happened? @endif @endsection

@php $twitter_title = 'Search in ' . $subPlebbit->name; @endphp
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
            <h2 style="text-align: center; font-weight: 200">Search in <a href="/p/{{$subPlebbit->name}}">/p/{{$subPlebbit->name}}</a></h2>
            <form method="GET" action="/search/{{$subPlebbit->name}}">
                <div id="custom-search-input">
                    <div class="input-group col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
                        <input value="{{ Request::input('q') }}" type="text" name="q" class="search-query form-control" placeholder="Search" />
                        <span class="input-group-btn">
                                <button class="btn btn-primary" type="submit">
                                    <span class="fa fa-search"></span>
                                </button>
                        </span>
                    </div>
                </div>
            </form>

            @php
                $first = true;
                $user = new \App\User();
            @endphp
            <div class="row">
                @if($threads)
                    @foreach($threads as $thread)
                        @php $postername = $user->select('username')->where('id', $thread->poster_id)->first(); @endphp
                        <div style="margin-top:0; padding-bottom: 10px; margin-bottom: 10px; @if($first) margin-top: 20px; @php $first = false @endphp @endif" class="panel">
                            <div class="thread">
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
                        </div>
                    @endforeach

                    <div id="page_control">
                        @if($page > 1 && $threads->count() > 0)
                        <a href="?page={{Request::input('page')-1}}">Previous</a> -
                        @endif
                        @if($threads->count() > 24)
                            <a href="@if(!(Request::input('page'))) ?page=2 @else ?page={{$page+1}} @endif">Next</a>
                        @endif
                    </div>

                    @php unset($thread); @endphp
                @endif

                @if($threads == null || $threads && $threads->count() == 0 && !Request::input('page') && !Request::input('after'))
                    <h2 id="looks_like" style="font-weight: lighter; text-align: center">No results found</h2>
                    @php $no_res = true; @endphp
                @elseif(Request::input('page') || Request::input('after'))
                    @if($threads == null || $threads && $threads->count() == 0 )
                        <div class="welcome" style="font-weight: lighter; margin-top: 50px; text-align: center">
                            <h2 style="font-weight: lighter">No results found for that search criteria</h2>
                            <h4 style="font-weight: lighter; text-align: center">Looks like we ran out of stolen memes</h4>
                            <a href="@if(Request::input('page') == '2') /p/{{$subPlebbit->name}} @elseif(Request::input('after')) ?page={{Request::input('page')-1}}&after={{Request::input('after')}} @else ?page={{Request::input('page')-1}} @endif">Go back a page</a>
                        </div>
                    @endif
                @endif
            </div>


        </div>

        @include('layouts.partials.loginModal')

    @else
        <div class="container">
            <p>It looks like this plebbit does not exist. Make it yours!</p>
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
    </script>

@endsection