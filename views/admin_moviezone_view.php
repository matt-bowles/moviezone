<?php
/*-------------------------------------------------------------------------------------------------
@Module: admin_moviezone_view.php
This server-side module provides all required functionality to format and display movies/users in html

@Author: Vinh Bui (vinh.bui@scu.edu.au)
@Modified by: Matt Bowles (m.bowles.16@student.scu.edu.au)
@Date: 09/09/2017
--------------------------------------------------------------------------------------------------*/

class AdminMovieZoneView {

  /**
   * Prints the footer of the page.
   */
  public function footer()
  {
    print file_get_contents(_HTML_TEMPLATE_FOLDER_.'footer.html');
  }

  /**
   * Prints the admin login form.
   */
  public function adminLoginForm()
  {
    print file_get_contents(_HTML_TEMPLATE_FOLDER_.'loginForm_Admin.html');
  }

  /**
   * Prints the admin welcome message.
   */
  public function adminWelcome()
  {
    print "<p style='text-align:center'>Please select an action from the menu.</p>";
  }

  /**
   * Prints the admin nav panel - containing links to edit/delete a member, create a member, edit/delete a movie, create a movie, view movie stock report, and log out.
   */
  public function leftPanelAdmin()
  {
    print file_get_contents(_HTML_TEMPLATE_FOLDER_.'leftPanel_Admin.html');
  }

  /**
   * Prints a select containing all members registered in the database.
   * When a member is selected (and the search button is pressed), the edit/delete member form is loaded containing the user data of the selected member.
   * @param $members An array containing the surname, other_name, username, and member_id of every member in the database.
   */
  public function showSelectMember($members)
  {
    print <<<END
    <h4>Select User to Edit/Delete</h4>
    <p>Users are shown in dropdown as:<br>Surname, Other names - Username</p>
    <select id="member_select">
    <option disabled="" selected="" value="">Select...</option>  // Defualt option
END;

    foreach($members as $member) {
      $surname = $member['surname'];
      $other_name = $member['other_name'];
      $username = $member['username'];
      $member_id = $member['member_id'];
      print "<option value='$member_id'>$surname, $other_name - $username</option>";
    }
    print "</select>";
    print "<button style='margin-left: 10px' onclick='getMemberDetails()'>Search</button>";

  }

  /**
   * Prints a select element containing each movie within the database.
   * @param $movies A list of movies to be printed (must contain 'title', 'year', and 'movie_id').
   */
  public function showSelectMovie($movies)
  {
    print <<<END
    <h4>Select Movie to Edit/Delete</h4>
    <p>Movies are shown in dropdown as:<br>Title - Year</p>
    <select id="movie_select">
    <option disabled="" selected="" value="">Select...</option>  // Defualt option
END;
    // Print each option for the <select> element.
    foreach($movies as $movie) {
      $movie_id = $movie['movie_id'];
      $title = $movie['title'];
      $year = $movie['year'];
      print "<option value='$movie_id'>$title - $year</option>";
    }
    print "</select>";
    print "<button style='margin-left: 10px' onclick='getMovieDetails()'>Search</button>";

    $numMovies = sizeof($movies);
    print "<br><br>Currently there are $numMovies movies in the database";
  }

  /**
   * @param $error The error message to be printed.
   */
  public function showError($error)
  {
    print "<h2 id='error_text''>Error: $error</h2>";
  }


  /**
   * Prints a form containing all information with a user, as contained with the array for the required $userData parameter
   * @param $memberData An array containing all data within the 'member' table for a particular member.
   */
  public function editDeleteMemberForm($memberData)
  {
    $member_id = $memberData['member_id'];
    $surname = $memberData['surname'];
    $other_name = $memberData['other_name'];
    $username = $memberData['username'];
    $password = $memberData['password'];
    $occupation = $memberData['occupation'];
    $join_date = $memberData['join_date'];
    $contact_method = $memberData['contact_method'];
    $email = $memberData['email'];
    $mobile = $memberData['mobile'];
    $landline = $memberData['landline'];
    $wants_magazine = $memberData['magazine'];
    $street = $memberData['street'];
    $suburb = $memberData['suburb'];
    $postcode = $memberData['postcode'];

    $occupations = array("Student", "Manager", "Medical", "Trades", "Education", "Technician", "Clerical", "Retail", "Researcher", "Other");
    $contact_methods = array("email", "landline", "mobile");

    // Initalise the value of the checkbox, whether it should be required, and whether the 'required element' should be displayed.
    if ($wants_magazine == 1) {
      $checked = "checked";
      $magazine_required = "required";
      $magazaine_required_visibility = "visible";
    } else {
      $checked = "";
      $magazine_required = "";
      $magazaine_required_visibility = "hidden";
    }

    // Print 'Member ID' fieldset (contains surname, other names, password, occupation, and join date)
    print<<<END
    <!-- event.preventDefault() prevents the form from being submitted (hence refreshed), allowing for
         AJAX to be used instead. It also allows for the HTML 'required' attribute to be used. -->
    <form id="editMemberForm" name="editMemberForm" onsubmit="editMember();">
    <fieldset>
        <legend><i>Member ID: 28</i></legend>
        <input type='hidden' name="member_id" value="$member_id">
        <input type='hidden' name="request_type" value="edit_member">
        
        <div class="row">
            <label>Surname:</label>
            <input id="surname" name="surname" type="text" value="$surname" required>
        </div>
        <div class="row">
            <label>Other names:</label>
            <input id="other_name" name="other_name" type="text" value="$other_name" required>
        </div>
        <div class="row">
            <label>Username:</label>
            <input id="username" name="username" type="text" maxlength="10" value="$username" disabled>
        </div>
        <div class="row">
            <label>Password:</label>
            <input id="password" name="password" type="text" minlength="4" maxlength="10" value="$password" required>
        </div>
        <div class="row">
            <label>Occupation:</label>
            <select id="occupation" name="occupation" required>
END;
    foreach($occupations as $occ) {
      $selected = "";
      if ($occupation === $occ) {
        $selected = "selected";
      }
      print "<option value=\"$occ\" $selected>$occ</option>";
    }
    print<<<END
      </select>
      </div>
      <div class="row">
          <label>Join Date:</label>
          <input id="join_date" name="join_date" type="text" maxlength="10" value="$join_date" disabled>
      </div>
    </fieldset>
END;

    // Print 'Contact Details' fieldset (contains contact method, email, mobile, landline)
    print<<<END
    <fieldset>
    <legend><i>Contact Details</i></legend>
    <div class="row">
        <label>Contact method:</label>
        <select id="contact_method" name="contact_method" required onchange="change_contactMethod(this.value);">
END;
    // Print each possible contact method
    foreach($contact_methods as $contact) {
      $selected = "";
      if ($contact_method == $contact) {
        $selected = "selected";
      }
      print "<option value=\"$contact\" $selected>$contact</option>";
    }
    print<<<END
    </select>
    </div>
END;

    // Print an input element for each contact method
    foreach($contact_methods as $contact) {

      // This variable is used as a reference to the existing value of the contact method being iterated.
      // e.g. If the current iteration is "email", then contact_value will be set to the user's email (if provided).
      $contact_value = "";

      switch($contact) {
        case "email":
          $contact_value = $email;
          break;
        case "mobile":
          $contact_value = $mobile;
          break;
        case "landline":
          $contact_value = $landline;
          break;
      }

      // If preferred contact method is set to the current contact method being iterated,
      // then set the 'required' attribute of the element to be 'true' and display the required element (the "*").
      if ($contact_method == $contact) {
        $required_element_visibility = "visible";
        $required = "required";
      } else {
        $required_element_visibility = "hidden";
        $required = "";
      }
      print<<<END
      <div class="row">
          <label>$contact:</label>
          <input id="$contact" name="$contact" class="contact_inputs" $required value="$contact_value" width="46">
          <div class="required" style="visibility: $required_element_visibility" id="required_$contact">*</div>
      </div>
END;
    }

    // Print 'Magazine' fieldset (recieve magazine, address, suburb, postcode);
    print<<<END
    </fieldset>
    <fieldset>
    <legend><i>Magazine</i></legend>
    <div class="row">
        <label>Do you want to receive our monthly magazine?</label>
        <input id="magazine" name="magazine" type="checkbox" $checked onclick="change_wantsMagazine()">
    </div>
    <div class="row">
        <label>Street address:</label>
        <input id="street" name="street" type="text" class="magazine_inputs" value="$street" $magazine_required>
        <div class="required" id="required_street" style="visibility: $magazaine_required_visibility">*</div>
    </div>
    <div class="row">
        <label>Suburb and State:</label>
        <input id="suburb" name="suburb" type="text" class="magazine_inputs" value="$suburb" $magazine_required>
        <div class="required" id="required_suburb" style="visibility: $magazaine_required_visibility">*</div>
    </div>
    <div class="row">
        <label>Postcode:</label>
        <input id="postcode" name="postcode" type="text" minlength="4" maxlength="4" size="4" class="magazine_inputs" value="$postcode" $magazine_required>
        <div class="required" id="required_postcode" style="visibility: $magazaine_required_visibility">*</div>
    </div>
    </fieldset>
    </form>
    <div class="row">
      <input type="submit" form ="editMemberForm" value="Update User">
      <input type="submit" onclick="deleteMember()" value="Delete User">
    </div>
    <div class="row">
END;
  }

  /**
   * Prints a success message informing the user that a member has successfully been deleted.
   */
  public function deleteMemberSuccess()
  {
    $surname = $_POST['surname'];
    $other_name = $_POST['other_name'];
    $username = $_POST['username'];

    print "<h2>User: $surname, $other_name Username: $username deleted from System</h2>";
  }

  /**
   * Prints a success message informing the user that a member's details have successfully been edited.
   */
  public function editMemberSuccess()
  {
    $surname = $_POST['surname'];
    $other_name = $_POST['other_name'];

    print "<h2>Account for $other_name $surname successfully updated</h2>";
  }

  /**
   * Prints a success message informing the user that a movie's stock details have successfully been updated.
   */
  public function editMovieSuccess()
  {
    $movie_id = $_POST['movie_id'];
    $title = $_POST['title'];

    print "<h2>Movie $movie_id - $title successfully updated.</h2>";
  }

  /**
   * Prints a form that can be used by administrators to edit or delete the information of a movie.
   * @param $movieData An array containing movie_id, title, year, tagline, thumbpath, rental_period, and all stock/price information for DVD/BluRay
   */
  public function editDeleteMovieForm($movieData)
  {
    $movie_id = $movieData['movie_id'];
    $title = $movieData['title'];
    $year = $movieData['year'];
    $tagline = $movieData['tagline'];
    $thumbpath = _MOVIE_PHOTO_FOLDER_.$movieData['thumbpath'];
    $rental_period = $movieData['rental_period'];

    $DVD_rental_price = $movieData['DVD_rental_price'];
    $DVD_purchase_price = $movieData['DVD_purchase_price'];
    $numDVD = $movieData['numDVD'];
    $numDVDout = $movieData['numDVDout'];
    $numDVDinStore = $numDVD+$numDVDout;

    $BluRay_rental_price = $movieData['BluRay_rental_price'];
    $BluRay_purchase_price = $movieData['BluRay_purchase_price'];
    $numBluRay = $movieData['numBluRay'];
    $numBluRayOut = $movieData['numBluRayOut'];
    $numBluRayinStore = $numBluRay+$numBluRayOut;

    print <<<END
    <!-- event.preventDefault() prevents the form from being submitted (hence refreshed), allowing for
         AJAX to be used instead. It also allows for the HTML 'required' attribute to be used. -->
    <form id="editMovieForm" name="editMovieForm" onsubmit="editMovie();">
    <input name="thumbpath" value="$thumbpath" hidden>
    <input name="movie_id" value="$movie_id" hidden>
    <input name="title" value="$title" hidden>
    <fieldset>
        <legend>Movie Information:</legend>
        <img src="$thumbpath" alt="Movie poster" width="105" height="150" style="float: right;">
            <div class="row">
                <label>Movie ID:</label>
                <input type="text" disabled value="$movie_id">
            </div>
            <div class="row">
                <label>Title:</label>
                <input type="text" disabled value="$title">
            </div>
            <div class="row">
                <label>Year:</label>
                <input type="text" disabled value="$year">
            </div>
            <div class="row">
                <label>Tag line:</label>
                <input type="text" disabled value="$tagline">
            </div>
    </fieldset>

    <fieldset>
        <legend>Stock Information</legend>
            <div class="row">
                <label>Rental Period:</label>
                <select id="rental_period" name="rental_period">
END;
    // Print each rental period.
    $rental_periods = array("3 Day", "Overnight", "Weekly");

    foreach($rental_periods as $period) {
      $selected = ($period == $rental_period ? " selected" : "");
      print"<option value=\"$period\"$selected>$period</option>";
    }

    PRINT <<<END
                </select>
                <div class="required">*</div>
            </div>
            
            <fieldset>
                <legend>DVD:</legend>
                    <div class="row">
                        <label>Rental price:</label>
                        <input name="DVD_rental_price" class="num_input" type="text" value="$DVD_rental_price">
                        <div class="required">*</div>
                    </div>
                    <div class="row">
                        <label>Purchase price:</label>
                        <input name="DVD_purchase_price" class="num_input" type="text" value="$DVD_purchase_price">
                        <div class="required">*</div>
                    </div>
                    <div class="row">
                        <label>In-stock:</label>
                        <input name="numDVD" class="num_input" type="text" value="$numDVD">
                        <div class="required">*</div>
                    </div>
                    <div class="row">
                        <label>Currently Rented:</label>
                        <input name="numDVDout" class="num_input" type="text" value="$numDVDout">
                        <div class="required">* Overwrite only if a rental has failed to be returned.</div>
                    </div>
                    <div class="row">
                        <label>In Store:</label>
                        <input type="text" class="num_input" value="$numDVDinStore" disabled>
                    </div>
            </fieldset>
            
            <fieldset>
                <legend>BluRay:</legend>
                    <div class="row">
                        <label>Rental price:</label>
                        <input name="BluRay_rental_price" class="num_input" type="text" value="$BluRay_rental_price">
                        <div class="required">*</div>
                    </div>
                    <div class="row">
                        <label>Purchase price:</label>
                        <input name="BluRay_purchase_price" class="num_input" type="text" value="$BluRay_purchase_price">
                        <div class="required">*</div>
                    </div>
                    <div class="row">
                        <label>In-stock:</label>
                        <input name="numBluRay" class="num_input" type="text" value="$numBluRay">
                        <div class="required">*</div>
                    </div>
                    <div class="row">
                        <label>Currently Rented:</label>
                        <input name="numBluRayout" type="text" class="num_input" value="$numBluRayOut">
                        <div class="required">* Overwrite only if a rental has failed to be returned.</div>
                    </div>
                    <div class="row">
                        <label>In Store:</label>
                        <input type="text" class="num_input" value="$numBluRayinStore" disabled>
                    </div>
            </fieldset>
    </fieldset>
    </form>
    <div class="row">
      <input type="submit" form = "editMovieForm" value="Update Movie">
      <input type="submit" onclick="deleteMovie()" value="Delete Movie">
    </div>
END;
  }

  /**
   * Prints a success message informing the user that a movie has successfully been deleted.
   */
  public function deleteMovieSuccess()
  {
    $movie_id = $_POST['movie_id'];
    $title = $_POST['title'];

    print "<h2>Movie: $movie_id - $title deleted from system</h2>";
    print "<br><p style='text-align: center'>Image also removed from system.</p>";
  }

  /**
   * Prints a success message informing the user that a movie has successfully been registered.
   */
  public function createMovieSuccess()
  {
    $movie_id = $_POST['title'];
    print "<h2>Movie: $movie_id created successfully.</h2>";
  }


  /**
   * Prints a form that allows an administrator to register a new movie in the database.
   * @param $data An array containing names/ids of directors, studios, genres, actors.
   */
  public function createMovieForm($data)
  {
    print <<<END
    <p style="float: right;">* = Compulsory field</p>

<!-- Create movie form -->
<form id="createMovieForm" name="createMovieForm" onsubmit="createMovie()" enctype="multipart/form-data">
    <fieldset>
        <legend>New Movie</legend>
        <div class="row">
            <label>Title:</label>
            <input name="title" type="text" required>
            <div class="required">*</div>
        </div>
        <div class="row">
            <label>Year:</label>
            <input name="year" type="text" minlength="4" maxlength="4" size="4" min="1898" max="2019" required>
            <div class="required">*</div>
        </div>
        <div class="row">
            <label>Tag line:</label>
            <input name="tagline" type="text" maxlength="128" size="64" required onchange="checkIfExists(this.name, this.value)">
            <div class="required">*</div>
            <div id="tagline_available" class="errorBox"></div>
        </div>
        <div class="row">
            <label>Plot:</label>
            <textarea name="plot" rows="4" maxlength="256" required onchange="checkIfExists(this.name, this.value)"></textarea>
            <div class="required">*</div>
            <div id="plot_available" class="errorBox"></div>
        </div>
        <div class="row">
            <label>Poster:</label>
            <input id="poster" name="poster" type="file" required onchange="validatePoster()">
            <div class="required">*</div>
            <div id="poster_error" class="errorBox"></div>
            <input hidden id="poster_valid" value="true">
        </div>
        <div class="row">
            <label>Director:</label>
            <select name="director" id="director_select">
END;
    $this->printSelectOptions($data['directors']);
      print <<<END
            </select>
            <div class="required">*</div>
        </div>
        <div class="row">
            <label>Or new director:</label>
            <input name="director" type="text" onchange="checkIfExists(this.name, this.value)">
            <div id="director_available" class="errorBox"></div>
        </div>
        <div class="row">
            <label>Studio:</label>
            <select name="studio" id="studio_select">
END;
    $this->printSelectOptions($data['studios']);
    print <<<END
            </select>
            <div class="required">*</div>
        </div>
        <div class="row">
            <label>Or new studio:</label>
            <input name="studio" type="text" onchange="checkIfExists(this.name, this.value)">
            <div id="studio_available" class="errorBox"></div>
        </div>
        <div class="row">
            <label>Genre:</label>
            <select name="genre" id="genre_select">
END;
    $this->printSelectOptions($data['genres']);
    print <<<END
            </select>
            <div class="required">*</div>
        </div>
        <div class="row">
            <label>Or new genre:</label>
            <input name="genre" type="text" onchange="checkIfExists(this.name, this.value)">
            <div id="genre_available" class="errorBox"></div>
        </div>
        <div class="row">
            <label>Classification:</label>
            <select name="classification" id="classification_select">
                <option value="">[blank]</option>
                <option value="G">G</option>
                <option value="PG">PG</option>
                <option value="M">M</option>
                <option value="MA">MA</option>
                <option value="R">R</option>
            </select>
            <div class="required">*</div>
        </div>
        <div class="row">
            <label>Or new classification:</label>
            <input name="classification" type="text" onchange="checkIfExists(this.name, this.value)">
            <div id="classification_available" class="errorBox"></div>
        </div>
    </fieldset>

    <fieldset>
        <legend>Movies Stars and Co-stars</legend>
        <div class="row">
            <label>First star:</label>
            <select name="star1" class="actor" id="star1_select">
END;
    $this->printSelectOptions($data['actors']);
    print <<<END
            </select>
            <div class="required">*</div>
        </div>
        <div class="row">
            <label>Or new star1:</label>
            <input name="star1" class="actor" type="text" onchange="checkIfExists(this.name, this.value)">
            <div id="star1_available" class="errorBox"></div>
        </div>
        <div class="row">
            <label>Second star:</label>
            <select name="star2" class="actor" id="star2_select">
END;
    $this->printSelectOptions($data['actors']);
    print <<<END
            </select>
            <div class="required">*</div>
        </div>
        <div class="row">
            <label>Or new star2:</label>
            <input name="star2" class="actor" type="text" onchange="checkIfExists(this.name, this.value)">
            <div id="star2_available" class="errorBox"></div>
        </div>
        <div class="row">
            <label>Third star:</label>
            <select name="star3" class="actor" id="star3_select">
END;
    $this->printSelectOptions($data['actors']);
    print <<<END
            </select>
        </div>
        <div class="row">
            <label>Or new star3:</label>
            <input name="star3" class="actor" type="text" onchange="checkIfExists(this.name, this.value)">
            <div id="star3_available" class="errorBox"></div>
        </div>
        <div class="row">
            <label>First costar:</label>
            <select name="costar1" class="actor" id="costar1_select">
END;
    $this->printSelectOptions($data['actors']);
    print <<<END
            </select>
        </div>
        <div class="row">
            <label>Or new costar1:</label>
            <input name="costar1" class="actor" type="text" onchange="checkIfExists(this.name, this.value)">
            <div id="costar1_available" class="errorBox"></div>
        </div>
        <div class="row">
            <label>Second costar:</label>
            <select name="costar2" class="actor" id="costar2_select">
END;
    $this->printSelectOptions($data['actors']);
    print <<<END
            </select>
        </div>
        <div class="row">
            <label>Or new costar2:</label>
            <input name="costar2" class="actor" type="text" onchange="checkIfExists(this.name, this.value)">
            <div id="costar2_available" class="errorBox"></div>
        </div>
        <div class="row">
            <label>Third costar:</label>
            <select name="costar3" class="actor" id="costar3_select">
END;
    $this->printSelectOptions($data['actors']);
    print <<<END
            </select>
        </div>
        <div class="row">
            <label>Or new costar3:</label>
            <input name="costar3" class="actor" type="text" onchange="checkIfExists(this.name, this.value)">
            <div id="costar3_available" class="errorBox"></div>
        </div>
    </fieldset>

    <fieldset>
        <legend>Stock Information:</legend>
        <div class="row">
            <label>Rental Period:</label>
            <select name="rental_period" required>
                <option disabled="" selected="" value="">Select...</option>
                <option value="3 Day">3 Day</option>
                <option value="Overnight">Overnight</option>
                <option value="Weekly">Weekly</option>
            </select>
            <div class="required">*</div>
        </div>

END;
    // Print a price/stock info input fieldset for each medium (DVD/BluRay)

    $mediums = array("DVD", "BluRay");
    foreach ($mediums as $medium) {
      print <<<END
      <fieldset>
          <legend>$medium:</legend>
          <div class="row">
              <label>Rental price:</label>
              <input name="{$medium}_rental_price" type="text" required>
              <div class="required">*</div>
          </div>
          <div class="row">
              <label>Purchase price:</label>
              <input name="{$medium}_purchase_price" type="text" required>
              <div class="required">*</div>
          </div>
          <div class="row">
              <label>In-stock:</label>
              <input name="num{$medium}" type="text" required>
              <div class="required">*</div>
          </div>
          <div class="row">
              <label>Rented:</label>
              <input name="num{$medium}out" type="text" value="0" required>
              <div class="required">* Overwrite only if some rented before system entry</div>
          </div>
      </fieldset>
END;
    }

    print <<<END
    </fieldset>
</form>

<div class="row">
    <input type="submit" form="createMovieForm" value="Submit Query">
    <input type="reset" form="createMovieForm" value="Reset">
</div>
END;
  }

  /**
   * Prints a tabular report of the stock of each movie in the database.-
   */
  public function showMovieStockReport($stockData)
  {
    print <<<END
    <table id="stockReportTable">
      <tr>
        <th rowspan="2"><b>Movie/ID</b></th>
        <th colspan="3"><b>DVD</b></th>
        <th colspan="3"><b>BluRay</b></th>
      </tr>
      <tr>
        <!-- DVD -->
        <th class="tbl_subheading"><i>In stock</i></th>
        <th class="tbl_subheading"><i>Available</i></th>
        <th><i>Rented</i></th>
        
        <!-- BluRay -->
        <th class="tbl_subheading"><i>In stock</i></th>
        <th class="tbl_subheading"><i>Available</i></th>
        <th class="tbl_subheading"><i>Rented</i></th>
      </tr>
END;

    foreach($stockData as $datum)
    {
      $title = $datum['title'];
      $movie_id = $datum['movie_id'];
      $dvdAvailable = $datum['numDVD'];
      $dvdRented = $datum['numDVDout'];
      $dvdTotal = $dvdAvailable + $dvdRented;
      $blurayAvailable = $datum['numBluRay'];
      $blurayRented = $datum['numBluRayOut'];
      $blurayTotal = $blurayAvailable + $blurayRented;

      print <<<END
      <tr>
        <td><b>$title - $movie_id</b></td>
        <td>$dvdTotal</td>
        <td>$dvdAvailable</td>
        <td>$dvdRented</td>
        <td>$blurayTotal</td>
        <td>$blurayAvailable</td>
        <td>$blurayRented</td>
      </tr>
END;
    }

    print "</table>";
  }

  /**
   * Prints select options given an array of data. This array must contain a value for 'id' and 'name'.
   * @param $data An array containing an "id" and a "name" value, formatted exactly like that.
   */
  private function printSelectOptions($data)
  {
    print "<option value=''>[blank]</option>";

    foreach($data as $item) {
      print "<option value='".$item['id']."'>".$item['name']."</option>";
    }
  }
}
?>