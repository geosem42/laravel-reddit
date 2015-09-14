@extends('layouts.default')

@section('content')

    <div class="row">

        <!-- Blog Entries Column -->
        <div class="col-md-8">

            <h1 class="page-header">
                Blog
                <small>Secondary Text</small>
            </h1>


            @foreach($articles as $article)
            <!-- First Blog Post -->
            <h2>
                <a href="{{ action('ArticlesController@show', [$article->id]) }}">{{ $article->title }}</a>
            </h2>
            <p class="lead">
                by <a href="#">{{ $article->user->name }}</a>
            </p>
            <p>
                <span class="glyphicon glyphicon-time"></span> Posted on {{ date('F D, d Y', strtotime($article->published_at)) }}
                <span class="pull-right">
                    @if(Auth::check() && $article->user->id == Auth::user()->id)
                        <a href="{{ action('ArticlesController@edit', [$article->id]) }}">EDIT</a>
                    @endif
                </span>
            </p>
            <hr>
            <img class="img-responsive" src="http://placehold.it/900x300" alt="">
            <hr>
            <p>{!! $article->body !!}</p>
            <a class="btn btn-primary" href="{{ action('ArticlesController@show', [$article->id]) }}">Read More <span class="glyphicon glyphicon-chevron-right"></span></a>

            <hr>
            @endforeach

            <!-- Pager -->
            <ul class="pager">
                <li class="previous">
                    <a href="#">&larr; Older</a>
                </li>
                <li class="next">
                    <a href="#">Newer &rarr;</a>
                </li>
            </ul>

        </div>

        @include('blog/sidebar')

        </div>

    </div>
    <!-- /.row -->
@stop