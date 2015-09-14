@extends('layouts/default')

@section('content')

    <script type="text/javascript" src="{{ asset('assets/js/tinymce/tinymce.min.js') }}"></script>
    <script type="text/javascript">
        tinymce.init({
            selector : "textarea",
            plugins : ["advlist autolink lists link image charmap print preview anchor", "searchreplace visualblocks code fullscreen", "insertdatetime media table contextmenu paste jbimages"],
            toolbar : "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image jbimages",
        });
    </script>

    <h1>Edit Article: {{ $article->title }}</h1>

    <hr>

    {!! Form::model($article, ['method' => 'PATCH', 'action' => ['ArticlesController@update', $article->id]]) !!}
    <p>
        {!! Form::label('title', 'Title:') !!}
        {!! Form::text('title', null, ['class' => 'form-control']) !!}
    </p>

    <div class="editor">
        <p>
            {!! Form::label('body', 'Body:') !!}
            {!! Form::textarea('body', null, ['class' => 'form-control', 'id' => 'myEditor']) !!}
        </p>
    </div>

    <p>
        {!! Form::label('published_at', 'Date:') !!}
        {!! Form::input('date', 'published_at', date('Y-m-d'), ['class' => 'form-control']) !!}
    </p>

    <p>
        {!! Form::label('tag_list', 'Tags:') !!}
        {!! Form::select('tag_list[]', $tags, null, ['class' => 'form-control', 'multiple']) !!}
    </p>

    <p>
        {!! Form::submit('Submit Article', ['id' => 'submit']) !!}
    </p>
    {!! Form::close() !!}

    @if($errors->any())
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

@stop