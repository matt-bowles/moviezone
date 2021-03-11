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
            <?php $controller->checkLoggedIn()?>
            <h1 style="text-align: center;">Contact Info for DVD Emporium</h1>
            <div id="contact_info">
            <img src="img/dvd_emporium.jpg" alt="A landscape picture of the DVD Emporium" style="display: block; margin: auto;">
            <p style="text-align: center;"><i>Come on down and view our collection</i></p>

            <div style="text-align: center;">
                <b>Phone:</b> (07) 1234-5678
                    <br>
                <b>Address:</b> Southern Cross Drive<br>Bilinga, Queensland 4225
                    <br>
                <b>Email:</b> <a href="mailto:info@DVD-Emporium.com.au?subject=Direct from DVD Emporium website">
                    info@DVD-Emporium.com.au</a>
            </div>
            <br>
            <h1 style="text-align: center;">DVD Emporium Location (Google Maps)</h1>
            <iframe
                    style="display: block; margin: auto;"
                    width="600"
                    height="450"
                    frameborder="0" style="border:0"
                    src="https://www.google.com/maps/embed/v1/place?key=AIzaSyATGWGM9AqhzRT_6nvvWJcwEYDHctgXa0s&q=Southern+Cross+University+Gold+Coast" allowfullscreen>
            </iframe>
            </div>
        </div>
    </main>

  <?php $view->footer(); ?>
</div>

</body>
</html>