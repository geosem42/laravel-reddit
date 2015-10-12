@extends('layouts/default')

@section('scripts')
    <link rel="stylesheet" href="{{ URL::asset('assets/css/jquery.upvote.css') }}">
    <script type="text/javascript" src="{{ URL::asset('assets/js/jquery.upvote.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/jquery.jscroll.min.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('.topic').upvote();

            $('.vote').on('click', function (e) {
                e.preventDefault();
                var $button = $(this);
                var postId = $button.data('post-id');
                var value = $button.data('value');
                $.post('votes', {postId:postId, value:value}, function(data) {
                    if (data.status == 'success')
                    {
                        //
                    }
                }, 'json');
            });

            /*$('.scroll').jscroll({
                autoTrigger: true,
                debug: true,
                nextSelector: '.pagination li.active + li a',
                contentSelector: 'div.scroll',
                callback: function() {
                    $('ul.pagination:visible:first').hide();
                }
            });*/

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
            <div class="scroll">
                @foreach($posts as $post)
                    @include('partials/post')
                @endforeach
                {!! $posts->render() !!}
            </div>
        </div>

        <div class="col-md-4">
            @include('partials/sub_sidebar')
        </div>
    </div>
@stop
