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
            <div class="col-md-8"><h1>{{ $user->name }}</h1></div>
            <div class="col-md-2">
                    <h2>
                        Link Karma: {{ $linkKarma }}
                    </h2>
            </div>
            <div class="col-md-2">
                <h2>Comment Karma: </h2>
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