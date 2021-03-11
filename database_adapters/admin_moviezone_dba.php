<?php
/*dbAdapter: this module acts as the database abstraction layer for the application
@Module: admin_moviezone_dba.php
@Author: Vinh Bui (vinh.bui@scu.edu.au)
@Modify by: Matt Bowles (m.bowles.16@student.scu.edu.au)
@Version: 1.0
*/


require_once('moviezone_config.php');   // Connection parameters

/**
 * DBAdpater class performs all required CRUD functions for the application
 */
class AdminDBAdapter
{
  private $dbConnectionString;
  private $dbUser;
  private $dbPassword;
  private $dbConn; //holds connection object
  private $dbError; //holds last error message

  /**
   * @param $dbConnectionString e.g. mysql:host=localhost;dbname=mbowle16_moviezone_db.
   * @param $dbUser username for an account with CRUD privileges.
   * @param $dbPassword
   */
  public function __construct($dbConnectionString, $dbUser, $dbPassword)
  {
    $this->dbConnectionString = $dbConnectionString;
    $this->dbUser = $dbUser;
    $this->dbPassword = $dbPassword;
  }

  /**
   * Destroys the connection to the database.
   */
  public function __destruct()
  {
    $this->dbConn = null;
  }

  /**
   * Opens connection to the database. Sets error and sets $dbConn to null if true.
   */
  public function dbOpen()
  {
    try {
      $this->dbConn = new PDO($this->dbConnectionString, $this->dbUser, $this->dbPassword);
      // set the PDO error mode to exception
      $this->dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $this->dbError = null;
    } catch (PDOException $e) {
      $this->dbError = $e->getMessage();
      $this->dbConn = null;
    }
  }

  /**
   * Closes the connection to the web application's database.
   */
  public function dbClose()
  {
    //in PDO assigning null to the connection object closes the connection
    $this->dbConn = null;
  }

  /**
   * @return The last error that was encountered by the database.
   */
  public function lastError()
  {
    return $this->dbError;
  }

  /**
   * Simply prepares and executes a SQL query on the server.
   * @param $query The SQL query to be prepared and executed on the web application's database.
   * @param $notSelect Whether the query being executed is a SELECT query.
   * @param $selectSingle Whether or not the query being executed wishes to select only a single column.
   * @return The result of the SQL query that was executed.
   */
  private function executeQuery($query, $notSelect=false, $selectSingle=false)
  {
    $result = null;
    $this->dbError = null; // reset error message before execution

    if ($this->dbConn != null) {
      try {
        // Prepare SQL query
        $query = $this->dbConn->prepare($query);

        // Execute query
        $query->execute();

        if($selectSingle) {
          return $query->fetch();
        }

        // Retrieve results from the execution (if the query executed is SELECT)
        if (!$notSelect) {
          // Fetch result(s), if data is being obtained from db.
          $result = $query->fetchAll(PDO::FETCH_ASSOC);
        }
      } catch (PDOException $e) {
        // Set error msg
        $this->dbError = $e->getMessage();
        $result = null;
      }
    } else {
      $this->dbError = MSG_ERR_CONNECTION;
    }
    return $result;
  }

  /**
   * @return An array of all registered member usernames with their member_id.
   */
  public function selectAllUsers()
  {
    $query = "SELECT surname, other_name, username, member_id FROM member ORDER BY surname;";
    return $this->executeQuery(strval($query)); // convert query to string
  }

  /**
   * Selects all movie ids, titles, and years of release.
   * @return An array of all movie ids, movie titles, and years of release in the database.
   */
  public function selectAllMovies()
  {
    $query = "SELECT movie_id, title, year from movie ORDER BY title;";
    return $this->executeQuery(strval($query)); // convert query to string
  }

  /**
   * @param $id A valid member_id.
   * @return All information associated with a specific member_id.
   */
  public function getMemberInfo($id)
  {
    $query = "SELECT * FROM member WHERE member_id='$id'";
    $result = $this->executeQuery($query, false, true);
    return $result;
  }

  /**
   * @param $id A valid movie_id.
   * @return All information associated with a specific movie_id.
   */
  public function getMovieInfo($id)
  {
    $query = "SELECT * FROM movie_detail_view WHERE movie_id='$id'";
    $result = $this->executeQuery($query, false, true);
    return $result;
  }

  /**
   * Registers a new entry for a field in the database.
   * Valid fields that can be inserted: director/studio/genre/actor.
   * @param $field The type of field the value will be inserted for (e.g. director, studio, genre, etc.)
   * @param $value The value of the field to be inserted.
   */
  private function insertNew($field, $value)
  {
    // A list of fields that where inserting is acceptable.
    $VALID_INSERT_FIELDS = array(
      'director', 'studio', 'genre',
      'star1', 'star2', 'star3',
      'costar1', 'costar2', 'costar3'
    );
    $field = strtolower($field);    // Ensure correct formatting.

    // Ensure that field is acceptable for inserting.
    if (in_array($field, $VALID_INSERT_FIELDS)) {

      // If field contains 'star' (star1, costar1, etc.), set field to 'actor'.
      // i.e. A new value will be inserted into the 'actor' table.
      if (strpos($field, 'star') !== false) {
        $field = "actor";
      }

      //e.g. actor_name.
      $fieldName = $field . "_name";

      // Form the insert query.
      $query = "INSERT INTO $field ($fieldName) VALUES ('$value');";

      // Execute the insert query.
      $this->executeQuery($query, true);

      // Return the ID value generated via the insertion.
      return $this->dbConn->lastInsertId();
    }

  }

  /**
   * Checks if a value for a specific field (in a table) exists.
   * @param $field The field that will be queried (e.g. tagline, plot, classification, star1, etc.)
   * @param $val The value which will be checked for uniqueness in the provided field.
   * @return bool True if the value for the field is already in use, false otherwise.
   */
  public function checkIfExists($field, $val)
  {
    $field = strtolower($field);
    $val = strtolower($val);

    // If the passed field contains the word star, set the field to 'actor'.
    // i.e. the provided value will be checked for uniqueness in the 'actor' table.
    if (strpos($field, 'star') !== false) {
      $field = "actor";
    }

    if ($field == 'tagline' || $field == 'plot' || $field == 'classification') {
      $name = $field;
      $field = 'movie';
    } else {
      $name = $field."_name";   // e.g. director_name
    }

    $query = "SELECT $name FROM $field WHERE $name = \"$val\"";

    $result = $this->executeQuery($query);

    // If there was a result, return true (it exists, hence not unique).
    if ($result == null) {
      return false;
    } else {
      return true;
    }
  }

  /**
   * Registers a new movie in the system's database and creates associations for the actors that star in it.
   * @param $movieData An array containing all neccessary data for the movie/actors to be inserted.
   */
  public function createMovie($movieData)
  {
    $movieData = array_filter($movieData);    // Get rid of empty values.

    // Fields where the value must be unique in the database.
    $unique_fields = array(
      'director', 'studio', 'genre',
      'star1', 'star2', 'star3',
      'costar1', 'costar2', 'costar3'
    );

    // Check whether the unique fields in the movie data contain values that already existing (denoted by an integer, i.e. an ID).
    // Insert as new value into respective table if doesn't exist.
    foreach($unique_fields as $field) {
      if (key_exists($field, $movieData)) {
        $val = $movieData[$field];

        // Check if value is a number - e.g. is an ID, insert as new if false.
        if (!is_numeric($val)) {
          // Doesn't exist, insert into table and grab generated ID.
          $id = $this->insertNew($field, $val);
          $movieData[$field] = $id;
        }
      }
    }

    $actorTypes = array(
      'star1', 'star2', 'star3',
      'costar1', 'costar2', 'costar3'
    );

    // Holds actor ids/role in movie
    $actorData = array();

    // Populate actorData by removing actors from movieData and adding them to actorData.
    foreach($actorTypes as $key) {
      if (isset($movieData[$key])) {
        $actorData[$key] = $movieData[$key];
        unset($movieData[$key]);
      }
    }

    // Remove any unused/empty actor roles from the array.
    $actorData = array_filter($actorData);

    // Very inefficient, could be refactored.
    $movieData['director_id'] = $movieData['director'];
    unset($movieData['director']);
    $movieData['studio_id'] = $movieData['studio'];
    unset($movieData['studio']);
    $movieData['genre_id'] = $movieData['genre'];
    unset($movieData['genre']);

    // Form strings to be used in the insert movie query.
    $fields_string = implode(", ", array_keys($movieData));
    $values_string = implode("\",\"", array_values($movieData));
    $values_string = "\"".$values_string."\"";

    // Finalise insert movie query.
    $query = "INSERT INTO movie ($fields_string) VALUES ($values_string)";

    // Insert movie without actors and grab ID.
    $this->executeQuery($query, true);
    $movie_id = $this->dbConn->lastInsertId();

    // Insert actors into movie_actor table.
    foreach ($actorData as $key=>$value) {
      $query = "INSERT INTO movie_actor (movie_id, actor_id, role) VALUES ('$movie_id', '$value', '$key')";
      $this->executeQuery($query, true);
    }
  }

  /**
   * Deletes a movie and its associated movie poster from the system.
   * @param $movie_id The movie to be deleted.
   * @return The|void
   */
  public function deleteMovie($movie_id)
  {
    // Delete entry for movie within 'movie' table.
    $query = "DELETE FROM movie WHERE movie_id='$movie_id'";
    $this->executeQuery($query, true);

    // Check if an error occurred while deleting the movie (not actors), return if true.
    if (!empty($this->dbError)) {
      return;
    }

    // Delete each entry for movie within 'movie_actor' table.
    $query = "DELETE FROM movie_actor where movie_id='$movie_id'";
    return $this->executeQuery($query, true);
  }

  /**
   * Edits the details of a movie.
   * @param $movieData An array containing all data to be updated. Must include movie_id.
   */
  public function editMovie($movieData)
  {
    $query = $this->formEditQuery($movieData, 'movie');

    // Execute the update query.
    $this->executeQuery($query, true);
  }

  /**
   * Deletes a member account from the database.
   * @param $member_id The member id value for the account to be deleted.
   */
  public function deleteMember($member_id)
  {
    $this->executeQuery("DELETE FROM member WHERE member_id='$member_id';");
  }

  /**
   * Edits the information of a member account in the database.
   * @param $memberData An array containing all information to be updated. Must include the member_id.
   */
  public function editMember($memberData)
  {
    $query = $this->formEditQuery($memberData, 'member');

    // Execute the update query.
    $this->executeQuery($query, true);
  }

  /**
   * Forms a query that is executed to update existing data stored on the system's database.
   * @param $data An array containing all data to be updated.
   * @param $table The name of the table to be updated. This value is also used to refer to the dataset's ID (should be formatted as tablename_id).
   * @return string The edit query generated by the method.
   */
  private function formEditQuery($data, $table)
  {
    $id_name = $table."_id";
    $id = $data[$id_name];              // Get reference to id value.
    unset($data[$id_name]);             // Unset so that it's not included in the query.
    $query = "UPDATE $table SET ";
    $i = 0;

    foreach($data as $key=>$val) {
      $query .= "$key='$val'";

      if ($i != intval(sizeof($data))-1) {
        $query .= ", ";
      }
      $i++;
    }

    $query .= " WHERE $id_name ='$id';";

    return $query;
  }

  /**
   * Fetches all the 'dynamic data' that can be selected when creating a movie.
   * (e.g. director names, director IDs, etc.).
   *
   * Classifications are not included as it is assumed that they will always remain constant.
   * @return array An array containing
   */
  public function getCreateMovieData()
  {
    $data = array('directors', 'studios', 'genres', 'actors');
    foreach($data as $key) {
      $type = substr($key, 0, strlen($key)-1);    // "directors" --> director, "actors" --> actor, etc.
      $id = $type."_id";
      $name = $type."_name";
      $tbl = $type;

      $data[$key] = $this->executeQuery("SELECT $id AS id, $name AS name FROM $tbl ORDER BY $name");
    }

    return $data;
  }

  /**
   * Fetches data about the stock of all movies in the system's database.
   * @return The title/id of each movie, as well as its stock information for both DVD and BluRay.
   */
  public function getMovieStockData()
  {
    $query = "SELECT movie_id, title, numDVD, numDVDout, numBluRay, numBluRayOut FROM movie ORDER BY title";
    return $this->executeQuery($query);
  }
}
?>