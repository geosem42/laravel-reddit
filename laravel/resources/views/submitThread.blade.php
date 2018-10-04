@extends('layouts.app')

@section('title') Lolhow: Create a new post @endsection

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
            <li @if(app('request')->input('type') == 'bet') class="active" @endif><a data-toggle="tab" href="#bet">Bet</a></li>
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

                    <div class="form-group{{ $errors->has('sublolhow') ? ' has-error' : '' }}">
                        <div class="container">
                            <h4>Sublolhow <span style="color:red">*</span></h4>
                        </div>
                        <div class="col-md-6">
                            <input autocomplete="off" type="text" id="sublolhow" class="form-control" name="sublolhow" placeholder="Sublolhow" value="@if (!empty(old('sublolhow'))){{old('sublolhow')}}@elseif(isset($name)){{$name}}@endif">
                            @if ($errors->has('sublolhow'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('sublolhow') }}</strong>
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

                    <div class="form-group{{ $errors->has('sublolhow') ? ' has-error' : '' }}">
                        <div class="container">
                            <h4>Sublolhow <span style="color:red">*</span></h4>
                        </div>
                        <div class="col-md-6">
                            <input autocomplete="off" style="max-width: 100%;" id="sublolhow2" class="form-control" name="sublolhow" placeholder="sublolhow" value="@if (!empty(old('sublolhow'))){{old('sublolhow')}}@elseif(isset($name)){{$name}}@endif">

                            @if ($errors->has('sublolhow'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('sublolhow') }}</strong>
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
            <div id="bet" class="tab-pane fade @if(app('request')->input('type') == 'bet') in active @endif">
                <form class="form-horizontal" action="{{ route('bet.store') }}" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}" style="margin-top: 20px;">
                            <div class="container"><h4>Title</h4></div>
                            <div class="col-md-6">
                                <input autocomplete="off" placeholder="Name" id="title" type="text" class="form-control" name="title" value="{{ old('title') }}" required>
                                @if ($errors->has('title'))
                                    <span class="help-block"><strong>{{ $errors->first('title') }}</strong></span>
                                @endif
                            </div>
                        </div> 

                        <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                            <div class="container"><h4>Description</h4></div>
                            <div class="col-md-6">
                                <textarea style="max-width: 100%;" placeholder="Description" id="description" class="form-control" name="description" required>{{ old('description') }}</textarea>
                                @if ($errors->has('description'))
                                    <span class="help-block"><strong>{{ $errors->first('description') }}</strong></span>
                                @endif
                            </div>
                        </div> 

                        <div class="form-group{{ $errors->has('betting_closes') ? ' has-error' : '' }}">                            
                            <div class="col-md-4 col-sm-6">
                                <div class="font-style"><h4>Betting Closes</h4></div>
                                <input autocomplete="off" placeholder="Betting Closes" id="betting_closes" type="text" class="form-control datepicker" name="betting_closes" value="{{ old('betting_closes') }}" required>
                                @if ($errors->has('betting_closes'))
                                    <span class="help-block"><strong>{{ $errors->first('betting_closes') }}</strong></span>
                                @endif
                            </div>
                            
                            <div class="col-md-2 col-sm-6">
                                <div class="font-style"><h4>Timezone : UTC</h4></div>
                                <input type="time" id="timzone_bc" name="timzone_bc" min="00:00" max="24:00" class="form-control" required />
                            </div>
                        </div> 

                        <div class="form-group{{ $errors->has('resolution_paid') ? ' has-error' : '' }}">
                            <div class="col-md-4 col-sm-6">
                                <div class="font-style"><h4>Resolution Paid</h4></div>
                                <input autocomplete="off" placeholder="Resolution Paid" id="resolution_paid" type="text" class="form-control datepicker" name="resolution_paid" value="{{ old('resolution_paid') }}" required>
                                @if ($errors->has('resolution_paid'))
                                    <span class="help-block"><strong>{{ $errors->first('resolution_paid') }}</strong></span>
                                @endif
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <div class="font-style"><h4>Timezone : UTC</h4></div>
                                <input type="time" id="timzone_rp" name="timzone_rp" min="00:00" max="24:00" class="form-control" required />
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('initial_bet') ? ' has-error' : '' }}">
                            <div class="container"><h4>Initial Bet</h4></div>
                            <div class="col-md-4">
                                <input autocomplete="off" placeholder="Initial Bet" id="initial_bet" type="number" min="10" max="1000" class="form-control" name="initial_bet" value="{{ old('initial_bet') }}" required>
                                @if ($errors->has('initial_bet'))
                                    <span class="help-block"><strong>{{ $errors->first('initial_bet') }}</strong></span>
                                @endif
                            </div>
                            <div class="col-md-1">
                                <label><input type="checkbox" name="initial_bet_chk" value="yes">&nbsp;Yes</label>
                            </div>
                            <div class="col-md-1">
                                <label><input type="checkbox" name="initial_bet_chk" value="no">&nbsp;No</label>
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('fee') ? ' has-error' : '' }}">
                            <div class="container"><h4>Fee</h4></div>
                            <div class="col-md-6">
                                <select class="form-control" name="fee" id="fee">
                                    <option value="0">0 %</option>
                                    <option value="1">1 %</option>
                                    <option value="2">2 %</option>
                                    <option value="3">3 %</option>
                                </select>
                                @if ($errors->has('fee'))
                                    <span class="help-block"><strong>{{ $errors->first('fee') }}</strong></span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('options') ? ' has-error' : '' }}">
                        <div class="container"><h4>Bet Options</h4></div>
                            <div class="col-md-6">
                                <input autocomplete="off" placeholder="Name" id="options" type="text" class="form-control" name="options" value="{{ old('options') }}" required>
                                @if ($errors->has('options'))
                                    <span class="help-block"><strong>{{ $errors->first('options') }}</strong></span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('sublolhow') ? ' has-error' : '' }}">
                            <div class="container">
                                <h4>Sublolhow <span style="color:red">*</span></h4>
                            </div>
                            <div class="col-md-6">
                                <input autocomplete="off" type="text" id="sublolhow3" class="form-control" name="sublolhow" placeholder="Sublolhow" value="@if (!empty(old('sublolhow'))){{old('sublolhow')}}@elseif(isset($name)){{$name}}@endif">
                                @if ($errors->has('sublolhow'))
                                    <span class="help-block">
                                            <strong>{{ $errors->first('sublolhow') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6">
                                <input type="submit" value="Create Bet" class="btn btn-primary pull-right">
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
                return "api/sublolhows/search/"+phrase;
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

        $("#sublolhow").easyAutocomplete(options);
        $("#sublolhow2").easyAutocomplete(options);
        $("#sublolhow3").easyAutocomplete(options);
        $('div.easy-autocomplete').removeAttr('style');

        $(document).ready(function(){
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd'
             });
        });
    </script>
@endsection
