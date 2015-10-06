@extends('layouts/default')

@section('scripts')
    <link rel="stylesheet" href="{{ URL::asset('assets/css/jquery.upvote.css') }}">
    <script src="{{ URL::asset('assets/js/jquery.upvote.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('.topic').upvote();

            $('.vote').on('click', function (e) {
                e.preventDefault();
                var $button = $(this);
                var postId = $button.data('post-id');
                var value = $button.data('value');
                $.post('http://localhost/reddit/public/votes', {postId:postId, value:value}, function(data) {
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
    <div class="row">
        <div class="col-md-8">
            @if($errors->any())
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
            <h1>Subreddit: {{ $subreddit->name }}</h1>

            @foreach($subreddit->posts as $post)
                @include('partials/post')
            @endforeach
        </div>

        <div class="col-md-4">
            @include('partials/sub_sidebar')
        </div>
    </div>
@stop