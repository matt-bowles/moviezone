<?php
require_once("admin_main.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>MovieZone</title>

  <script src="js/ajax.js"></script>
  <script src="js/admin.js"></script>

  <script src="js/validate.js"></script>

  <!--    Local CSS   -->
  <link href="css/stylesheet.css" rel="stylesheet">

</head>
<body>
<div id="wrapper">
  <header>
    <h1 id="heading">DVD Emporium - Administration</h1>
      <nav>
      </nav>
  </header>

  <main>
    <div id="leftPanel">
      <h2>Admin Menu</h2>
        <ul>
      <?php
      if (!isset($_SESSION['admin'])) {
        print "<a href=\"moviezone.php\"><li>Exit to MovieZone</li></a>";
      } else {
          $view->leftPanelAdmin();
      }
      ?>
       </ul>
    </div>

    <div id="rightPanel">
      <?php $controller->checkLoggedIn()?>
        <h1 id="adminHeading" class="title"></h1> <!--    Contains the heading function  -->
        <div id="mainContent">
        <?php
        // If user isn't logged in as admin, show the admin login form.
        if (!isset($_SESSION['admin']) && !isset($_SESSION['admin_fullname'])) {
            $view->adminLoginForm();
        } else {
          // User is logged in as admin, show welcome message.
          print("<h1 class='title'>Welcome " . $_SESSION['admin_fullname'] . "</h1>");
          print("<br><h3 id='admin_welcomemsg' style='text-align: center'>Please select an action from the menu.</h3>");
        }
        ?>
        </div>
    </div>

  </main>
  <?php $view->footer(); ?>
</div>

</body>
</html>