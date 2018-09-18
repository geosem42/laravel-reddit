@extends('layouts/default')

@section('content')
    <div class="col-md-8">
        <h1>Homepage</h1>

        @if($errors->any())
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        @foreach($posts as $post)
            @include('partials/post')
        @endforeach
    </div>

    <div class="col-md-4">
        @include('site/sidebar')
    </div>
@stop