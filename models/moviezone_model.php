<?php
/*-------------------------------------------------------------------------------------------------
@Module: moviezone_model.php
This server-side module provides all required functionality i.e. to select, update, delete movies

This module shares both the member and guest functionality as the majority of their functionality is the same.

@Author: Vinh Bui (vinh.bui@scu.edu.au)
@Modified by: Matt Bowles (m.bowles.16@student.scu.edu.au)
@Date: 09/09/2017
--------------------------------------------------------------------------------------------------*/

require_once('moviezone_config.php'); // Stores connection details.

class GuestMovieZoneModel {
  private $error;       // Holds any error that occurred.
  private $dbAdapter;   // Used for database access.

  /**
   * The constructor -- creates an instance of the database adapter, providing the connection string/username/password.
   * This informaiton should be stored within the config file.
   */
  public function __construct()
  {
    $this->dbAdapter = new GuestDBAdapter(DB_CONNECTION_STRING, DB_USER, DB_PASS);
  }

  /**
   * Closes the connection to the system's database.
   */
  public function __destruct()
  {
    $this->dbAdapter->dbClose();
  }

  /**
   * Filters all movies by a condition (specified via a GET request) and returns the result.
   * Current valid filter types: actor_name, director, genre_name, classification, movie_id.
   * @return Movies that meet the filter condition
   */
  public function filterMovies()
  {
    $filterType = '';  // Initalise filter type

    // Filter by actor
    if (!empty($_REQUEST['actor_id'])) {
      $filterType = 'actor_id';
      $param = $_REQUEST['actor_id'];
  }

    // Filter by director
    else if (isset($_REQUEST['director_id'])) {
      $filterType = 'director_id';
      $param = $_REQUEST['director_id'];
    }

    // Filter by genre
    else if (isset($_REQUEST['genre_id'])) {
      $filterType = 'genre_id';
      $param = $_REQUEST['genre_id'];
    }

    // Filter by classification
    else if (isset($_REQUEST['classification'])) {
      $filterType = 'classification';
      $param = $_REQUEST['classification'];
    }

    else {
      $this->error = "Something went wrong.";
      return;
    }

    $this->dbAdapter->dbOpen();
    $result = $this->dbAdapter->filterMovies($filterType, $param);
    $this->dbAdapter->dbClose();
    $this->error = $this->dbAdapter->lastError();

    return $result;
  }

  /**
   * @param $movie_id The 'movie_id' value of a movie, which general data will be returned for.
   * @return The data for a movie whose movie_id value corresponds to the parameter passed.
   */
  public function getMovieData($movie_id)
  {
    $this->dbAdapter->dbOpen();
    $result = $this->dbAdapter->getMovieData($movie_id);
    $this->dbAdapter->dbClose();
    $this->error = $this->dbAdapter->lastError();

    return $result;
  }

  /**
   * @return The appropriate information (as contained in movie_detail_view) for all movies in the database.
   */
  public function selectAllMovies()
  {
    $this->dbAdapter->dbOpen();
    $result = $this->dbAdapter->selectAllMovies();
    $this->dbAdapter->dbClose();
    $this->error = $this->dbAdapter->lastError();

    return $result;
  }

  /**
   * @return The appropriate information (as contained in movie_detail_view) for all 'new releases' in the database.
   */
  public function selectNewReleases($n=_MAX_NEW_RELEASES_)
  {
    $this->dbAdapter->dbOpen();
    $result = $this->dbAdapter->selectNewReleases($n);
    $this->dbAdapter->dbClose();
    $this->error = $this->dbAdapter->lastError();

    return $result;
  }

  /**
   * @return The name of each actor registered in the database.
   */
  public function selectAllFromMovies($field)
  {
    $this->dbAdapter->dbOpen();
    $result = $this->dbAdapter->selectAllFromMovies($field);
    $this->dbAdapter->dbClose();
    $this->error = $this->dbAdapter->lastError();

    return $result;
  }

  /**
   * @return The name of each director registered in the database.
   */
  public function selectAllDirectors()
  {
    $this->dbAdapter->dbOpen();
    $result = $this->dbAdapter->selectAllDirectors();
    $this->dbAdapter->dbClose();
    $this->error = $this->dbAdapter->lastError();

    return $result;
  }

  /**
   * @return A list of every unique genre recorded in the system's database.
   */
  public function selectAllGenres()
  {
    $this->dbAdapter->dbOpen();
    $result = $this->dbAdapter->selectAllGenres();
    $this->dbAdapter->dbClose();
    $this->error = $this->dbAdapter->lastError();

    return $result;
  }

  /**
   * @return A list of every unique classification recorded in the system's database.
   */
  public function selectAllClassifications()
  {
    $this->dbAdapter->dbOpen();
    $result = $this->dbAdapter->selectAllClassifications();
    $this->dbAdapter->dbClose();
    $this->error = $this->dbAdapter->lastError();

    return $result;
  }

  /**
   * @return A (possible) error that may have occurred when operating the database adapter.
   */
  public function getError()
  {
		return $this->error;
	}


  /** Login functions **/

  /**
   * @param $username A username (string), NOT a member id.
   * @param $password A password that may be associated with the username.
   * @return bool Whether the login attempt was successful.
   */
	public function validateMemberLogin($username, $password)
  {
    // Open connection to database.
    $this->dbAdapter->dbOpen();

    // Get user info (contains hashed/salted password, and the full name)
    $userInfo = $this->dbAdapter->getPassword($username);

    // Connection to database no longer needed, terminate connection and get (possible) error.
    $this->dbAdapter->dbClose();
    $this->error = $this->dbAdapter->lastError();

    // Get correct password (if it exists).
    $correctPassword = $userInfo['password'];

    // $correctPassword will be null if an account for the username doesn't exist.
    if ($correctPassword != null && $password === $correctPassword) {
      // Success
      $_SESSION['fullname'] = $userInfo[1];
      $_SESSION['movies_in_cart'] = array();
      return true;
    } else {
      // Not a successh
      return false;
    }
  }

  /**
   * @return Movies that have been added to the member's cart.
   */
  public function selectAllMoviesInCart()
  {
    $this->dbAdapter->dbOpen();
    $result = $this->dbAdapter->selectAllMoviesInCart();
    $this->dbAdapter->dbClose();
    $this->error = $this->dbAdapter->lastError();

    return $result;
  }

  /**
   * Checks whether a value for a valid unique field type (both provided by a POST request) is already used or not.
   * @return bool True if the value for the field is NOT being used, false otherwise.
   */
  public function checkIfExists($type, $value)
  {
    $this->dbAdapter->dbOpen();
    $result = $this->dbAdapter->checkIfExists($type, $value);
    $this->dbAdapter->dbClose();
    $this->error = $this->dbAdapter->lastError();

    return $result;
  }

  /**
   * Registers a new member account.
   * @param $memberData An array containing all required information to register a new member account.
   */
  public function createMember($memberData){
    $this->dbAdapter->dbOpen();
    $this->dbAdapter->createMember($memberData);
    $this->dbAdapter->dbClose();
    $this->error = $this->dbAdapter->lastError();
  }

}
?>