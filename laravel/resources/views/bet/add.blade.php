@extends('layouts.app')

@section('title') Create Bet @endsection

@section('stylesheets') 
@endsection

@section('content')
    <div style="margin-top: 22px;" class="container">
        <div class="col-md-8 col-md-offset-2">
            @if(Session::has('success'))
                <div class="row">
                    <div id="message" class="alert alert-success">
                        {{ Session::get('success') }}
                    </div>
                </div>
            @endif
            <div class="panel">
                <div class="panel-heading">
                    <h2>Create bet</h2>
                </div>
                <div class="panel-body">
                    <form class="form-horizontal" action="{{ route('bet.store') }}" method="post">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                            <label for="title" class="col-md-2 control-label">Title</label>
                            <div class="col-md-9">
                                <input autocomplete="off" placeholder="Name" id="title" type="text" class="form-control" name="title" value="{{ old('title') }}" required>
                                @if ($errors->has('title'))
                                    <span class="help-block"><strong>{{ $errors->first('title') }}</strong></span>
                                @endif
                            </div>
                        </div> 

                        <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                            <label for="description" class="col-md-2 control-label">Description</label>
                            <div class="col-md-9">
                                <textarea style="max-width: 100%;" placeholder="Description" id="description" class="form-control" name="description" required>{{ old('description') }}</textarea>
                                @if ($errors->has('description'))
                                    <span class="help-block"><strong>{{ $errors->first('description') }}</strong></span>
                                @endif
                            </div>
                        </div> 

                        <div class="form-group{{ $errors->has('betting_closes') ? ' has-error' : '' }}">
                            <label for="betting_closes" class="col-md-2 control-label">Betting Closes</label>
                            <div class="col-md-9">
                                <input autocomplete="off" placeholder="Betting Closes" id="betting_closes" type="text" class="form-control datepicker" name="betting_closes" value="{{ old('betting_closes') }}" required>
                                @if ($errors->has('betting_closes'))
                                    <span class="help-block"><strong>{{ $errors->first('betting_closes') }}</strong></span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('resolution_paid') ? ' has-error' : '' }}">
                            <label for="resolution_paid" class="col-md-2 control-label">Resolution Paid</label>
                            <div class="col-md-9">
                                <input autocomplete="off" placeholder="Resolution Paid" id="resolution_paid" type="text" class="form-control datepicker" name="resolution_paid" value="{{ old('resolution_paid') }}" required>
                                @if ($errors->has('resolution_paid'))
                                    <span class="help-block"><strong>{{ $errors->first('resolution_paid') }}</strong></span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('initial_bet') ? ' has-error' : '' }}">
                            <label for="initial_bet" class="col-md-2 control-label">Initial Bet</label>
                            <div class="col-md-9">
                                <input autocomplete="off" placeholder="Initial Bet" id="initial_bet" type="number" min="10" max="1000" class="form-control" name="initial_bet" value="{{ old('initial_bet') }}" required>
                                @if ($errors->has('initial_bet'))
                                    <span class="help-block"><strong>{{ $errors->first('initial_bet') }}</strong></span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('fee') ? ' has-error' : '' }}">
                            <label for="fee" class="col-md-2 control-label">Fee</label>
                            <div class="col-md-9">
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

                        <div class="form-group">
                            <div class="col-md-11">
                                <input type="submit" value="Create Bet" class="btn btn-primary pull-right">
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')   
    <script type="text/javascript">
        $(document).ready(function(){
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd'
             });
        });
    </script>
@endsection
