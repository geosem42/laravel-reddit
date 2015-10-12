@extends('layouts/default')

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