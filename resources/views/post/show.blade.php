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
            @include('partials/post')

            @include('partials/comments')
        </div>

        <div class="col-md-4">
            @include('partials/post_sidebar')
        </div>
    </div>
@stop