@extends('layouts/default')

@section('scripts')
    <script type="text/javascript" src="{{ asset('assets/js/tinymce/tinymce.min.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            tinymce.init({
                selector : "textarea",
                menubar    : false,
                plugins : ["advlist autolink lists link image charmap print preview anchor", "searchreplace visualblocks code fullscreen", "insertdatetime media table contextmenu paste"],
                toolbar : "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
            });
        });
    </script>
@endsection

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