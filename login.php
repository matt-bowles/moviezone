<?php
require_once("moviezone_main.php");

// Redirect user to moviezone page if they are already logged in as a member
if (isset($_SESSION['member']) && $_SESSION['member'] == true) {
    header("Location: moviezone.php");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>MovieZone</title>

    <script src="js/ajax.js"></script>

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
            <?php $controller->loadNewReleasesLeftPanel(true) ?>
        </div>

        <div id="rightPanel">
            <h1>Log-in Form</h1>

            <div id="loginStatus"></div>

            <p>Remember you may only rent/purchase five (5) movies via the on-line system</p>

            <form method="post" action="login.php">
                Username:<br>
                <input type="text" name="username"><br>
                Password:<br>
                <input type="password" name="password"><br>
                <input type="submit" value="Login">
            </form>

          <?php
          // Check POST request for username/password
          // (called when attempting to log in)
          if (isset($_POST['username']) && isset($_POST['password'])) {
            $controller->handleMemberLoginRequest();
          }
          ?>

        </div>

    </main>

  <?php $view->footer(); ?>

</div>

</body>
</html>