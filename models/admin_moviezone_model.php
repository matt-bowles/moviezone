<?php
/*-------------------------------------------------------------------------------------------------
@Module: admin_moviezone_model.php
This server-side module provides all required functionality i.e. to select, update, delete movies/users

@Author: Vinh Bui (vinh.bui@scu.edu.au)
@Modified by: Matt Bowles (m.bowles.16@student.scu.edu.au)
@Date: 09/09/2017
--------------------------------------------------------------------------------------------------*/

require_once('moviezone_config.php'); // Stores connection details.

class AdminMoviezoneModel {
  private $error;       // Holds any error that occurred.
  private $dbAdapter;   // Used for database access.

  /**
   * The constructor -- creates an instance of the database adapter, providing the connection string/username/password.
   * This informaiton should be stored within the config file.
   */
  public function __construct()
  {
    $this->dbAdapter = new AdminDBAdapter(DB_CONNECTION_STRING, DB_USER, DB_PASS);
  }

  /**
   * Closes the connection to the system's database.
   */
  public function __destruct()
  {
    $this->dbAdapter->dbClose();
  }

  /**
   * @return The title, movie_id, and year values for every movie in the database.
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
   * @return A (possible) error that may have occurred when operating the database adapter.
   */
  public function getError()
  {
		return $this->error;
	}

  /**
   * Accepts a combination of any username with a password of 'webdev2'.
   * @param $username A username (string).
   * @param $password A password that may be associated with the username (if it exists).
   * @return bool Whether or not the admin login attempt was successful.
   */
  public function validateAdminLogin($username, $password)
  {
    // At the moment, admin page can be accessed if the submitted password is 'webdev2'.
    if ($password == 'webdev2') {
      $_SESSION['admin_fullname'] = $username;
      return true;
    } else {
      return false;
    }
  }

  /**
   * @return The username and member_id for all member accounts in the database.
   */
  public function selectAllUsers()
  {
    $this->dbAdapter->dbOpen();
    $result = $this->dbAdapter->selectAllUsers();
    $this->dbAdapter->dbClose();
    $this->error = $this->dbAdapter->lastError();

    return $result;
  }

  /**
   * @return ALl information contained within the 'member' table for a specific member_id.
   */
  public function getMemberInfo($member)
  {
    $this->dbAdapter->dbOpen();
    $result = $this->dbAdapter->getMemberInfo($member);
    $this->dbAdapter->dbClose();
    $this->error = $this->dbAdapter->lastError();

    return $result;
  }

  /**
   * @return ALl information contained within the 'movie_detail_view' view for a specific movie_id.
   */
  public function getMovieInfo($movie_id)
  {
    $this->dbAdapter->dbOpen();
    $result = $this->dbAdapter->getMovieInfo($movie_id);
    $this->dbAdapter->dbClose();
    $this->error = $this->dbAdapter->lastError();

    return $result;
  }

  /**
   * Updates information for a member account in the database.
   * @param $memberData An array containing all member information to be updated. Must include member_id.
   */
  public function editMember($memberData)
  {
    $this->dbAdapter->dbOpen();
    $result = $this->dbAdapter->editMember($memberData);
    $this->dbAdapter->dbClose();
    $this->error = $this->dbAdapter->lastError();

    return $result;
  }

  /**
   * Deletes all information for a particular member from the system's database.
   * @param $member_id A valid member_id, whose information will be deleted.
   */
  public function deleteMember($member_id)
  {
    $this->dbAdapter->dbOpen();
    $result = $this->dbAdapter->deleteMember($member_id);
    $this->dbAdapter->dbClose();
    $this->error = $this->dbAdapter->lastError();
  }

  /**
   * Registers a movie in the database, and saves the movie's poster to storage.
   * @param $movieData An array containing all required information to create a movie.
   */
  public function createMovie($movieData)
  {
    // Get associations required for movie poster.
    $file = $_FILES['poster'];
    $title = $_POST['title'];

    // Save movie poster
    $thumbpath = $this->saveMoviePoster($file, $title);

    // Set thumbpath to the path of the newly saved movie poster.
    $movieData['thumbpath'] = $thumbpath;

    // Create movie using $movieData
    $this->dbAdapter->dbOpen();
    $this->dbAdapter->createMovie($movieData);
    $this->dbAdapter->dbClose();
    $this->error = $this->dbAdapter->lastError();

    return;
  }

  /**
   * Edits a movie (limited to only rental/purchase prices and stock information).
   * @param $movieData An array containing the movie information to be updated. Must contain movie_id.
   */
  public function editMovie($movieData)
  {
    $this->dbAdapter->dbOpen();
    $result = $this->dbAdapter->editMovie($movieData);
    $this->dbAdapter->dbClose();
    $this->error = $this->dbAdapter->lastError();
  }

  /**
   * Deletes an entry for a movie from the system's database (as well as its poster).
   * @param $movie_id The movie_id value for the movie to be deleted.
   * @param $thumbpath The relative path to the movie's poster.
   */
  public function deleteMovie($movie_id, $thumbpath)
  {
    // Delete movie poster
    if (!unlink($thumbpath)) {
      $this->error = "Failed to delete movie poster - is the directory correct?";
      return;
    }

    $this->dbAdapter->dbOpen();
    $result = $this->dbAdapter->deleteMovie($movie_id);
    $this->dbAdapter->dbClose();
    $this->error = $this->dbAdapter->lastError();
  }

  /**
   * Attempts to save a movie poster to the system's local storage.
   * @param $file The file to be saved.
   * @param $movie_title The title of the movie for the which the poster is being saved for.
   * @return string The name of the file saved, in the format of the unix timestamp followed by the movie's title with no whitespace.
   */
  public function saveMoviePoster($file, $movie_title)
  {
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileError = $file['error'];
    $fileExt = strtolower(end(explode(".", $fileName)));    // e.g. ".png"

    // If file was uploaded to server without any errors...
    if ($fileError == 0) {

      // Create file name string - unix timestamp + movie title w/o whitespace.
      $fileNameNew = time() . str_replace(" ", "", $movie_title) . "." . $fileExt;
      $fileDestination = _MOVIE_PHOTO_FOLDER_ . $fileNameNew;

      // Move file from temp storage to poster location, effectively finalising the upload.
      move_uploaded_file($fileTmpName, $fileDestination);
      return $fileNameNew;
    } else {
      $this->error = "Something went wrong.";
    }
  }

  /**
   * Check if a value already exists for a field.
   * @param $field e.g. username, address, landline.
   * @param $value A valid value for the selected field.
   * @return bool True if the value already exists, false otherwise.
   */
  public function checkIfExists($field, $value)
  {
    $this->dbAdapter->dbOpen();
    $result = $this->dbAdapter->checkIfExists($field, $value);
    $this->dbAdapter->dbClose();
    $this->error = $this->dbAdapter->lastError();

    return $result;
  }

  /**
   * Retrieves the data needed to laod the 'create movie' form - e.g. a list of actors, directors, etc.
   * @return array An array of all directors, studios, genres, and actors.
   */
  public function getCreateMovieData()
  {
    $this->dbAdapter->dbOpen();
    $result = $this->dbAdapter->getCreateMovieData();
    $this->dbAdapter->dbClose();
    $this->error = $this->dbAdapter->lastError();

    return $result;
  }

  /**
   * Retrieves the stock data of each movie within the database.
   * @return array An array containing the number of available and rented DVDs and BluRays.
   */
  public function getMovieStockData()
  {
    $this->dbAdapter->dbOpen();
    $result = $this->dbAdapter->getMovieStockData();
    $this->dbAdapter->dbClose();
    $this->error = $this->dbAdapter->lastError();

    return $result;
  }
}
?>