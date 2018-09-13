@extends('layouts/default')

@section('content')
    <h1>My Subreddits</h1>

    <div class="container">
        <div class="row col-md-12">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th class="text-center">Action</th>
                </tr>
                </thead>

                @if(Session::has('message'))
                    <p class="alert {{ Session::get('success-class', 'alert-success') }}" role="alert">{{ Session::get('message') }}</p>
                @elseif(Session::has('message_info'))
                    <p class="alert {{ Session::get('alert-class', 'alert-warning') }}" role="alert">{{ Session::get('message_info') }}</p>
                @elseif(Session::has('message_danger'))
                    <p class="alert {{ Session::get('alert-class', 'alert-danger') }}" role="alert">{{ Session::get('message_danger') }}</p>
                @endif

                @foreach($subreddit as $sub)
                <tr>
                    <td>{{ $sub->id }}</td>
                    <td>{{ $sub->name }}</td>
                    <td class="text-center">
                        <a class='btn btn-info btn-xs' href="{{ action('SubredditController@edit', [$sub->id]) }}"><span class="glyphicon glyphicon-edit"></span> Edit</a>
                        <a class="btn btn-warning btn-xs" href="{{ action('ModeratorsController@create', [$sub->id]) }}" ><span class="glyphicon glyphicon-user"></span> Moderators</a>
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
@stop