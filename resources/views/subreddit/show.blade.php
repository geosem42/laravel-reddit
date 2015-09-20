@extends('layouts/default')
@section('scripts')
    <script src="{{ URL::asset('assets/js/jquery.upvote.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('.topic').upvote();

            $('.vote').on('click', function (e) {
                e.preventDefault();
                var $button = $(this);
                var postId = $button.data('post-id');
                var value = $button.data('value');
                $.post('/votes', {postId:postId, value:value}, function(data) {
                    if (data.status == 'success')
                    {
                        // Do something if you want..
                    }
                }, 'json');
            });
        });
    </script>
@endsection

@section('content')
    <link rel="stylesheet" href="{{ URL::asset('assets/css/jquery.upvote.css') }}">

    <h1>Subreddit: {{ $subreddit->name }}</h1>

    @foreach($subreddit->posts as $post)
        <div class="row">
            <div class="span8">
                <div class="row">
                    <div class="col-md-12">
                        <h4><strong><a href="#"></a></strong></h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-1">
                        <div class="upvote topic" data-post="{{ $post->id }}">
                            <a class="upvote vote {{ $post->votes && $post->votes->contains('user_id', Auth::id()) ? ($post->votes->where('user_id', Auth::id())->first()->value > 0 ? 'upvote-on' : null) : null}}" data-value="1" data-post-id="{{ $post->id }}"></a>
                            <!-- Notice how we set the sum of the votes for this post here -->
                            <span class="count">{{ $post->votes->sum('value') }}</span>
                            <a class="downvote vote {{ $post->votes && $post->votes->contains('user_id', Auth::id()) ? ($post->votes->where('user_id', Auth::id())->first()->value < 0 ? 'downvote-on' : null) : null}}" data-value="-1" data-post-id="{{ $post->id }}"></a>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <a href="#" class="thumbnail">
                            <img src="http://placehold.it/70x70" alt="">
                        </a>
                    </div>
                    <div class="col-md-10">
                        <p>
                            <a href="#">{{ $post->title }}</a>
                        </p>
                        <p style="color: darkgrey; font-size: 12px;">
                            <i class="glyphicon glyphicon-user" style="padding-right: 5px;"></i>submitted by <a href="#">{{ $post->user->name }}</a>
                             <i class="glyphicon glyphicon-calendar" style="padding-left: 15px;"></i> {{ $post->created_at->diffForHumans() }}
                             <i class="glyphicon glyphicon-comment" style="padding-left: 15px;"></i> <a href="#">3 Comments</a>
                             <i class="glyphicon glyphicon-tags" style="padding-left: 15px;"></i> Tags : <a href="#"><span class="label label-info">Snipp</span></a>
                            <a href="#"><span class="label label-info">Bootstrap</span></a>
                            <a href="#"><span class="label label-info">UI</span></a>
                            <a href="#"><span class="label label-info">growth</span></a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endforeach


@stop