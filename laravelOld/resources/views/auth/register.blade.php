@extends('layouts/default')

@section('content')

    @if($errors->any())
        <ul>
            @foreach($errors->all() as $error)
                <div class="alert alert-danger list-unstyled" role="alert">
                    <li class="">{{ $error }}</li>
                </div>
            @endforeach
        </ul>
    @endif

    {!! Form::open(['url' => 'auth/register']) !!}
        <fieldset>
            <div id="legend">
                <legend class="">Register</legend>
            </div>
            <div class="control-group">
                <!-- Username -->
                <label class="control-label"  for="name">Username</label>
                <div class="controls">
                    <input type="text" id="name" name="name" placeholder="" value="{{ old('name') }}" class="form-control">
                    <p class="help-block">Username can contain any letters or numbers, without spaces</p>
                </div>
            </div>

            <div class="control-group">
                <!-- E-mail -->
                <label class="control-label" for="email">E-mail</label>
                <div class="controls">
                    <input type="text" id="email" name="email" placeholder="" value="{{ old('email') }}" class="form-control">
                    <p class="help-block">Please provide your E-mail</p>
                </div>
            </div>

            <div class="control-group">
                <!-- Password-->
                <label class="control-label" for="password">Password</label>
                <div class="controls">
                    <input type="password" id="password" name="password" placeholder="" class="form-control">
                    <p class="help-block">Password should be at least 4 characters</p>
                </div>
            </div>

            <div class="control-group">
                <!-- Password -->
                <label class="control-label"  for="password_confirmation">Password (Confirm)</label>
                <div class="controls">
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="" class="form-control">
                    <p class="help-block">Please confirm password</p>
                </div>
            </div>

            <div class="control-group">
                <!-- Button -->
                <div class="controls">
                    <button class="btn btn-success">Register</button>
                </div>
            </div>
        </fieldset>
    {!! Form::close() !!}

@stop