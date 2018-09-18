@extends('layouts.app')

@section('title') Whoops... @endsection

@php $twitter_title = 'Sorry, that thread is gone'; @endphp
@include('layouts.partials.twitter_cards')

@section('content')

    <div class="container">
        <p>The thread you were looking for was not found.</p>
    </div>

@endsection
