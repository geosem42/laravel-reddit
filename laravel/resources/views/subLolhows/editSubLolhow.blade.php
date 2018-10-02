@extends('layouts.app')

@section('title') Edit {{ $lolhow->name }} @endsection

<link rel="stylesheet" href="{{asset('css/easy-autocomplete.min.css')}}">
<link rel="stylesheet" href="{{asset('css/bootstrap-tagsinput.css')}}">
@section('stylesheets')
    <style>
        .container {
            font-family: roboto;
            font-weight: 300;
        }
        .bootstrap-tagsinput {
            width: 100%;
            margin-bottom: 10px;
        }
    </style>
@endsection

@section('content')

    <div style="margin-top: 22px;" class="container">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel">
                <div class="panel-heading">
                    <h2>Edit <a href="/p/{{$lolhow->name}}">/p/{{$lolhow->name}}</a></h2>
                </div>

                <div class="panel-body">
                    <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                            <label for="title" class="col-md-2 control-label">Title</label>
                            <div class="col-md-9">
                                <input placeholder="Title" id="title" type="text" class="form-control" name="title" value="@if (!empty(old('title'))) {{ old('title') }} @else{{$lolhow->title}}@endif">

                                @if ($errors->has('title'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                            <label for="description" class="col-md-2 control-label">Social Description</label>
                            <div class="col-md-9">
                                <textarea style="max-width: 100%;" name="description" id="description" placeholder="description" cols="30" rows="5" class="form-control">@if (!empty(old('description'))) {{ old('description') }} @else{{$lolhow->description}}@endif</textarea>
                                @if ($errors->has('description'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('description') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!--div class="form-group{{ $errors->has('social_description') ? ' has-error' : '' }}">
                            <label for="description" class="col-md-2 control-label">Social description</label>
                            <div class="col-md-9">
                                <textarea style="max-width: 100%;" name="social_description" id="social_description" placeholder="Social description" cols="30" rows="5" class="form-control">@if (!empty(old('social_description'))) {{ old('social_description') }} @else{{$lolhow->description_social}}@endif</textarea>
                                @if ($errors->has('social_description'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('social_description') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div -->

                        <div class="form-group{{ $errors->has('header') ? ' has-error' : '' }}">
                            <label for="header" class="col-md-2 control-label">Header</label>
                            <div class="col-md-9">
                                <input placeholder="Header picture" id="header" type="file" class="form-control" name="header" value="{{ old('header') }}">

                                @if ($errors->has('header'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('header') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        @if($lolhow->header)
                            <div class="form-group">
                                <label for="header" class="col-md-2 control-label">Current Header</label>
                                <div class="col-md-10">
                                    <img style="max-height: 100px;" src="/images/lolhows/headers/{{$lolhow->header}}" alt="{{$lolhow->title}}">
                                    <label style="position: absolute; bottom:25px; margin-left: 10px;" class="checkbox-inline">
                                        <input @if($lolhow->header_type == 'fit') checked @endif type="checkbox" name="header_type"> Stretch header to full width
                                    </label>
                                    <label style="position: absolute; bottom:0; margin-left: 10px;" class="checkbox-inline">
                                        <input type="checkbox" name="delete_header"> Delete header image
                                    </label>
                                </div>
                            </div>
                        @endif

                        <div class="form-group{{ $errors->has('icon') ? ' has-error' : '' }}">
                            <label for="icon" class="col-md-2 control-label">Icon</label>
                            <div class="col-md-9">
                                <input placeholder="Header picture" id="icon" type="file" class="form-control" name="icon" value="{{ old('icon') }}">

                                @if ($errors->has('icon'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('icon') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        @if($lolhow->icon)
                            <div class="form-group">
                                <label for="icon" class="col-md-2 control-label">Current Icon</label>
                                <div class="col-md-10">
                                    <img style="max-height: 100px;" src="/images/lolhows/icons/{{$lolhow->icon}}" alt="{{$lolhow->title}}">
                                    <label style="position: absolute; bottom:0; margin-left: 10px;" class="checkbox-inline">
                                        <input type="checkbox" name="delete_icon"> Delete icon
                                    </label>
                                </div>
                            </div>
                        @endif


                        <div class="form-group {{ $errors->has('moderator') ? ' has-error' : '' }}">
                            <label for="moderator" class="col-md-2 control-label">Moderators</label>
                            <div class="col-md-9">
                                <input data-role="tagsinput" id="moderator" type="text" class="form-control" name="moderator" value="{{$mods}}">

                                @if ($errors->has('moderator'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('moderator') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('header_color') ? ' has-error' : '' }}">
                            <label for="header_color" class="col-md-2 control-label">Header color</label>
                            <div class="col-md-9">
                                <input id="header_color" type="text" class="form-control jscolor" name="header_color" value="@if (!empty(old('header_color'))) {{ old('header_color') }} @else{{$lolhow->header_color}}@endif">

                                @if ($errors->has('header_color'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('header_color') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('color') ? ' has-error' : '' }}">
                            <label for="color" class="col-md-2 control-label">Color</label>
                            <div class="col-md-9">
                                <input id="header_color" type="text" class="form-control jscolor" name="color" value="@if (!empty(old('color'))) {{ old('color') }} @else{{$lolhow->color}}@endif">

                                @if ($errors->has('color'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('color') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>


                        <div class="form-group">
                            <div class="col-md-11">
                                <div class="col-md-2 col-md-offset-2 col-xs-2">
                                    <a href="/p/{{$lolhow->name}}/edit/css">Edit css</a>
                                </div>
                                <div class="col-md-8 col-xs-10">
                                    <input type="submit" value="Update lolhow" class="btn btn-primary pull-right">
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ asset('js/jscolor.min.js') }}"></script>
    <script src="{{asset('js/bootstrap-tagsinput.min.js')}}"></script>
@endsection