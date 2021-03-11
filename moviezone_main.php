<?php

require_once('moviezone_config.php');

// Create instances of the model, view, and controller (as required by the MVC design pattern).

// Guest classes are used by default (e.g. if a session is not active).

$model = new GuestMovieZoneModel();
$view = new GuestMovieZoneView();
$controller = new GuestMovieZoneController($model, $view);

// Handle requests.
if (!empty($_REQUEST[CMD_REQUEST])) {
  $request = $_REQUEST[CMD_REQUEST];
  $controller->processRequest($request);
}
?>