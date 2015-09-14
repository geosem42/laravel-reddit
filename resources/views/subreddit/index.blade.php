@extends('layouts/default')

<link rel="stylesheet" href="{{ URL::asset('assets/css/style.css') }}">

@section('content')
    <h1>All Subreddits</h1>

    @foreach($subreddit as $sub)
        <div class="row">
            <div class="col-md-12">
                <div class="well well-sm">
                    <div class="row">
                        <div class="col-xs-12 col-md-12 section-box">
                            <h3>
                                <a href="{{ action('SubredditController@show', [$sub->id]) }}">{{ $sub->name }}</a>
                            </h3>
                            <p>{{ $sub->description }}</p>
                            <div class="row rating-desc">
                                <div class="col-md-12">
                                    <small>9,732 subscribers, community for 2 days</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    {!! $subreddit->render() !!}
@stop