@extends('layouts.app')

@section('title')
    Plebbit: Send private message
@endsection


@section('stylesheets')
    <link rel="stylesheet" href="{{ asset('css/subplebbit.css') }}">
    <link rel="stylesheet" href="{{asset('css/easy-autocomplete.min.css')}}">
    <style>
        #header {
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }
    </style>
@endsection

@section('content')

    <div class="container">
        <h2 id="header">Send a private message</h2>

        <form id="link_form" action="{{ route('messages.send') }}" method="post" class="form-horizontal">
            {{ csrf_field() }}
            <div class="form-group{{ $errors->has('subject') ? ' has-error' : '' }}">
                <div class="container">
                    <h4>Subject <span style="color:red">*</span></h4>
                </div>
                <div class="col-md-6">
                    <input type="text" style="max-width: 100%;" id="subject" class="form-control" name="subject" placeholder="Subject" cols="30" rows="2" value="@if (!$errors->has('subject')){{old('subject')}}@endif" />

                    @if ($errors->has('subject'))
                        <span class="help-block">
                            <strong>{{ $errors->first('subject') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="form-group{{ $errors->has('message') ? ' has-error' : '' }}">
                <div class="container">
                    <h4>Message <span style="color:red">*</span></h4>
                </div>
                <div class="col-md-6">
                    <textarea style="max-width: 100%;" id="message" class="form-control" name="message" placeholder="Message" cols="30" rows="2">@if (!$errors->has('message')){{old('message')}}@endif</textarea>

                    @if ($errors->has('message'))
                        <span class="help-block">
                            <strong>{{ $errors->first('message') }}</strong>
                        </span>
                    @endif
                </div>
            </div>


            <div class="form-group{{ $errors->has('to') ? ' has-error' : '' }}">
                <div class="container">
                    <h4>To <span style="color:red">*</span></h4>
                </div>
                <div class="col-md-6">
                    <input autocomplete="off" type="text" id="subplebbit" class="form-control" name="to" placeholder="To" @if (!$errors->has('to') && $username) value="{{$username}}" @endif @if (!$errors->has('to')) value="{{old('to')}}"@endif>
                    @if ($errors->has('to'))
                        <span class="help-block">
                            <strong>{{ $errors->first('to') }}</strong>
                        </span>
                    @endif
                </div>
            </div>


            <div style="margin-top: 20px; margin-bottom: 50px;" class="form-group">
                <div class="col-md-6">
                    <button style="width: 130px;" id="submit_link" class="btn btn-primary pull-right">Send message</button>
                </div>
            </div>

        </form>
    </div>

@endsection

@section('scripts')
    <script src="{{asset('js/jquery.easy-autocomplete.min.js')}}"></script>
    <script>
        var options = {

            url: function(phrase) {
                return "/api/users/search/"+phrase;
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