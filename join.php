<?php
require_once("moviezone_main.php");
?>

<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>MovieZone</title>

    <script src="js/ajax.js"></script>
    <script src="js/validate.js"></script>

    <script>
      // Load tool tips on page load.
      window.addEventListener('load', function() {
        loadToolTips();
      });
    </script>

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
          <?php $controller->loadNewReleasesLeftPanel(true)?>
        </div>

        <div id="rightPanel">
            <div id="mainContent">
            <?php $view->signupForm() ?>
            </div>
        </div>
    </main>

  <?php $view->footer(); ?>
</div>

</body>
</html>