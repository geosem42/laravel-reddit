<nav id="nav" class="navbar navbar-default navbar-static-top">
    <div class="container">
        <div class="navbar-header">

            <!-- Collapsed Hamburger -->
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            @if(Auth::check())
                @php
                    $alerts = new \App\Alert();
                    $alerts = $alerts->getAlertsByUser(Auth::user()->id);
                @endphp
                <span id="alerts_mobile" class="dropdown" style="float: right">
                    <a style="color: #777; margin:0; padding:10px; background: none;" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                        <span style="position: absolute; left: -10px; top: 15px; font-size: 20px;" class="fa fa-bell"></span>
                        @if(count($alerts) > 0)<span style="position:absolute; top:10px; z-index: 2; background: orangered; right: -3px;" class="badge">{{count($alerts)}}</span>@endif
                    </a>
                    <ul style="margin-top: 30px; width: 300px; left: -225px;" class="dropdown-menu" role="menu">
                        @php $first = true; @endphp
                        @foreach($alerts as $alert)
                            <li>
                                @if(!$first)
                                    <hr style="margin: 1px; padding:0">
                                @endif
                                @php $first = false; @endphp
                                @if($alert['type'] == 'mention')
                                    <a style="white-space: normal; text-align: left" href="/alerts/{{$alert['code']}}">
                                        <span><strong>{{$alert['user_display_name']}}</strong> replied <strong>{{substr($alert['comment'], 0, 43)}}</strong> on {{substr($alert['thread_title'], 0, 20)}}</span>
                                    </a>
                                @else
                                    <a href="{{ route('message.view', $alert['code']) }}">
                                        <span>New private message from <strong>{{$alert['user_display_name']}}</strong></span>
                                    </a>
                                @endif
                            </li>
                        @endforeach
                        @if(count($alerts) < 1)
                            <li>
                                <a>
                                    No alerts for now
                                </a>
                            </li>
                        @endif
                    </ul>
                </span>
            @endif

            <!-- Branding Image -->
            <a class="navbar-brand" href="{{ url('/') }}">
                <div style="height: inherit; display: inline-block; font-family: Raleway; font-weight: 500;">
                    {{ config('app.name', 'Lolhow') }}
                    @if(isset($subLolhow) && $subLolhow->icon)
                        <img style="height: 48px; margin-top: -11px;" src="/images/lolhows/icons/{{$subLolhow->icon}}" alt="lolhow">
                    @else
                        <img style="height: inherit; margin-top: -14px;" src="{{url('/images/gifs/smile_transperent.gif')}}" alt="lolhow">
                    @endif
                </div>
            </a>
        </div>

        <div class="collapse navbar-collapse" id="app-navbar-collapse">

            @if(Auth::check())
                @php
                    $subscriptions = new \App\Subscription();
                    $subscribed = $subscriptions->subscriptions(Auth::user()->id);
                @endphp
                <ul class="nav navbar-nav navbar">
                    <li class="dropdown">
                        <a style="margin-top: 0px;" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            my sublolhows <span class="caret"></span>
                        </a>
                        <ul style="font-size: 12px; margin:0; padding:0;" class="dropdown-menu" role="menu">
                            <li class="subscriptions">
                                @if($subscribed->count() < 1)
                                    <span style="padding: 10px;">No subscriptions yet</span>
                                @else
                                    @foreach($subscribed as $sub)
                                        <a class="sub" href="/p/{{$sub->name}}">{{$sub->name}}</a>
                                    @endforeach
                                @endif
                            </li>
                        </ul>
                    </li>
                </ul>
            @endif

            <!-- Right Side Of Navbar -->
            <ul class="nav navbar-nav navbar-right">
                <!-- Authentication Links -->
                @if (Auth::guest())
                    <li><a href="{{url('login/irt') }}">Login with Obliooooo!</a></li>
             <!--<li><a href="{{-- route('register') --}}">Register</a></li>-->
             <li> <a href="{{url('externalsignup')}}">Register with Oblio!</a></li>
                <!-- <li> <a href="{{config('services.oblio.distribution_url').'externalsignup'}}">Register with Oblio!</a></li> -->
                @else
                    <li id="alerts_desktop" class="dropdown">
                        <a style="margin:0; padding:10px; background: none;" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            <span style="position: absolute; left: -10px; top: 15px; font-size: 22px;" class="fa fa-bell"></span>
                            @if(count($alerts) > 0)<span style="position:absolute; top:10px; z-index: 2; background: orangered; right: -3px;" class="badge">{{count($alerts)}}</span>@endif
                        </a>
                        <ul style="margin-top: 30px; width: 300px;" class="dropdown-menu" role="menu">
                            @php $first = true; @endphp
                            @foreach($alerts as $alert)
                                <li>
                                    @if(!$first)
                                    <hr style="margin: 1px; padding:0">
                                    @endif
                                    @php $first = false; @endphp
                                    @if($alert['type'] == 'mention')
                                        <a style="white-space: normal; text-align: left" href="/alerts/{{$alert['code']}}">
                                            <span><strong>{{$alert['user_display_name']}}</strong> replied <strong>{{substr($alert['comment'], 0, 43)}}</strong> on {{substr($alert['thread_title'], 0, 20)}}</span>
                                        </a>
                                    @else
                                        <a href="{{ route('message.view', $alert['code']) }}">
                                            <span>New private message from <strong>{{$alert['user_display_name']}}</strong></span>
                                        </a>
                                    @endif
                                </li>
                            @endforeach
                            @if(count($alerts) < 1)
                                <li>
                                    <a>
                                        No alerts for now
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>


                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            <span class="{{ Auth::user()->karma_color }}">{{ Auth::user()->username }}</span> <span class="caret"></span>
                        </a>

                        <ul class="dropdown-menu" role="menu">
                            <li>
                                <a href="/u/{{Auth::user()->username}}">My profile</a>
                            </li>
                            <li>
                                <a href="{{ route('messages.inbox') }}">Private messages</a>
                            </li>
                            <li>
                                <a href="{{ route('sublolhow.create') }}">Create sublolhow</a>
                            </li>
                            <li>
                                <a href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                    Logout
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </li>
                        </ul>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>
