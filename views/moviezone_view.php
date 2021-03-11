<?php
session_start();
/*-------------------------------------------------------------------------------------------------
@Module: moviezone_view.php
This server-side module provides all required functionality to format and display movies in html.

This module shares both the member and guest functionality as the majority of their functionality is the same.

@Author: Vinh Bui (vinh.bui@scu.edu.au)
@Modified by: Matt Bowles (m.bowles.16@student.scu.edu.au)
@Date: 09/09/2017
--------------------------------------------------------------------------------------------------*/

class GuestMovieZoneView {

  /**
   * Prints the top navbar through reading a premade HTML template.
   * This navbar contains links to the home/contact/techzone/moviezone/join pages.
   */
  public function topNavbar()
  {
    print file_get_contents(_HTML_TEMPLATE_FOLDER_.'topNavbar.html');
  }

  /**
   * Prints the left moviezone panel through reading a premade HTML template.
   * This panel contains the control panel used to filter movies (show all, new releases, actor, director, genre, classification) as well as the necessary login/logout/admin buttons.
   */
  public function leftMZPanel()
  {
    if (isset($_SESSION['member']) && $_SESSION['member'] == true) {
      print file_get_contents(_HTML_TEMPLATE_FOLDER_.'MZLeftPanel_Member.html');
    } else {
      // User is a guest
      print file_get_contents(_HTML_TEMPLATE_FOLDER_.'MZLeftPanel_Guest.html');
    }
  }

  /**
   * Prints the footer of the page.
   */
  public function footer()
  {
    print file_get_contents(_HTML_TEMPLATE_FOLDER_.'footer.html');
  }

  /**
   * Prints the form used to sign up for a member account.
   */
  public function signupForm()
  {
    print file_get_contents(_HTML_TEMPLATE_FOLDER_.'memberSignupForm.html');
  }

  /**
   * Prints a message, indicating that the user has successfully signed up for a member account.
   * The user's first name/other_name is required to be set in the $_POST superglobal.
   */
  public function signupSuccess()
  {
    print<<<END
    <h2 style="text-align: center">Status</h2>
    <p style="text-align: center;">Congraluations $_POST[other_name] you have successfully signed up at the DVD Emporium and can now book movies!
    <br>
    <a href="moviezone.php" style="text-decoration: underline;">Would you like to go to the MovieZone page and Log In?</a>
    </p>
END;
  }

  /**
   * @param $error The error message to be printed.
   */
  public function showError($error)
  {
    print "<h2 id='error_text'>Error: $error</h2>";
  }

  /**
   * Prints a <select> element with appropriate <option>s for a predefined set of  filter types (actor, director, etc.)
   * @param $selectType The type of select element that will be constructed for a filter (e.g. filter by actor, director, etc.).
   * @param $items An array of 'items' which will each be printed as an <option> element.
   */
  public function showSelect($selectType, $items)
  {
    $selectType = strtolower($selectType);  // Ensures correct formatting for the following switch/case statement

    // Determine the type of select element to be established, as well as any of the required database columns to be displayed (e.g. actor_name, director_name, etc.).
    switch($selectType) {
      case 'actor':
        $val = 'actor_id';
        $txt = 'actor_name';
        break;
      case 'director':
        $val = 'director_id';
        $txt = 'director_name';
        break;
      case 'genre':
        $val = 'genre_id';
        $txt = 'genre_name';
        break;
      case 'classification':
        $val = 'classification';
        $txt = $val;
        break;
      default:
        $this->showError("invalid \$selectType used.");
        return;
    }

    $selectID = $selectType.'Select';   // An ID is assigned so that its value can be read by JS functions.

    // Print the <select>/<form> element.
    // The default <option> (i.e. Select...) is also printed here.
    print "<div id='select'>
             <select id='$selectID' onchange='selectFilterChanged()'>
             <option disabled='' selected=''>Select...</option> // default
           </div>";

    // Print options for the <select> element
    foreach ($items as $item) {
      print "<option value='$item[$val]'>$item[$txt]</option>";
    }
  }

  /**
   * Cycles through an array of movies that are then printed.
   * @param $movies An array of movies that is to be printed/formatted using HTML.
   * @param bool $fullDetail Specifies whether or not the movies will be printed with complete details.
   */
  public function showMovies($movies, $fullDetail=true)
  {
    if (!empty($movies)) {
      foreach ($movies as $movie) {
        $this->printMovieinHTML($movie, $fullDetail);
      }
    }
  }

  /**
   * Prints a movie in HTML utilising ALL information collected from movie_detail_view.
   * @param $movie The movie to be printed (in full detail).
   */
  private function printMovieInHTML($movie, $fullDetail)
  {
    // Associate variables with obtained information for a particular movie in the database.
    // Enables information to be printed more easily.
    $thumbpath = _MOVIE_PHOTO_FOLDER_.$movie['thumbpath'];
    $title = $movie['title'];
    $rentalPeriod = $movie['rental_period'];
    $genre = $movie['genre'];
    $year = $movie['year'];
    $director = $movie['director'];
    $classification = $movie['classification'];
    $studio = $movie['studio'];
    $tagline = $movie['tagline'];
    $plot = $movie['plot'];
    $dvdRentalPrice = $movie['DVD_rental_price'];
    $dvdPurchasePrice = $movie['DVD_purchase_price'];
    $dvdAvailibility = $movie['numDVD'] - $movie['numDVDout'];
    $blurayAvailibility = $movie['numBluRay'] - $movie['numBluRayOut'];
    $blurayRentalPrice = $movie['BluRay_rental_price'];
    $blurayPurchasePrice = $movie['BluRay_purchase_price'];

    // Get correctly formatted string of starring actors.
    $starringString = $this->getStarringString($movie);

    // Determine the class type. New releases will have the 'newRelease' class appended so that extra formatting can be performed.
    if ($fullDetail) {
      $className = "movie";
    } else {
      $className = "movie newRelease";
    }

    // Format the obtained details for movie in HTML.
    print "<div class='$className'><legend>$title</legend>";

    // If user is logged in, display availability/add to cart button for each movie.
    // (buttons not printed for the 'new release' display)
    if ($fullDetail && isset($_SESSION['member'])) {
      $this->printButton($blurayAvailibility, $dvdAvailibility, $movie['movie_id']);
    }

    print "<img src=$thumbpath alt='Movie poster'>";

    // Rental period is excluded from the 'new release' display.
    if ($fullDetail) {
      print "<b>$rentalPeriod Rental</b><br>";
    }

    print "<b>Genre:</b> $genre<br>";

    // Year of release is excluded from the 'new release' display.
    if ($fullDetail) {
      print "<b>Year:</b> $year<br>";
    }

    print <<<END
      <b>Director:</b> $director<br>
      <b>Classification:</b> $classification<br>
      <b>Starring:</b> $starringString<br>
      <b>Studio:</b> $studio<br>
      <b>Tagline:</b> $tagline<br><br>
      $plot
END;

    // DVD/BluRay information is excluded from the 'new release' display.
    if ($fullDetail) {
      print <<<END
      <br><br>
      <b>Rental:</b> DVD - \$$dvdRentalPrice  BluRay - \$$blurayRentalPrice<br>
      <b>Purchase:</b> DVD - \$$dvdPurchasePrice  BluRay - \$$blurayPurchasePrice<br>
      <b>Availability:</b> DVD - $dvdAvailibility  BluRay - $blurayAvailibility
END;
    }
    print "</div>";
  }

  /**
   * Generates a 'starring string' for a movie - accepts any number of stars/costars.
   * @param $movie A movie which a starring string will be generated for.
   * @return string The generated starring string - e.g. "star1, star2, star3 and costar1."
   */
  public function getStarringString($movie)
  {
    // All actors in a given movie (removes any empty values from the array).
    $actors = array_filter(array($movie['star1'], $movie['star2'], $movie['star3'], $movie['costar1'],
                                 $movie['costar2'], $movie['costar3']));

    $starringString = '';   // Initalises the output.
    $i = 0;                 // Used to determine when last actor is being iterated.

    foreach ($actors as $actor) {
      $i++;

      // Last actor in array.
      // e.g. " and lastActor"
      if ($i == sizeof($actors)) {
        $starringString .= " and $actor.";
        break;
      }

      // If actor is between first and last actors in array.
      // e.g. ", middleActor"
      if ($actor != $actors[0]) {
        $starringString .= ", $actor";
      }

      // If actor is 'star1' (i.e. first actor in array).
      // e.g. "firstActor"
      else {
        $starringString .= "$actor";
      }
    }

    return $starringString;
  }

  /**
   * Prints a button for a movie that when clicked, may add the movie to the user's cart.
   * @param $blurayAvailibility The amount of BluRays currently available for a movie.
   * @param $dvdAvailibility The amount of DVDs currently available for a movie.
   * @param $movie_id The ID of the movie which the button will be created for.
   */
  public function printButton($blurayAvailibility, $dvdAvailibility, $movie_id)
  {
    if (!isset($_SESSION['movies_in_cart'])) {
      // Function is being called when the 'movies_in_cart' session variable is not initalised.
      // Perhaps they are not logged in as a member.
      return;
    }

    $available = true;  // Whether a movie can be added to the user's cart.

    // Movie rental status messages
    $outOfStock = "Currently Out of Stock";
    $selected = "Already Selected";
    $maxSelected = "Maximum Selection Made";
    $bothAvailable = "Rent/Purchase";
    $blurayAvailable = "Only BluRay in Stock";
    $dvdAvailable = "Only DVD in Stock";

    if (in_array($movie_id, $_SESSION['movies_in_cart'])) {
      // If movie is already in the user's cart.
      $btnTxt = $selected;
      $available = false;
    } else if (intval(sizeof($_SESSION['movies_in_cart']) > 4)) {
      // If user already has 5 movies in their cart.
      $btnTxt = $maxSelected;
      $available = false;
    } else if ($blurayAvailibility >= 1 && $dvdAvailibility >= 1) {
      // Both BluRay/DVD are available for rent/purchase.
      $btnTxt = $bothAvailable;
    } else if ($blurayAvailibility >= 1 && $dvdAvailibility == 0) {
      $btnTxt = $blurayAvailable;
    } else if ($blurayAvailibility == 0 && $dvdAvailibility >= 1) {
      $btnTxt = $dvdAvailable;
    } else {
      // Movie must be out of stock.
      $btnTxt = $outOfStock;
      $available = false;
    }

    if ($available) {
      // Adds the movie to the user's cart when clicked.
      print "<input id='btn_$movie_id' type='button' value='$btnTxt' onclick='addToCart($movie_id)'><br>";
    } else {
      // Doesn't do anything when clicked.
      print "<input type='button' value='$btnTxt'><br>";
    }
  }

  /**
   * Prints every movie that is within the member's cart. This function is called upon loading the checkout page.
   * @param $movies An array of up to five (5) movies containing information as seen within movie_detail_view.
   */
  public function printCart($movies)
  {
    foreach ($movies as $movie) {
      $movie_id = $movie['movie_id'];
      $thumbpath = _MOVIE_PHOTO_FOLDER_.$movie['thumbpath'];
      $title = $movie['title'];
      $year = $movie['year'];
      $tagline = $movie['tagline'];
      $dvdAvailibility = $movie['numDVD'] - $movie['numDVDout'];
      $blurayAvailibility = $movie['numBluRay'] - $movie['numBluRayOut'];

      $availibilityString = "";

      if ($dvdAvailibility >= 1 && $blurayAvailibility >= 1) {
        // Both dvd and bluray are available for rent/purchase.
        $availibilityString = "$dvdAvailibility DVDs are available and $blurayAvailibility BluRays are available";
      } else if ($dvdAvailibility >= 1 && $blurayAvailibility == 0) {
        // Only dvd is available.
        $availibilityString = "Only $dvdAvailibility DVDs are available";
      } else {
        // Only bluray is available.
        $availibilityString = "Only $blurayAvailibility BluRays are available";
      }

      // Print the checkout box for the movie.
      print <<<END
      <div class='movie checkoutItem'>
        <legend>Movie $movie_id Information</legend>
        <img src=$thumbpath alt='Movie poster'>
          <br>
        <label>Title:</label><input type="text" size="45" disabled value="$title">
          <br>
        <label>Year:</label><input type="text" size="4" disabled value="$year">
          <br>
        <label>Tagline:</label><input type="text" size="60" disabled value="$tagline">
          <br> 
          <br>
        $availibilityString   
      </div>
END;

    }
  }
}
?>