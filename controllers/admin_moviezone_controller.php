<?php
/*-------------------------------------------------------------------------------------------------
@Module: admin_moviezone_controller.php
This server-side modules handles and resolves any requests sent to moviezone_main.php

@Author: Vinh Bui (vinh.bui@scu.edu.au)
@Modified by:  Matt Bowles (m.bowles.16@student.scu.edu.au)
@Date: 09/09/2017
--------------------------------------------------------------------------------------------------*/
require_once('moviezone_config.php');

class AdminMovieZoneController
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
   * Processes an HTTP request that is sent to the handler by calling an appropriate function.
   * @param $request The request received by the request handler.
   */
  public function processRequest($request)
  {
    switch ($request) {
      case CMD_SHOW_DETAILS_MEMBER:
        $this->handleShowEditDeleteMemberForm();
        break;
      case CMD_SHOW_DETAILS_MOVIE:
        $this->handleShowDetailsMovie();
        break;
      case CMD_DELETE_MEMBER:
        $this->handleDeleteMemberRequest();
        break;
      case CMD_EDIT_MEMBER:
        $this->handleEditMemberRequest();
        break;
      case CMD_SHOW_EDIT_DELETE_MEMBER:
        $this->handleShowEditDeleteMemberRequest();
        break;
      case CMD_SHOW_CREATE_MOVIE:
        $this->handleShowCreateMovieFormRequest();
        break;
      case CMD_SHOW_EDIT_DELETE_MOVIE:
        $this->handleShowEditDeleteMovieFormRequest();
        break;
      case CMD_CREATE_MOVIE:
        $this->handleCreateMovieRequest();
        break;
      case CMD_EDIT_MOVIE:
        $this->handleEditMovieRequest();
        break;
      case CMD_DELETE_MOVIE:
        $this->handleDeleteMovieRequest();
        break;
      case CMD_CHECK_IF_EXISTS:
        $this->handleCheckIfExistsRequest();
        break;
      case CMD_VALIDATE_POSTER:
        $this->validatePoster();
        break;
      case CMD_SHOW_MOVIE_STOCK_REPORT:
        $this->handleShowMovieStockReportRequest();
        break;
      case CMD_LOGIN_ADMIN:
        $this->handleAdminLoginRequest();
        break;
      default:
        $this->view->showError("Request unknown");
        print_r($_REQUEST);
    }
  }

  /**
   * Loads the left panel used for the admin interface.
   * Contains links to: editing/deleting a member, creating a member, editing/deleting a movie, creating a movie, logging out.
   */
  public function loadAdminLeftPanel()
  {
    $this->view->leftAdminPanel();
  }

  /**
   * Verifies whether an admin login attempt was successful.
   * At the moment, a combination of any username with the password 'webdev2' will grant access.
   */
  private function handleAdminLoginRequest()
  {

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Attempt login.
    if ($this->model->validateAdminLogin($username, $password)) {
      // Successful, create session for admin.
      $_SESSION['admin'] = $_POST['username'];

      // Print back to js, which will refresh page.
      print "true";

    } else {
      // Not a success, print back to js.
      print "false";
    }
  }

  /**
   * Checks whether a user is logged in as a member or an admin, and prints a corresponding message.
   */
  public function checkLoggedIn()
  {
    // Check if user is logged in as member.
    if (isset($_SESSION['member'])) {
      $name = $_SESSION['fullname'];
      print "$name<br>";
      print "logged-in<br>";

      if (isset($_SESSION['movies_in_cart'])) {
        print intval($_SESSION['movies_in_cart'])." movies selected<br>";
      }
    }

    // Check if user is logged in as admin.
    if (isset($_SESSION['admin']) && isset($_SESSION['admin_fullname'])) {
      $name = $_SESSION['admin_fullname'];
      print "<span style='color: #ff8800'>$name</span><br>";
      print "<span style='color: #ff8800'>Admin-mode</span>";
    }
  }

  /**
   * Loads a select element that allows the user to choose a member in the database. Upon choosing a member, the edit/delete member form is loaded.
   */
  private function handleShowEditDeleteMemberRequest()
  {
    $members = $this->model->selectAllUsers();
    if ($members != null) {
      $this->view->showSelectMember($members);
    } else {
      $error = $this->model->getError();
      if (!empty($error)) {
        $this->view->showError($error);
      }
    }
  }

  /**
   * Loads the data required to create a movie (e.g. a list of all actors, directors, genres, etc.)
   * and prints it in a form.
   */
  private function handleShowCreateMovieFormRequest()
  {
    $data = $this->model->getCreateMovieData();
    $this->view->createMovieForm($data);
  }

  /**
   * Prints the form that allows an admin to update/delete a member.
   * A valid 'member_id' value must be provided in the HTTP request.
   */
  private function handleShowEditDeleteMemberForm()
  {

    if (!empty($_REQUEST['member_id'])) {
      $member = $_REQUEST['member_id'];
    } else {
      $this->view->showError("Invalid request - must supply 'member_id'");
      return;
    }

    $memberData = $this->model->getMemberInfo($member);
    if ($memberData != null) {
      $this->view->editDeleteMemberForm($memberData);
    } else {
      $error = $this->model->getError();
      if (!empty($error)) {
        $this->view->showError($error);
      }
    }
  }

  /**
   * Registers a movie in the database.
   */
  private function handleCreateMovieRequest()
  {
    $movieData = array();

    foreach($_POST as $key=>$value) {
      $movieData[$key] = $_POST[$key];
    }

    // Remove request from moviedata.
    unset($movieData['request']);

    $this->model->createMovie($movieData);

    $error = $this->model->getError();
    if (!empty($error)) {
      $this->view->showError($error);
    } else {
      $this->view->createMovieSuccess();
    }
  }

  /**
   * Prints a <select> element containing all movies.
   * Once a movie is selected, a form to update its information will be generated.
   */
  private function handleShowEditDeleteMovieFormRequest()
  {
    $movies = $this->model->selectAllMovies();
    if ($movies != null) {
      $this->view->showSelectMovie($movies);
    } else {
      $error = $this->model->getError();
      if (!empty($error)) {
        $this->view->showError($error);
      }
    }
  }

  /**
   * Displays the details of a given movie in a form, ready for editing/deleting.
   * Requires: an HTTP GET request containing a valid 'movie_id'.
   */
  private function handleShowDetailsMovie()
  {
    $movie = $_GET['movie_id'];
    $movieData = $this->model->getMovieInfo($movie);
    if ($movieData != null) {
      $this->view->editDeleteMovieForm($movieData);
    } else {
      $error = $this->model->getError();
      if (!empty($error)) {
        $this->view->showError($error);
      }
    }
  }

  /**
   * Removes all information for a movie from the database.
   * The movie's poster is also deleted.
   */
  private function handleDeleteMovieRequest()
  {
    $movie_id = $_POST['movie_id'];
    $thumbpath = $_POST['thumbpath'];

    if ($movie_id != null && $thumbpath != null) {
      $this->model->deleteMovie($movie_id, $thumbpath);
    } else {
      $this->view->showError("Thumbpath must be provided.");
      return;
    }

    $error = $this->model->getError();
    if (!empty($error)) {
      $this->view->showError($error);
    } else {
      $this->view->deleteMovieSuccess();
    }

  }

  /**
   * Updates information for a given movie.
   */
  public function handleEditMovieRequest()
  {
    $movieData = array();

    foreach($_POST as $key=>$value) {
      $movieData[$key] = $_POST[$key];
    }

    // Remove request from moviedata.
    unset($movieData['request']);


    $this->model->editMovie($movieData);

    if (!empty($error)) {
      $this->view->showError($error);
    } else {
      $this->view->editMovieSuccess();
    }
  }

  /**
   * Removes all information from the database for a specific member.
   * Requires a $_POST variable with a valid value for 'member_id'.
   */
  public function handleDeleteMemberRequest()
  {
    $member_id = $_POST['member_id'];

    if ($member_id != null) {
      $this->model->deleteMember($member_id);       // Delete member from db.
      $this->view->deleteMemberSuccess();           // Show success message.
    } else {
      $error = $this->model->getError();
      if (!empty($error)) {
        $this->view->showError($error);
      }
    }
  }

  /**
   * Updates the database's information for a member.
   */
  public function handleEditMemberRequest()
  {
    $memberData = $_POST;

    // Remove requests from member data array.
    unset($memberData['request_type']);
    unset($memberData['request']);

    $magazine = $memberData['magazine'];

    // Remove any empty parameters (such as email if not provided, etc.)
    $memberData = array_filter($memberData);

    $memberData['magazine'] = $magazine;

    $this->model->editMember($memberData);

    if (!empty($error)) {
      $this->view->showError($error);
    } else {
      $this->view->editMemberSuccess();
    }
  }


  /**
   * Checks if a value for a certain field exists in the system's database.
   * Requires 2 post/get requests variables in the form of 'type'/'value'.
   */
  public function handleCheckIfExistsRequest()
  {
    $type = $_REQUEST['type'];
    $value = $_REQUEST['value'];

    // Print response back to the Ajax call (true/false)
    print $this->model->checkIfExists($type, $value);
  }

  /**
   * Determines whether a poster is able to be uploaded to the server.
   * E.g. Is it the right format? Does it exceed the file size limit? Was there an error uploading?
   *
   * Prints back to JavaScript which deals with it as needed.
   */
  public function validatePoster()
  {
    if (!empty($_FILES['poster'])) {
      $file = $_FILES['poster'];

      $fileName = $file['name'];
      $fileSize = $file['size'];
      $fileError = $file['error'];
      $fileExt = strtolower(end(explode(".", $fileName)));    // e.g. ".png"

      // List of valid image file types accepted by the system.
      $valid_filetypes = array('png', 'jpeg', 'jpg', 'gif');

      // If file uploaded successfully...
      if ($fileError == 0) {

        // Is it the correct filesize?
        if ($fileSize > _MAX_POSTER_FILE_SIZE_) {
          $maxSize = _MAX_POSTER_FILE_SIZE_/1048576;
          print "Movie poster's file size exceeds the limit ($maxSize mb)";
        }

        // Is it a valid image type?
        if (!in_array($fileExt, $valid_filetypes)) {
          print "Invalid file uploaded (only png/jpeg/jpg/gif accepted)";
        }

      } else {
        // Something went wrong.
        print "Something went wrong uploading the movie poster - please try again.";
      }
    } else {
      print "Something went wrong uploading the movie poster - please try again.";
    }
  }

  /**
   * Shows a tabular format of movie stock data.
   * Data shown includes: amount of copies in-stock/available/rented in both DVD and BluRay format for each movie.
   */
  public function handleShowMovieStockReportRequest()
  {
    $stockData = $this->model->getMovieStockData();
    if ($stockData != null) {
      $this->view->showMovieStockReport($stockData);
    } else {
      $error = $this->model->getError();
      if (!empty($error)) {
        $this->view->showError($error);
      }
    }
  }

}
?>