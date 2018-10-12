@extends('layouts.app')

@section('title') Lolhow: Register @endsection

@php $twitter_title = 'Register'; @endphp
@include('layouts.partials.twitter_cards')

@section('content')
<div style="margin-top: 20px;" class="container">
	
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Register</div>
                <div class="panel-body">
			Sorry. Please go to https://distribution.projectoblio.com to register.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
