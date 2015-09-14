<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>Reddit</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="{{ URL::asset('assets/css/bootstrap.min.css') }}">
    <script src="{{ URL::asset('assets/js/jquery.min.js') }}"></script>

    <!-- Custom styles for this template -->
    <link rel="stylesheet" href="{{ URL::asset('assets/css/sticky-footer-navbar.css') }}">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="{{ URL::asset('assets/js/html5shiv.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/respond.min.js') }}"></script>
    <![endif]-->
</head>

<body>

<!-- Fixed navbar -->
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">Reddit</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li class="{{ Request::is('/') ? 'active' : '' }}"><a href="{{ URL::action('HomeController@index') }}">Home</a></li>
                <li class="{{ Request::is('articles') ? 'active' : '' }}"><a href="{{ URL::action('ArticlesController@index') }}">Articles</a></li>
                <li class="{{ Request::is('subreddit') ? 'active' : '' }}"><a href="{{ URL::action('SubredditController@index') }}">Subreddits</a></li>
                <li class="{{ Request::is('subreddit/create') ? 'active' : '' }}"><a href="{{ URL::action('SubredditController@create') }}">Create Sub</a></li>
                <li class="{{ Request::is('posts/create') ? 'active' : '' }}"><a href="{{ URL::action('PostsController@create') }}">Create Post</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                @if(Auth::check())
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ Auth::getUser()->name }} <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="#">Edit Profile</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="{{ url('auth/logout') }}">Logout</a></li>
                        </ul>
                    </li>
                    @else
                        <li class="{{ Request::is('auth/register') ? 'active' : '' }}"><a href="{{ url('auth/register') }}">Register</a></li>
                        <li class="{{ Request::is('auth/login') ? 'active' : '' }}"><a href="{{ url('auth/login') }}">Login</a></li>
                @endif

            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>

<!-- Begin page content -->
<div class="container">
    @yield('content')
</div>

<footer class="footer">
    <div class="container">
        <p class="text-muted">Place sticky footer content here.</p>
    </div>
</footer>


<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->

<script src="{{ URL::asset('assets/js/bootstrap.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/jquery-ui.min.js') }}"></script>
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->

</body>
</html>