<?php
  use Core\Auth;
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/style.css" rel="stylesheet">
	  <title>Arsalan Thange | Framework</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    
</head>
<body>
	<header>
    
  <nav class="blue accent-2">
    <div class="nav-wrapper">
      <a href="/" class="brand-logo"></a>
      <ul id="nav-mobile" class="right hide-on-med-and-down">

      <?php
       if(!Auth::check()) {
        ?>
          <li><a href="/login">Login</a></li>
          <li><a href="/register">Register</a></li>
        <?php
        }
        ?>
        
        <?php if(Auth::check()) {
        ?>
          <li><a href="/">Index</a></li>
          <li><a href="/logout">Logout</a></li>
        <?php
        }
        ?>
      </ul>
    </div>
  </nav>
	</header>
	<main>


