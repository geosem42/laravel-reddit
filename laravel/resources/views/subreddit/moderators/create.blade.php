@extends('layouts/default')

@section('scripts')
    <link rel="stylesheet" href="{{ URL::asset('assets/css/typeahead.css') }}">
    <script src="{{ URL::asset('assets/js/typeahead.bundle.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            var users = new Bloodhound({
                datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
                queryTokenizer: Bloodhound.tokenizers.whitespace,
                prefetch: 'data/users',
                remote: {
                    url: 'data/users/%QUERY',
                    wildcard: '%QUERY'
                }
            });

            $('#remote .typeahead').typeahead(null, {
                name: 'name',
                display: 'name',
                source: users
            });

            $('#remote .typeahead').bind('typeahead:select', function(ev, suggestion) {
                $('.user_id').val(suggestion.id);
            });
        });
    </script>
@endsection

@section('content')
    <h1>Moderators for: {{ $subreddit->name }}</h1>

    @if(Session::has('message'))
        <p class="alert {{ Session::get('success-class', 'alert-success') }}" role="alert">{{ Session::get('message') }}</p>
    @elseif(Session::has('message_info'))
        <p class="alert {{ Session::get('alert-class', 'alert-warning') }}" role="alert">{{ Session::get('message_info') }}</p>
    @endif

    {!! Form::open(['url' => 'subreddit/' . $subreddit->id . '/moderators', 'method' => 'POST']) !!}

    <p>
        <div id="remote">
            <input class="form-control typeahead" type="text" placeholder="Choose a Username" name="user_name">
            <input type="hidden" class="user_id" value="" name="user_id">
        </div>
    </p>

    <p>
        {!! Form::submit('Submit Moderator', ['id' => 'submit', 'class' => 'btn btn-primary']) !!}
    </p>

    {!! Form::close() !!}

    <h1>Moderators List:</h1>
    <div class="row col-md-6">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Subreddit</th>
                <th class="text-center">Action</th>
            </tr>
            </thead>
            @foreach($moderators as $moderator)
            <tr>
                <td>{{ $moderator->id }}</td>
                <td>{{ $moderator->user->name }}</td>
                <td>{{ $moderator->subreddit->name }}</td>
                <td class="text-center">
                    {!! Form::open(['action' => ['ModeratorsController@destroy', $subreddit->id, $moderator->id], 'method' => 'delete']) !!}
                    {!! Form::submit('Delete', ['class' => 'btn btn-xs btn-danger']) !!}

                    {!! Form::close() !!}

                </td>
            </tr>
            @endforeach
        </table>
    </div>



    @if($errors->any())
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif
@stop