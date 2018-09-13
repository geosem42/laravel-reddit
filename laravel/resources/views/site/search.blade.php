@extends('layouts/default')

@section('scripts')
    <link rel="stylesheet" href="{{ URL::asset('assets/css/jquery.upvote.css') }}">
    <script type="text/javascript" src="{{ URL::asset('assets/js/jquery.upvote.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/jquery.jscroll.min.js') }}"></script>
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
            <h1>Searching entire Site</h1>
            <div class="scroll">
                @if(count($posts) < 1)
                    There are no matches
                @endif

                @foreach($posts as $post)
                    @include('partials/post')
                @endforeach

            </div>
        </div>

        <div class="col-md-4">
            @include('site/sidebar')
        </div>
    </div>
@stop