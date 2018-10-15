@extends('layouts.app')

@section('title') Lolhow: Post your c O n S p I r A c I e s  here lol @endsection

@section('stylesheets')
    <link rel="stylesheet" href="{{ asset('css/thread.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sublolhow.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ladda-themeless.min.css') }}">
    <style>
        #stripe {
            background-color:#2779A8;
            /*background-color:#2D8CC2;*/
            height: 20px;
            width: 100%;
            margin-top: -22px;
            position: sticky;
            z-index: 3;
        }
    </style>
@endsection

@section('content')
    <div id="stripe" data-spy="affix"></div>

    <div class="container">

        <div style="margin-top: 20px;" class="panel comment" id="post_panel_{{$parent->id}}">
            <div class="panel-body">
                <div class="row">
                    <div style="width: 40px; margin-top: -5px;" class="votes col-xs-2 col-sm-1">
                        <div style="margin-left: 20px;" class="wrap">
                            <div style="margin-bottom: -15px; font-size: 20px;" class="row stack">
                                <a style="color: inherit;" href="javascript:votepost('{{$parent->id}}', 'up');"><i id="{{$parent->id}}_up_post" data-voted="no" data-vote="up" data-post="{{$parent->id}}" class="fa fa-sort-asc"></i></a>
                             </div>
                             <div class="row stack">
                                <span id="{{$parent->id}}_counter_post" class="stack count">{{$parent->score}}</span>
                             </div>
                            <div style="margin-top: -15px; font-size: 20px;" class="row stack">
                                <a style="color: inherit;" href="javascript:votepost('{{$parent->id}}', 'down');"><i id="{{$parent->id}}_down_post" data-voted="no" data-vote="down" data-thread="{{$parent->thread_id}}" class="fa fa-sort-desc stack"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-10 col-sm11">
                        <span><a href="/u/'+res.post.user_display_name+'">{{$parent->user_display_name}}</a> {{Carbon\Carbon::parse($parent->created_at)->diffForHumans()}}  (your comment)</span>
                        <p>{{ nl2br($parent->comment) }}</p>
                        <div class="linkwrapper"><a style="color: grey;" href="javascript:reply('{{$parent->id}}');">Reply</a></div>
                        <div id="comment_box_app_{{$parent->id}}"></div>
                    </div>
                </div>
            </div>
        </div>

        <div style="margin-top: -15px; margin-left: 20px; background: #fff7dd" class="panel comment" id="post_panel_{{$reply->id}}">
            <div class="panel-body">
                <div class="row">
                    <div style="width: 40px; margin-top: -5px;" class="votes col-xs-2 col-sm-1">
                        <div style="margin-left: 20px;" class="wrap">
                            <div style="margin-bottom: -15px; font-size: 20px;" class="row stack">
                                <a style="color: inherit;" href="javascript:votepost('{{$reply->id}}', 'up');"><i id="{{$reply->id}}_up_post" data-voted="no" data-vote="up" data-post="{{$reply->id}}" class="fa fa-sort-asc"></i></a>
                            </div>
                            <div class="row stack">
                                <span id="{{$reply->id}}_counter_post" class="stack count">{{$reply->score}}</span>
                            </div>
                            <div style="margin-top: -15px; font-size: 20px;" class="row stack">
                                <a style="color: inherit;" href="javascript:votepost('{{$reply->id}}', 'down');"><i id="{{$reply->id}}_down_post" data-voted="no" data-vote="down" data-thread="{{$reply->thread_id}}" class="fa fa-sort-desc stack"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-10 col-sm11">
                        <span><a href="/u/'+res.post.user_display_name+'">{{$reply->user_display_name}}</a> {{Carbon\Carbon::parse($reply->created_at)->diffForHumans()}} </span>
                        <p>{{ nl2br($reply->comment) }}</p>
                        <div class="linkwrapper"><a style="color: grey;" href="javascript:reply('{{$reply->id}}');">Reply</a></div>
                        <div id="comment_box_app_{{$reply->id}}"></div>
                    </div>
                </div>
            </div>
        </div>

        @if($user_reply)
            <div style="margin-top: -15px; margin-left: 40px;" class="panel comment" id="post_panel_{{$user_reply->id}}">
                <div class="panel-body">
                    <div class="row">
                        <div style="width: 40px; margin-top: -5px;" class="votes col-xs-2 col-sm-1">
                            <div style="margin-left: 20px;" class="wrap">
                                <div style="margin-bottom: -15px; font-size: 20px;" class="row stack">
                                    <a style="color: inherit;" href="javascript:votepost('{{$user_reply->id}}', 'up');"><i id="{{$user_reply->id}}_up_post" data-voted="no" data-vote="up" data-post="{{$user_reply->id}}" class="fa fa-sort-asc"></i></a>
                                </div>
                                <div class="row stack">
                                    <span id="{{$reply->id}}_counter_post" class="stack count">{{$user_reply->score}}</span>
                                </div>
                                <div style="margin-top: -15px; font-size: 20px;" class="row stack">
                                    <a style="color: inherit;" href="javascript:votepost('{{$user_reply->id}}', 'down');"><i id="{{$user_reply->id}}_down_post" data-voted="no" data-vote="down" data-thread="{{$user_reply->thread_id}}" class="fa fa-sort-desc stack"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-10 col-sm11">
                            <span><a href="/u/'+res.post.user_display_name+'">{{$user_reply->user_display_name}}</a> {{Carbon\Carbon::parse($user_reply->created_at)->diffForHumans()}} </span>
                            <p>{{ nl2br($user_reply->comment) }}</p>
                            <div class="linkwrapper"><a style="color: grey;" href="javascript:reply('{{$user_reply->id}}');">Reply</a></div>
                            <div id="comment_box_app_{{$user_reply->id}}"></div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>
@endsection


@section('scripts')
    @include('layouts.partials.vote')
    <script src="{{ asset('js/spin.min.js') }}"></script>
    <script src="{{ asset('js/ladda.min.js') }}"></script>
    <script src="{{ asset('js/moment.js') }}"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>
    <script>
        var thread = {{$thread->id}};

        $('.count').each(function() {
            _this = $(this);
            if (_this.text() > 1000) {
                _this.text(numeral(_this.text()).format('0.0a'));
            }
        });
        @foreach($voted as $vote)
            @if($vote->vote == 1)
                $('#{{$vote->post_id}}_up_post').css('color', '#4CAF50').attr('data-voted', 'yes');
            @else
                $('#{{$vote->post_id}}_down_post').css('color', '#F44336').attr('data-voted', 'yes');
            @endif
        @endforeach

        function reply(id) {
            @if(Auth::check())
            _this = $('#comment_box_app_' + id);

            if ($('#comment_box_' + id).length > 0) {
                return; // Commentbox already collapsed
            }
            _this.append(
                '         <div class="commentbox" id="comment_box_'+id+'">' +
                '                <div style="width: 300px;" class="panel-body">' +
                '                        <textarea placeholder="comment" class="form-control" id="reply_text_'+id+'" cols="30" rows="3"></textarea>' +
                '                        <span style="color: red;" id="comment_box_alert_'+id+'"></span>' +
                '                        <a id="post_reply_btn_'+id+'" href="javascript:submit_reply('+id+')" style="margin-top: 5px;" class="btn btn-primary submitpostbtn pull-right ladda-button xd" data-style="slide-up">Post comment</a>' +
                '                        <a href="javascript:cancel_reply('+id+');" style="margin-top: 5px; margin-right: 5px;" class="btn btn-primary submitpostbtn pull-right ladda-button" data-style="slide-up">Cancel</a>' +
                '                </div>' +
                '            </div>');
            @else
            $('#loginModal').modal('show');
            $('#loginModalMessage').text('to reply');
            @endif
        }

        function cancel_reply(id) {
            _this = $('#comment_box_' + id).remove();
        }

        function submit_reply(id) {
            button = document.getElementById('post_reply_btn_'+id);
            var l = Ladda.create(button);
            l.start();

            comment = $('#reply_text_' + id).val();
            data = {'thread': thread, 'comment': comment, 'parent': id, 'api_token': '{{Auth::user()->api_token}}'};
            $.post( '/api/comments/add', data, function(res) {
                if (res.warning) {
                    $('#comment_box_alert_' + id).empty().append('<span>'+ res.warning +'</span>');
                    l.stop();
                } else {
                    to_append = $('#post_panel_' + id);
                    created = moment(res.post.created_at).utcOffset('+0400').format('YYYY-MM-DD HH:mm');
                    created = moment(created);
                    ago = created.fromNow();
                    to_append.append('' +
                        '                <div id="post_panel_' + res.post.id + '" style="margin-left: 20px; width:95%; min-width: 400px;" class="col-xs-12">' +
                        '                    <div style="width: 40px; margin-top: -5px;" class="votes col-xs-2 col-sm-1">' +
                        '                        <div style="margin-left: 20px;" class="wrap">' +
                        '                            <div style="margin-bottom: -15px; font-size: 20px;" class="row stack">' +
                        '                                <a style="color: inherit;" href="javascript:votepost(' + res.post.id + ', `up`);"><i id="' + res.post.id + '_up_post" data-voted="no" data-vote="up" data-post="' + res.post.id + '" class="fa fa-sort-asc"></i></a>' +
                        '                            </div>' +
                        '                            <div class="row stack">' +
                        '                                <span id="' + res.post.id + '_counter_post" class="stack">' + res.post.score + '</span>' +
                        '                            </div>' +
                        '                            <div style="margin-top: -15px; font-size: 20px;" class="row stack">' +
                        '                                <a style="color: inherit;" href="javascript:votepost(' + res.post.id + ', `down`);"><i id="' + res.post.id + '_down_post" data-voted="no" data-vote="down" data-thread="' + res.post.id + '" class="fa fa-sort-desc stack"></i></a>' +
                        '                            </div>' +
                        '                        </div>' +
                        '                    </div>' +
                        '                    <div class="col-xs-10 col-sm11">' +
                        '                        <span><a href="/u/' + res.post.user_display_name + '">' + res.post.user_display_name + '</a> ' + ago + '</span>' +
                        '                        <p>' + res.post.comment.replace(/(?:\r\n|\r|\n)/g, '<br />') + '</p>' +
                        '                        <div style="margin-bottom:3px;" class="linkwrapper"><a style="color: grey;" href="javascript:reply(' + res.post.id + ');">Reply</a></div>' +
                        '                        <div id="comment_box_app_' + res.post.id + '"></div>' +
                        '                    </div>' +
                        '                </div>'
                    );
                    l.stop();
                    $('#comment_box_' + id).remove();
                }
            });
        }
    </script>
@endsection