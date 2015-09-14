@extends('layouts/default')

@section('content')
    <h1>Create Subreddit</h1>

    {!! Form::open(['url' => 'subreddit']) !!}

    <p>
        {!! Form::label('name', 'Name:') !!}
        {!! Form::text('name', null, ['class' => 'form-control']) !!}
    </p>

    <p>
        {!! Form::label('description', 'Description:') !!}
        {!! Form::textarea('description', null, ['class' => 'form-control']) !!}
    </p>

    <p>
        {!! Form::submit('Create Subreddit', ['id' => 'submit']) !!}
    </p>

    @if($errors->any())
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif
@stop