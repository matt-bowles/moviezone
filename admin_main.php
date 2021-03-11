<?php

require_once('moviezone_config.php');

// Create instances of the model, view, and controller (as required by the MVC design pattern).
$model = new AdminMovieZoneModel();
$view = new AdminMovieZoneView();
$controller = new AdminMovieZoneController($model, $view);


// Handle requests.
if (!empty($_REQUEST[CMD_REQUEST])) {
  $request = $_REQUEST[CMD_REQUEST];
  $controller->processRequest($request);
}
?>