<?php
// Initalise the session so that session variables can be accessed.
session_start();

if (!empty($_REQUEST['account_type'])) {

  $account = strtolower($_REQUEST['account_type']);

  switch($account) {
    case 'member':
      print("member");
      $_SESSION['member'] = null;
      $_SESSION['fullname'] = null;
      header("Location: index.php");        // Redirect to home page page.
      break;
    case 'admin':
      print("admin");
      $_SESSION['admin'] = null;
      $_SESSION['admin_fullname'] = null;
      header("Location: admin.php");        // Redirect to moviezone app page.
    break;
    default:
      header("Location: index.php");
  }
} else {
  // Invalid request, send to home page.
  header("Location: index.php");
}
