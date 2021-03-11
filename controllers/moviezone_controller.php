<?php
/*-------------------------------------------------------------------------------------------------
@Module: moviezone_controller.php
This server-side modules handles and resolves any requests sent to moviezone_main.php

This module shares both the member and guest functionality as the majority of their functionality is the same.

@Author: Vinh Bui (vinh.bui@scu.edu.au)
@Modified by:  Matt Bowles (m.bowles.16@student.scu.edu.au)
@Date: 09/09/2017
--------------------------------------------------------------------------------------------------*/
require_once('moviezone_config.php');

class GuestMovieZoneController
{
  private $model;
  private $view;

  /**
   * The constructor - creates references to model/view objects.
   * @param $model An instance of the model class.
   * @param $view An instance of the view class.
   */
  public function __construct($model, $view)
  {
    $this->model = $model;
    $this->view = $view;
  }

  /**
   * The destructor - sets the values of the model/view objects to null - essentially destroying them.
   */
  public function __destruct()
  {
    $this->model = null;
    $this->view = null;
  }

  /**
   * Processes a request that is sent to the handler by calling an appropriate function.
   * @param $request The request received by the request handler.
   */
  public function processRequest($request)
  {
    switch ($request) {
      case CMD_FILTER_MOVIES:                                 // Filters a movie by a certain condition.
        $this->handleFilterMovieRequest();
        break;
      case CMD_SHOW_ALL_MOVIES:                               // Display all movies in the database.
        $this->handleShowAllMoviesRequest();
        break;
      case CMD_SHOW_NEW_RELEASES:                             // Display all new releases in the database.
        $this->handleShowAllNewReleasesRequest();
        break;
      case CMD_SHOW_FILTER_ACTOR:                             // Display <select>/<option> elements for all actors.
        $this->handleShowSelectRequest('actor');
        break;
      case CMD_SHOW_FILTER_DIRECTOR:                          // Display <select>/<option> elements for all directors.
        $this->handleShowSelectRequest('director');
        break;
      case CMD_SHOW_FILTER_GENRE;                             // Display <select>/<option> elements for all genres.
        $this->handleShowSelectRequest('genre');
        break;
      case CMD_SHOW_FILTER_CLASSIFICATION:                    // Display <select>/<option> elements for all classifications.
        $this->handleShowSelectRequest('classification');
        break;
      case CMD_LOGIN_MEMBER:
        $this->handleMemberLoginRequest();
        break;
      case CMD_ADD_TO_CART:
        $this->handleAddToCartRequest();
        break;
      case CMD_CREATE_MEMBER:
        $this->handleCreateMemberRequest();
        break;
      case CMD_CHECK_IF_EXISTS:
        $this->handleCheckIfExistsRequest();
        break;
      default:
        print("Request unknown");
    }
  }

  /**
   * Handles any filter movie request that was made to the request handler.
   * This is usually triggered (via js) on update of a <select> element's value.
   */
  private function handleFilterMovieRequest()
  {
    $movies = $this->model->filterMovies();

    if ($movies != null) {
      foreach($movies as $movie){
        $movie_data = $this->model->getMovieData($movie['movie_id']);
        $this->view->showMovies($movie_data);
      }
    } else {
      // There are no movies that match the filter criteria
      print("<h3>There are no movies that match the filter criteria.<br>Please try again.</h3>");
      $error = $this->model->getError();
      if (!empty($error)) {
        $this->view->showError($error);
      }
    }
  }

  /**
   * Displays (prints) all movies in the database.
   */
  private function handleShowAllMoviesRequest()
  {
    $movies = $this->model->selectAllMovies();
    if ($movies != null) {
      $this->view->showMovies($movies);
    } else {
      $error = $this->model->getError();
      if (!empty($error)) {
        $this->view->showError($error);
      }
    }
  }

  /**
   * Shows a <select> element (complete with all options) for a specified field/filter type.
   * Valid fields: director, studio, genre, classification.
   * @param $field The filter type which the <select>/<option> elements will be generated for.
   */
  private function handleShowSelectRequest($field)
  {
    $field = strtolower($field);
    $SELECTABLE_FIELDS = array('director', 'studio', 'genre', 'classification', 'actor');

    if (!in_array($field, $SELECTABLE_FIELDS)) {
      // Invalid filter type selected, display error.
      $error = "Invalid filter type used: please select one of the following:<br>";
      $error .= implode(", ", $SELECTABLE_FIELDS);
      $this->view->showError($error);
    } else {
      // Get all registered id/name values for the field.
      $items = $this->model->selectAllFromMovies($field);
      if ($items != null) {
        $this->view->showSelect($field, $items);
      } else {
        $error = $this->model->getError();
        if (!empty($error)) {
          $this->view->showError($error);
        }
      }
    }
  }

  /**
   * Displays all new releases in the database (in full detail).
   */
  private function handleShowAllNewReleasesRequest()
  {
    $newReleases = $this->model->selectNewReleases();

    if ($newReleases != null) {
      foreach($newReleases as $newRelease){
        $movie_data = $this->model->getMovieData($newRelease['movie_id']);
        $this->view->showMovies($movie_data);
      }
    } else {
      $error = $this->model->getError();
      if (!empty($error)) {
        $this->view->showError($error);
      }
    }
  }

  /**
   * Displays 2 new releases from the system's database.
   */
  public function loadNewReleasesLeftPanel()
  {
    // Get the movie_ids for 2 'new releases'.
    $newReleases = $this->model->selectNewReleases(2);

    if ($newReleases != null) {
      foreach($newReleases as $newRelease){
        // Get data for movie from movie_detail_view, and show the movie using that data.
        $movie_data = $this->model->getMovieData($newRelease['movie_id']);
        $this->view->showMovies($movie_data, false);
      }
    } else {
      $error = $this->model->getError();
      if (!empty($error)) {
        $this->view->showError($error);
      }
    }
  }

  /**
   * Handles a login request for a member account.
   * HTTP POST request must contains values for 'username' and 'password'.
   */
  public function handleMemberLoginRequest()
  {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (!isset($_POST['username']) && !isset($_POST['password'])) {
      $this->view->showError("Must provide both a username and a password in a POST request.");
      return;
    }

    // Attempt login using data.
    if ($this->model->validateMemberLogin($username, $password)) {
      // Successful, create session for member.
      $_SESSION['member'] = $_POST['username'];
      header('Location: moviezone.php');  // Redirect to /moviezone.php
    } else {
      // Not a success, display error.
      $this->view->showError("Login failed. Username or password incorrect.");
    }
  }

  /**
   * Adds a movie to the member's cart.
   * A $_POST variable 'movie_id' is required.
   */
  public function handleAddToCartRequest()
  {
    // Get number of movies in cart
    $size = 1 + intval(sizeof($_SESSION['movies_in_cart']));

    if ($size < 6) {
      // Add movie to cart (if it contains 1 movie)
      array_push($_SESSION['movies_in_cart'], $_POST['movie_id']);
    }

    print $size;  // Prints back to js, which deals with it appropriately.
  }

  /**
   * Obtains the details of the movies within the user's cart and prints them.
   */
  public function showAllMoviesInCart()
  {
    if (isset($_SESSION['movies_in_cart']) && sizeof($_SESSION['movies_in_cart']) > 0) {

      $moviesInCart = $this->model->selectAllMoviesInCart();
      if ($moviesInCart != null) {
        $this->view->printCart($moviesInCart);
      } else {
        $error = $this->model->getError();
        if (!empty($error)) {
          $this->view->showError($error);
        }
      }
    } else {
      $this->view->showError("You must select at least one (1) movie to checkout.");
    }
  }

  /**
   * Checks if a value for a certain field exists in the system's database.
   * Requires 2 post/get requests variables in the form of 'type'/'value'.
   */
  public function handleCheckIfExistsRequest()
  {
    if (isset($_REQUEST['type']) && (isset($_REQUEST['value']))) {
      $type = $_REQUEST['type'];
      $value = $_REQUEST['value'];

      // Print response back to the Ajax call (true/false)
      print $this->model->checkIfExists($type, $value);
    }
  }

  /**
   * Checks whether a user is logged in as a member and/or as an admin.
   * Prints a logged in status message if either are true, also prints amount of movies in cart for member.
   */
  public function checkLoggedIn()
  {
    // Check if user is logged in as member.
    if (isset($_SESSION['member'])) {
      $name = $_SESSION['fullname'];
      print "$name<br>";
      print "logged-in<br>";

      // Print how many movies are in cart.
      if (sizeof($_SESSION['movies_in_cart'] != 0)) {
        print "<span id='movies_in_cart_box'>";
        print sizeof($_SESSION['movies_in_cart']);
        print "</span>";
        print " movies selected<br>";
      }

    }

    // Check if user is logged in as admin.
    if (isset($_SESSION['admin']) && isset($_SESSION['admin_fullname'])) {
      $name = $_SESSION['admin_fullname'];
      print "<span style='color: red'>$name</span><br>";
      print "<span style='color: red'>Admin-mode</span>";
    }
  }

  /**
   * Registers a new account for a member.
   */
  public function handleCreateMemberRequest()
  {
    $memberData = $_POST;
    unset($memberData['request']);    // Remove 'request' from member data.

    // Remove any empty parameters (such as email if not provided, etc.)
    $memberData = array_filter($memberData);

    $this->model->createMember($memberData);

    $error = $this->model->getError();
    if (!empty($error)) {
      $this->view->showError($error);
    } else {
      $this->view->signupSuccess();
    }
  }
}
?>