@extends('layouts.app')

@section('title') Lolhow: Register @endsection

@php $twitter_title = 'Register'; @endphp
@include('layouts.partials.twitter_cards')

@section('content')

<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Document</title>
</head>
<body>
<?php if ($isUserLoggedIn) {
  $user = getUserDetails();
?>
    Hello <?php echo $user['name'] ?>, <br />
    your Email is: <?php echo $user['email'] ?> <br />
    <a href="?logout=true">Logout</a>
<?php } else { ?>
    <a href="?auth=true">Login With Airdrop Form</a>
<?php } ?>
</body>
</html>

@endsection
