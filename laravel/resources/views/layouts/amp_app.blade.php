<!DOCTYPE html>
<html amp lang="en">
<head>
    <meta charset="utf-8">
    <script async src="https://cdn.ampproject.org/v0.js"></script>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
    <link rel='icon' href="{{url('/')}}/images/logo.png" sizes="256x256" type="image/png" />
    <meta name="keywords" content=" lolhow, lolhow.net, vote, comment, submit " />
    <meta name="description" content="Lolhow • Post your stolen memes here" />
    @yield('meta')

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>

    @if(env('APP_ENV') == 'production')
        <script async custom-element="amp-analytics"
                src="https://cdn.ampproject.org/v0/amp-analytics-0.1.js"></script>

    @endif
    <style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style><noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript>
    @yield('stylesheets')
</head>
<body>
    <amp-analytics type="googleanalytics">
        <script type="application/json">
    {
      "vars": {
        "account": "UA-104410439-1"
      },
      "triggers": {
        "trackPageview": {
          "on": "visible",
          "request": "pageview"
        }
      }
    }
    </script>
    </amp-analytics>
    <div id="app">
        @yield('content')
    </div>
</body>
</html>
