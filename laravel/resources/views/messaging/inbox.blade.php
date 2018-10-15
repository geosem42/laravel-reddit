@extends('layouts.app')

@section('title')
    Lolhow: Messages inbox
@endsection


@section('stylesheets')
    <link rel="stylesheet" href="{{ asset('css/sublolhow.css') }}">
    <style>
        #stripe {
            background-color:#2779A8;
            height: 20px;
            width: 100%;
            position: sticky;
            z-index: 3;
        }
        .tabmenu li {
            border: 1px solid #5f99cf;
        }
        .pm_padding {
            margin-left: 10px;
        }
        .subject {
            margin-bottom: 0;
            margin-top: 2px;
        }
        .envelope {
            font-size: 20px;
            margin-top: 10px;
            margin-left: 10px;
        }
        .time {
            margin-right: -40px;
        }
        .thing_wrap {
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
        }
        .read {
            background-color: #f9f9f9;
        }
        .pm {
            margin-bottom: 5px;
        }
        #submit {
            background:none!important;
            border:none;
            color: #3097D1;
            padding:0!important;
            font: inherit;
            /*border is optional*/
            cursor: pointer;
        }
        #submit:hover {
            text-decoration: underline;
        }
        @media screen and (max-width: 560px) {
            .rightmenu {
                margin-top: 0;
                position: relative;
                float: right;
                margin-right: -20px;
            }
            .time {
                margin-right: -50px;
            }
        }
        @media screen and (max-width: 992px) {
            .time {
                margin-right: -20px;
            }
        }
        @media screen and (max-width: 610px) {
            .time {
                margin-right: -10px;
            }
        }
        @media screen and (max-width: 510px) {
            .time {
                display: none;
            }
            .time_wrap {
                display: none;
            }
            .thing_wrap {
                width: 91%;
            }
        }
        @media screen and (max-width: 335px) {
            .thing_wrap {
                width: 85%;
            }
        }
    </style>
@endsection

@section('content')
    <div id="stripe" data-spy="affix"></div>

    <div style="margin-bottom: 20px;" class="container">
        <!-- <ul style="float: left; padding: 0; margin-top: 10px; position: absolute">
            <h4>Unread</h4>
        </ul> -->
        <form action="{{ route('messages.mark_read') }}" method="post">
            {{ csrf_field()  }}
            <ul style="margin-top: 10px;" class="tabmenu rightmenu">
                <li class="selected tabmenu_bottom"><button id="submit" type="submit">Mark all as read</button></li>
                <li class="selected tabmenu_bottom"><a href="{{ route('messages.send') }}">Send a pm</a></li>
            </ul>
        </form>
    </div>

    <div class="container">

        @if(count($messages) < 1)
            <p>Your inbox appears to be empty</p>
        @endif

        @foreach($messages as $pm)
            <a style="color: inherit;" href="{{ route('message.view', $pm->code) }}">
                <div class="panel @if($pm->active == 0) read @endif pm">
                    <div class="row">
                        <div style="width: 20px;" class="col-xs-1">
                            <i class="fa @if($pm->active) fa-envelope-o @else fa-envelope-open-o @endif envelope" aria-hidden="true"></i>
                        </div>
                        <div class="col-xs-9 thing_wrap">
                            <h4 class="pm_padding subject overflow">{{$pm->subject}}</h4>
                                <a href="/u/{{$pm->from}}" class="pm_padding">{{$pm->from}}</a>
                        </div>
                        <div class="col-xs-2 time_wrap">
                            <p class="time" style="float: right; margin-top: 10px;">{{Carbon\Carbon::parse($pm->created_at)->diffForHumans()}}</p>
                        </div>
                    </div>
                </div>
            </a>
        @endforeach

        @if($messages->currentPage() > 1)
            <a href="{{$messages->previousPageUrl()}}">Previous</a>
        @endif
        @if($messages->currentPage() > 1 && $messages->currentPage() !== $messages->lastPage())
            -
        @endif
        @if($messages->currentPage() > 0 && $messages->currentPage() !== $messages->lastPage())
            <a href="{{$messages->nextPageUrl()}}">Next</a>
        @endif

    </div>

@endsection

@section('scripts')

    <script>
        $('#stripe').affix({
            offset: {
                top: $('#nav').height()
            }
        });
    </script>

@endsection