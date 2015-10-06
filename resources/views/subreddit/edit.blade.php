@extends('layouts/default')

@section('content')
    <h1>Edit sub: {{ $subreddit->name }}</h1>

    {!! Form::model($subreddit, ['method' => 'PATCH', 'action' => ['SubredditController@update', $subreddit->id]]) !!}

    <p>
        {!! Form::label('name', 'Name:') !!}
        {!! Form::text('name', null, ['class' => 'form-control']) !!}
    </p>

    <p>
        {!! Form::label('description', 'Description:') !!}
        {!! Form::textarea('description', null, ['class' => 'form-control']) !!}
    </p>

    <p>
        {!! Form::submit('Update Subreddit', ['id' => 'submit', 'class' => 'btn btn-primary']) !!}
    </p>

    @if($errors->any())
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif
@stop