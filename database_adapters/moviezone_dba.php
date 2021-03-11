<?php
/*dbAdapter: this module acts as the database abstraction layer for the application

This module shares both the member and guest functionality as the majority of their functionality is the same.

@Module: moviezone_dba.php
@Author: Vinh Bui (vinh.bui@scu.edu.au)
@Modify by: Matt Bowles (m.bowles.16@student.scu.edu.au)
@Version: 1.0
*/

require_once('moviezone_config.php');   // Connection parameters

/**
 * DBAdpater class performs all required CRUD functions for the application
 */
class GuestDBAdapter {

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
		}
		catch(PDOException $e) {
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
   * @param $condition The condition which the query will be filtered by (e.g. WHERE director_id = xyz)
   * @return An array of movie IDs where the condition is met.
   */
	public function filterMovies($condition, $val)
  {
    $tbl = "movie";
	  // Check for filter type & form query (using parameter)
    switch($condition) {
      case 'actor_id':
        $field = 'actor_id';
        $tbl = 'movie_actor';
        break;
      case 'director_id':
        $field = 'director_id';
        break;
      case 'genre_id':
        $field = 'genre_id';
        break;
      case 'classification':
        $field = 'classification';
        break;
      default:
        // Invalid filter type
        print "Invalid filter type used.";
        return;
    }
    $query = "SELECT movie_id FROM $tbl WHERE $field = '$val';";
    return $this->executeQuery($query);   // Execute the filter query and return the result (to model-->controller).
  }

  /**
   * Gets all data within the movie_detail_view for a particular movie_id.
   * @param $movie_id A valid movie_id (integer).
   * @return An array containing all data for a valid movie_id.
   */
  public function getMovieData($movie_id)
  {
    return $this->executeQuery("SELECT * FROM movie_detail_view WHERE movie_id='$movie_id';");
  }

  /**
   * This method is used when 'showing all movies'.
   * @return An array containing details of every movie recorded the system's database.
   */
	public function selectAllMovies()
  {
    return $this->executeQuery("SELECT * FROM movie_detail_view;");
  }

  /**
   * Selects all id/name values of a particular field in the 'movies' table.
   * @param $field The field for which all id/name values will be returned.
   * @return An array containing all id/name values for a field.
   */
  public function selectAllFromMovies($field) {
    $field = strtolower($field);

    if ($field == 'classification') {
      $query = "SELECT DISTINCT classification FROM movie ORDER BY classification;";
    } else {
      $id = $field . "_id";
      $name = $field . "_name";
      $tbl = $field;
      $query = "SELECT $id, $name FROM $tbl ORDER BY $name";
    }

    return $this->executeQuery($query);
  }

  /**
   * Selects a specified amount of new releases from the database (max 10).
   * @param $n The amount of new releases to be selected (max 10).
   * @return The new releases in the system (defined by movies that have a rental_period of 'Overnight').
   */
  public function selectNewReleases($n)
  {
	  $n>10 ? 10 : $n;  // If $n is over the defined max., then set to max.
    return $this->executeQuery("SELECT movie_id FROM movie_detail_view AS new_releases WHERE rental_period = 'Overnight' ORDER BY RAND() LIMIT $n");
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
   * Returns the password of a member account. If the account doesn't exist or
   * the password is incorrect, null is returned.
   * @param $username A (possibly invalid) member account.
   * @return Password of a user.
   */
  public function getPassword($username)
  {
    $result = $this->executeQuery("SELECT password, CONCAT(other_name, ' ', surname) AS full_name FROM member WHERE username='$username'", false, true);

    if (!empty($result)) {
      return $result;
    }
    return null;
  }

  /**
   * @return All information as contained within movie_detail_view for every movie in the user's cart.
   */
  public function selectAllMoviesInCart()
  {
    $query = "SELECT * FROM movie_detail_view WHERE movie_id IN (";

    $i = 0;
    foreach ($_SESSION['movies_in_cart'] as $movie) {
      $query .= $movie;
      if ($i != intval(sizeof($_SESSION['movies_in_cart'])-1)) {
        $query .= ',';
      }
      $i++;
    }

    return $this->executeQuery($query.')');
  }

  /**
   * Checks if a value for a particular field already exists.
   * For now, 'username' is the only supported field.
   * @param $field e.g. username, landline, whatever.
   * @param $val The value which will be checked for uniqueness in the field.
   * @return bool True if the field already exists, false otherwise.
   */
  public function checkIfExists($field, $val)
  {
    $ACCEPTED_FIELDS = array('username');

    $field = strtolower($field);
    $val = strtolower($val);

    if (!in_array($field, $ACCEPTED_FIELDS)) {
      print "Invalid field type used, please select one of the following:";
      print implode(", ", $ACCEPTED_FIELDS);
    }

    if ($field == 'username') {
      $name = $field;
      $field = 'member';
    }

    $query = "SELECT $name FROM $field WHERE $name = \"$val\"";
    $result = $this->executeQuery($query);

    if ($result == null) {
      return false;
    } else {
      return true;
    }
  }

  /**
   * Registers an new entry for a member.
   * @param $memberData An array containing all necessary member information.
   */
  public function createMember($memberData){
    $memberData["join_date"] = date("Y-m-d");   // Set join date - e.g. 2019-05-13

    $fields_string = implode(", ", array_keys($memberData));
    $values_string = implode("\",\"", array_values($memberData));
    $values_string = "\"".$values_string."\"";

    $query = "INSERT INTO member ($fields_string) VALUES ($values_string)";

    $this->executeQuery($query, true);
  }
}
?>