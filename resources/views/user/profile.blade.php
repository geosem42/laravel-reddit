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
    <div class="container">
        <div class="row">
            <div class="col-md-10"><h1>{{ $user->name }}</h1></div>
            <div class="col-md-2">
                <p>
                    <small>
                        Link Karma: {{ $linkKarma }}
                    </small>
                </p>
                <p><small>Comment Karma: </small></p>
            </div>
        </div>

            <div class="col-md-12">

                <div class="tab-pane active" id="my-posts">
                    @foreach($user->posts as $post)
                        @include('partials/post')
                    @endforeach


                </div><!--/tab-pane-->
            </div><!--/tab-content-->

        </div><!--/col-9-->
    </div><!--/row-->

@stop