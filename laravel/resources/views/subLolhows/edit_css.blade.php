@extends('layouts.app')

@section('title') Edit css of {{ $lolhow->name }} @endsection

@section('stylesheets')
    <style>
        #nav {
            display: none;
        }
        body {
            background-color: rgb(60, 65, 68);
        }
        .ace_editor {
            position: relative!important;
            border-style: none;
            margin: auto;
            max-width: 100%;
            width: 1300px;
            min-height: 800px;
            margin-top: 20px;
        }
        .alert {
            margin-top: 0 !important;
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <p style="color: white; margin-top: 10px;">Allowed hosts for external resources: i.imgur.com, imgur.com, lolhow.net <br>You may also not use import, charset, and namespace</p>
            </div>
            <div class="col-xs-10">
                <h1 style="color: white;">Editing css of {{$lolhow->name}}</h1>
            </div>
            <div class="col-xs-2">
                <form id="save_css" method="post" action="">
                    {{csrf_field()}}
                    <textarea style="display: none" name="custom_css" id="custom_css" cols="30" rows="10"></textarea>
                    <input id="click_me" style="margin-top: 25px;" type="submit" class="btn btn-primary pull-right" value="Save css">
                </form>
            </div>
        </div>

        <pre id="editor" class="ace_editor ace-tomorrow-night-eighties ace_dark">{{$lolhow->custom_css}}</pre>
    </div>

@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.1.3/ace.js"></script>
    <script>
        var editor = ace.edit("editor");
        editor.setTheme("ace/theme/tomorrow_night_eighties");
        editor.session.setMode("ace/mode/javascript");
        editor.renderer.setScrollMargin(10, 10);
        editor.setOptions({
            autoScrollEditorIntoView: true
        });
        editor.session.setMode('ace/mode/css');

        $("#click_me").click(function(e){
            e.preventDefault();
            $('#custom_css').val(editor.getValue());
            $('#save_css').submit();
        });
    </script>
@endsection