@extends('layouts/default')

@section('scripts')
    <link rel="stylesheet" href="{{ URL::asset('assets/css/typeahead.css') }}">
    <script src="{{ URL::asset('assets/js/typeahead.bundle.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/tinymce/tinymce.min.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            tinymce.init({
                selector : "textarea",
                menubar    : false,
                plugins : ["advlist autolink lists link image charmap print preview anchor", "searchreplace visualblocks code fullscreen", "insertdatetime media table contextmenu paste"],
                toolbar : "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
            });

            var subreddits = new Bloodhound({
                datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
                queryTokenizer: Bloodhound.tokenizers.whitespace,
                prefetch: 'data/subreddits',
                remote: {
                    url: 'data/subreddits/%QUERY',
                    wildcard: '%QUERY'
                }
            });

            $('#remote .typeahead').typeahead(null, {
                name: 'name',
                display: 'name',
                source: subreddits
            });

            $('#remote .typeahead').bind('typeahead:select', function(ev, suggestion) {
                $('.subreddit_id').val(suggestion.id);
            });
        });
    </script>
@endsection

@section('content')
    <h1>Create Post</h1>

    <div class="bs-posts bs-posts-tabs" data-posts-id="togglable-tabs">
        <ul id="myTabs" class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#link" id="link-tab" role="tab" data-toggle="tab" aria-controls="link" aria-expanded="true">Link</a></li>
            <li role="presentation"><a href="#text" role="tab" id="text-tab" data-toggle="tab" aria-controls="text">Text</a></li>
        </ul>
        <div id="myTabContent" class="tab-content" style="margin-top: 15px;">
            <div role="tabpanel" class="tab-pane fade in active" id="link" aria-labelledBy="link-tab">

                <div class="alert alert-warning" role="alert">You are submitting a link. The key to a successful submission is interesting content and a descriptive title.</div>

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
                    <div id="remote">
                        <input class="form-control typeahead" type="text" placeholder="Choose a Subreddit" name="subreddit_name">
                        <input type="hidden" class="subreddit_id" value="" name="subreddit_id">
                    </div>
                </p>

                <p>
                    {!! Form::submit('Submit Post', ['id' => 'submit', 'class' => 'btn btn-primary']) !!}
                </p>

                {!! Form::close() !!}
            </div>
            <div role="tabpanel" class="tab-pane fade" id="text" aria-labelledBy="text-tab">

                <div class="alert alert-warning" role="alert">You are submitting a text-based post. Speak your mind. A title is required, but expanding further in the text field is not. Beginning your title with "vote up if" is violation of intergalactic law.</div>

                {!! Form::open(['url' => 'posts', 'method' => 'POST']) !!}
                <p>
                    {!! Form::label('title', 'Title:') !!}
                    {!! Form::text('title', null, ['class' => 'form-control', 'id' => 'title']) !!}
                </p>

                <p>
                    {!! Form::label('text', 'Text:') !!}
                    {!! Form::textarea('text', null, ['class' => 'form-control', 'id' => 'text']) !!}
                </p>



                <p>
                    <div id="remote">
                        <input class="form-control typeahead" type="text" placeholder="Choose a Subreddit" name="subreddit_name">
                        <input type="hidden" class="subreddit_id" value="" name="subreddit_id">
                    </div>
                </p>

                <p>
                    {!! Form::submit('Submit Post', ['id' => 'submit', 'class' => 'btn btn-primary']) !!}
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