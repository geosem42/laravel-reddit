@extends('layouts/default')

@section('content')
    <h1>Create Post</h1>

    <div class="bs-posts bs-posts-tabs" data-posts-id="togglable-tabs">
        <ul id="myTabs" class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#home" id="home-tab" role="tab" data-toggle="tab" aria-controls="home" aria-expanded="true">Link</a></li>
            <li role="presentation"><a href="#profile" role="tab" id="profile-tab" data-toggle="tab" aria-controls="profile">Text</a></li>
        </ul>
        <div id="myTabContent" class="tab-content">
            <div role="tabpanel" class="tab-pane fade in active" id="home" aria-labelledBy="home-tab">

                {!! Form::open(['url' => 'posts', 'method' => 'POST']) !!}
                <p>
                    {!! Form::label('title', 'Title:') !!}
                    {!! Form::text('title', null, ['class' => 'form-control', 'id' => 'title']) !!}
                </p>

                <p>
                    {!! Form::label('link', 'Link:') !!}
                    {!! Form::text('link', null, ['class' => 'form-control', 'id' => 'link']) !!}
                </p>

                <p>
                    {!! Form::label('subreddit', 'Subreddit:') !!}
                    {!! Form::text('subreddit_id', null, ['class' => 'form-control', 'id' => 'subreddit_id']) !!}
                </p>

                <p>
                    {!! Form::submit('Submit Post', ['id' => 'submit']) !!}
                </p>

                {!! Form::close() !!}
            </div>
            <div role="tabpanel" class="tab-pane fade" id="profile" aria-labelledBy="profile-tab">
                {!! Form::open(['url' => 'posts']) !!}
                <p>
                    {!! Form::label('title', 'Title:') !!}
                    {!! Form::text('title', null, ['class' => 'form-control', 'id' => 'title']) !!}
                </p>

                <p>
                    {!! Form::label('text', 'Text:') !!}
                    {!! Form::textarea('text', null, ['class' => 'form-control', 'id' => 'text']) !!}
                </p>

                <p>
                    {!! Form::label('subreddit', 'Subreddit:') !!}
                    {!! Form::text('subreddit_id', null, ['class' => 'form-control', 'id' => 'subreddit_id']) !!}
                </p>

                <p>
                    {!! Form::submit('Submit Post', ['id' => 'submit']) !!}
                </p>

                {!! Form::close() !!}
            </div>
        </div>
    </div><!-- /tabs -->

    @if($errors->any())
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif
@stop