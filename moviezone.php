<?php
require_once("moviezone_main.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>MovieZone</title>

    <script src="js/ajax.js"></script>
    <script src="js/moviezone.js"></script>

    <!--    Local CSS   -->
    <link href="css/stylesheet.css" rel="stylesheet">

</head>
<body>
<div id="wrapper">
    <header>
        <h1 id="heading">DVD Emporium - MovieZone</h1>
        <?php $view->topNavbar() ?>
    </header>

    <main>
        <div id="leftPanel">
            <?php $view->leftMZPanel() ?>
        </div>

        <div id="rightPanel">
            <?php $controller->checkLoggedIn()?>
            <h1 id="selectHeading"></h1>    <!--    Contains the heading of each moviezone app page     -->
            <div id="topNav"></div>         <!--    Contains the select element used to filter movies   -->
            <div id="mainContent"></div>    <!--    Contains the movies according to the filters used   -->
        </div>
    </main>

  <?php $view->footer(); ?>
</div>

</body>
</html>