@extends('layouts/default')

@section('content')
    <h1>Edit Post: {{ $post->id }}</h1>

        @if (!empty($post->link))

                {!! Form::model($post, ['method' => 'PATCH', 'action' => ['PostsController@update', $post->id]]) !!}
                <p>
                    {!! Form::label('title', 'Title:') !!}
                    {!! Form::text('title', null, ['class' => 'form-control', 'id' => 'title']) !!}
                </p>

                <p>
                    {!! Form::label('link', 'Link:') !!}
                    {!! Form::text('link', null, ['class' => 'form-control', 'id' => 'link', 'disabled' => 'disabled']) !!}
                </p>

                <p>
                    {!! Form::submit('Submit Post', ['id' => 'submit', 'class' => 'btn btn-primary']) !!}
                </p>

                {!! Form::close() !!}
        @else

                {!! Form::model($post, ['method' => 'PATCH', 'action' => ['PostsController@update', $post->id]]) !!}
                <p>
                    {!! Form::label('title', 'Title:') !!}
                    {!! Form::text('title', null, ['class' => 'form-control', 'id' => 'title']) !!}
                </p>

                <p>
                    {!! Form::label('text', 'Text:') !!}
                    {!! Form::textarea('text', null, ['class' => 'form-control', 'id' => 'text']) !!}
                </p>

                <p>
                    {!! Form::submit('Submit Post', ['id' => 'submit', 'class' => 'btn btn-primary']) !!}
                </p>

                {!! Form::close() !!}

        @endif
    @if($errors->any())
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif
@stop