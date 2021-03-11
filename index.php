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
                <h1>Welcome to the Emporium</h1>
            <div id="welcomeText">
                <p><b>DVD Emporium</b> is known for its high quality and customer service. We are
                    dedicated to procuring he finest movies for our customers.
                    DVD was the premier digital video storage medium of the 20th
                    century, and now you too can enjoy the crisp visuals and clean audio of DVD as well as
                    the improved quality of the 21st century's storage medium of choice BluRay.
                </p>
                <p>Our shop, conveniently located near Southern Cross University at the Gold Coast
                    contains literally thousands of new and quality pre-loved DVDs and BluRays available
                    for your viewing pleasure. Rent or purchase its up to you. Consider becoming a member as
                    this will allow you to book on-line and save you the disappointment of arriving only to
                    find the movie you were wishing to view or purchase is currently out of stock.
                </p>
                <p>To become a member please <a href="join.php">join up</a>.</p>
                <p>You can view our extensive movie database in the <a href="moviezone.php">MovieZone</a>.</p>
                <p>As an additional service to our clientele our resident IT guru provides weekly advice
                    on important developments in the IT industry. View the advice from our store IT expert.
                </p>
            </div>
        </div>
    </main>

  <?php $view->footer(); ?>
</div>

</body>
</html>