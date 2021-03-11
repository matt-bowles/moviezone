<?php
require_once("moviezone_main.php");

// If the user tries to access the checkout page when they are not logged in as a member, redirect to login page.
if(!isset($_SESSION['member']) && !isset($_SESSION['movies_in_cart'])) {
    header('Location: login.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>MovieZone</title>

  <!--    Local CSS   -->
  <link href="css/stylesheet.css" rel="stylesheet">

</head>
<body>
<div id="wrapper">
  <header>
    <h1 id="heading">MovieZone</h1>
    <?php $view->topNavbar() ?>
  </header>

  <main>
    <div id="leftPanel">
      <h2>New Releases</h2>
      <?php $controller->loadNewReleasesLeftPanel()?>
    </div>

    <div id="rightPanel">
      <?php $controller->checkLoggedIn()?>
      <h1>Checkout</h1>
      <p>This module is currently being built and has not yet been completed<br>You have chosen the following movies to be booked/purchased.
      </p>
      <div id="mainContent"> <!--    Contains the movies according to the filters used   -->
        <?php
        if (isset($_SESSION['movies_in_cart'])) {
          $controller->showAllMoviesInCart();
        }
        ?>
      </div>
    </div>
  </main>

  <?php $view->footer(); ?>
</div>

</body>
</html>