@if(!Auth::check())
    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Please login or register <span id="loginModalMessage">to upvote</span></h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <h4>Login with Oblio!</h4>
                            <form class="form-horizontal" method="POST" action="{{ route('login') }}">
                                {{ csrf_field() }}
                                <input id="username" type="text" class="form-control" name="username" placeholder="Username" required autofocus>
                                <input style="margin-top: 10px;" id="password" type="password" class="form-control" name="password" placeholder="Password" required>
                                <input type="hidden" name="redirect" value="{{Request::url()}}">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember"> Remember Me
                                    </label>
                                </div>
                                <input type="submit" value="Login" class="btn btn-primary pull-right">
                            </form>
                        </div>

                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <h4>Register</h4>
                            <form class="form-horizontal" method="POST" action="{{ route('register') }}">
                                {{ csrf_field() }}
                                <input id="name" type="text" class="form-control" name="username" placeholder="Username" required autofocus>
                                <input style="margin-top: 10px;" id="email" type="email" class="form-control" name="email" placeholder="Email" required>
                                <input style="margin-top: 10px;" id="password" type="password" class="form-control" name="password" placeholder="Password" required>
                                <input style="margin-top: 10px;" id="password-confirm" type="password" class="form-control" placeholder="Verify password" name="password_confirmation" required>
                                <div style="margin-top:10px; transform:scale(0.89);-webkit-transform:scale(0.89);transform-origin:0 0;-webkit-transform-origin:0 0;">
                                </div>
                                <input type="submit" value="Register" class="btn btn-primary pull-right">
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endif
