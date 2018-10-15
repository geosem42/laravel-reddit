@extends('layouts.app')

@section('title') Lolhow: Login @endsection

@php $twitter_title = 'Login'; @endphp
@include('layouts.partials.twitter_cards')

@section('content')
<div style="margin-top: 20px;" class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Login</div>
                <div class="panel-body">
			Sorry. Please head over to https://poster.projectoblio.com/login/irt to continue.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
