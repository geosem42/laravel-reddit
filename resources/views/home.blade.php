@extends('layouts/default')

@section('content')
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
@stop