@extends('layouts.app')

@section('title')
    Lolhow: {{$messages[0]['subject']}}
@endsection


@section('stylesheets')
    <link rel="stylesheet" href="{{ asset('css/sublolhow.css') }}">
    <link rel="stylesheet" href="{{ asset('css/messages.css') }}">
@endsection

@section('content')
    <div id="stripe" data-spy="affix"></div>


    <div class="container">

        <h2 class="text_wrap" style="margin-top: 20px; margin-bottom: 20px;">{{$messages[0]['subject']}}</h2>

        <div id="msg_container" class="col-md-12">
            @foreach($messages as $pm)
                @if($user->id == $pm->user_id)
                    <div class="msg_wrapper">
                        <div style="margin-top: 5px;" class="msg_send">
                            <div style="text-align: right" class="msg_container">
                                <p>{{$pm->from}} ({{Carbon\Carbon::parse($pm->created_at)->diffForHumans()}})</p>
                                <p style="margin-top: 10px;">{!! nl2br(e($pm->message)) !!}</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="msg_wrapper">
                        <div style="margin-top: 5px;" class="msg_received">
                            <div class="msg_container">
                                <p>{{$pm->from}} ({{Carbon\Carbon::parse($pm->created_at)->diffForHumans()}})</p>
                                <p style="margin-top: 10px;">{!! nl2br(e($pm->message)) !!}</p>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <div class="reply_box col-md-6 col-sm-8">
            <form action="" method="post">
                {{ csrf_field() }}
                <div class="form-group{{ $errors->has('reply') ? ' has-error' : '' }}">
                    <textarea style="margin-top: 10px;" class="form-control" placeholder="Reply" name="reply" cols="30" rows="5"></textarea>
                    @if ($errors->has('reply'))
                        <span class="help-block">
                            <strong>{{ $errors->first('reply') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group">
                    @if($messages->currentPage() > 1)
                        <a href="{{$messages->previousPageUrl()}}">Previous</a>
                    @endif
                    @if($messages->currentPage() > 1 && $messages->currentPage() !== $messages->lastPage())
                        -
                    @endif
                    @if($messages->currentPage() > 0 && $messages->currentPage() !== $messages->lastPage())
                        <a href="{{$messages->nextPageUrl()}}">Next</a>
                    @endif
                    <input style="margin-top: -5px; margin-bottom: 20px;" type="submit" class="btn btn-primary pull-right" value="Send reply">
                </div>
            </form>
        </div>

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