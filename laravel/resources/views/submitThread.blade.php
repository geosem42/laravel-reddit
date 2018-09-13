@extends('layouts.app')

@section('title') Plebbit: Create a new post @endsection

@section('stylesheets')
    <link rel="stylesheet" href="{{asset('css/dropzone.css')}}">
    <link rel="stylesheet" href="{{asset('css/easy-autocomplete.min.css')}}">

    <style>
        .container {
            font-family: roboto;
            font-weight: 300;
        }
        .dropzone.dz-clickable {
            cursor: pointer;
        }
        .dropzone {
            border: 2px dashed #0087F7;
            border-radius: 5px;
            background: white;
            min-height: 150px;
            padding: 54px 54px;
        }
    </style>
@endsection

@section('content')
    <div class="container">

    @if(isset($name))
            <h2>Posting in <a href="/p/{{$name}}">/p/{{$name}}</a></h2>
        @else
            <h2>Post something new</h2>
        @endif

        <ul class="nav nav-tabs">
            <li @if(app('request')->input('type') == 'link') class="active"  @elseif(empty(app('request')->input('type'))) class="active" @endif><a data-toggle="tab" href="#link">Link</a></li>
            <li @if(app('request')->input('type') == 'text') class="active" @endif><a data-toggle="tab" href="#text">Text</a></li>
        </ul>

        <div class="tab-content">
            <div id="link" class="tab-pane fade @if(app('request')->input('type') == 'link') in active  @elseif(empty(app('request')->input('type'))) in active @endif">
                <form id="link_form" action="" method="post" class="form-horizontal">
                    {{ csrf_field() }}

                    <input type="hidden" name="type" value="link">

                    <div style="margin-top: 20px;" class="form-group{{ $errors->has('url') ? ' has-error' : '' }}">
                        <div class="container">
                            <h4>Url</h4>
                        </div>
                        <div class="col-md-6">
                            <input type="text" id="url" class="form-control" name="url" placeholder="Url" value="@if (!$errors->has('url')){{old('url')}}@endif" autocomplete="off">
                            @if ($errors->has('url'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('url') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                        <div class="container">
                            <h4>Title <span style="color:red">*</span></h4>
                        </div>
                        <div class="col-md-6">
                            <textarea style="max-width: 100%;" id="title" class="form-control" name="title" placeholder="Title" cols="30" rows="2">@if (!$errors->has('title')){{old('title')}}@endif</textarea>

                            @if ($errors->has('title'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('title') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('subplebbit') ? ' has-error' : '' }}">
                        <div class="container">
                            <h4>Subplebbit <span style="color:red">*</span></h4>
                        </div>
                        <div class="col-md-6">
                            <input autocomplete="off" type="text" id="subplebbit" class="form-control" name="subplebbit" placeholder="Subplebbit" value="@if (!empty(old('subplebbit'))){{old('subplebbit')}}@elseif(isset($name)){{$name}}@endif">
                            @if ($errors->has('subplebbit'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('subplebbit') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                </form>

                <div class="row">
                    <div class="col-md-6">
                        <div id="dropzone">
                            <form id="drop_zone" action="/api/upload/media" class="dropzone" enctype="multipart/form-data">
                                <input type="hidden" name="api_token" value="{{Auth::user()->api_token}}">
                                <div class="dz-message" data-dz-message><span>Drop your media here <br>jpg, png, gif, webm, mp4</span></div>
                            </form>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 20px; margin-bottom: 50px;" class="form-group">
                    <div class="col-md-6">
                        <button style="width: 100px;" id="submit_link" class="btn btn-primary pull-right">Post it</button>
                    </div>
                </div>

            </div>


            <div id="text" class="tab-pane fade @if(app('request')->input('type') == 'text') in active @endif">
                <form action="" method="post" class="form-horizontal">
                    {{ csrf_field() }}

                    <input type="hidden" name="type" value="text">
                    <div style="margin-top: 20px;" class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                        <div class="container">
                            <h4>Title <span style="color:red">*</span></h4>
                        </div>
                        <div class="col-md-6">
                            <textarea style="max-width: 100%;" id="title" class="form-control" name="title" placeholder="Title" cols="30" rows="2">@if (!$errors->has('title')){{old('title')}}@endif</textarea>

                            @if ($errors->has('title'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('title') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('text') ? ' has-error' : '' }}">
                        <div class="container">
                            <h4>Text</h4>
                        </div>
                        <div class="col-md-6">
                            <textarea style="max-width: 100%;" id="text" class="form-control" name="text" placeholder="Text" cols="30" rows="10">@if (!$errors->has('text')){{old('text')}}@endif</textarea>

                            @if ($errors->has('text'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('text') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('subplebbit') ? ' has-error' : '' }}">
                        <div class="container">
                            <h4>Subplebbit <span style="color:red">*</span></h4>
                        </div>
                        <div class="col-md-6">
                            <input autocomplete="off" style="max-width: 100%;" id="subplebbit2" class="form-control" name="subplebbit" placeholder="subplebbit" value="@if (!empty(old('subplebbit'))){{old('subplebbit')}}@elseif(isset($name)){{$name}}@endif">

                            @if ($errors->has('subplebbit'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('subplebbit') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <input style="width: 100px;" type="submit" value="Post it!" class="btn btn-primary pull-right">
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>

@endsection

@section('scripts')
    <script src="{{asset('js/dropzone.js')}}"></script>
    <script src="{{asset('js/jquery.easy-autocomplete.min.js')}}"></script>
    <script>
        var baseurl = '{{url('/')}}';
        var key = '';

        $('#submit_link').click(function() {
            $('#link_form').submit();
        });

        Dropzone.autoDiscover = false;
        $(".dropzone").dropzone({
            maxFiles: 1,
            maxFilesize: 4, // MB
            addRemoveLinks: true,
            removedfile: function(file) {
                $('#url').val('');
                file.previewElement.remove();
                $.get( "api/media/delete/"+key);
            },
            acceptedFiles: ".jpeg,.jpg,.png,.gif,.mp4",
            success : function(file, response){
                key = response.key;
                console.log(key);
                $('#url').val(baseurl + '/media/' + response.link);
            }
        });

        var options = {

            url: function(phrase) {
                return "api/subplebbits/search/"+phrase;
            },

            getValue: function(element) {
                return element.name;
            },

            ajaxSettings: {
                dataType: "json",
                method: "GET",
                data: {
                    dataType: "json"
                }
            },

            preparePostData: function(data) {
                data.phrase = $("#example-ajax-post").val();
                return data;
            },

            requestDelay: 400
        };

        $("#subplebbit").easyAutocomplete(options);
        $("#subplebbit2").easyAutocomplete(options);
        $('div.easy-autocomplete').removeAttr('style');
    </script>
@endsection
