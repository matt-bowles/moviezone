/**
 * Logouts an admin account.
 */
function logout() {
  makeAjaxPostRequest('admin_main.php', 'cmd_logout_admin', null, null);
}

/**
 * Attempts a login attempt for admin.
 * If login succeeds: admin page is refreshed and user is granted access to control panel functions.
 * If login fails: error text is shown.
 */
function adminLogin() {
  event.preventDefault();

  formData = document.adminLoginForm;

  params = "&username="+formData.username.value;
  params += "&password="+formData.password.value;

  makeAjaxPostRequest('admin_main.php', 'cmd_login_admin', params, function(response) {
    if (response == 'true') {
      // Login passed, refresh page.
      location.reload();
    } else {
      // Login failed, show error.
      document.getElementById('admin_errorbox').innerHTML = "Login failed. Username or password incorrect.";
    }
  });
}

/**
 * Loads and populates the edit/delete member form with pre-existing information.
 */
function memberEditDeleteClick() {
  loadAdminBackButton();
  setAdminHeading("Edit/Delete Member");
  makeAjaxGetRequest('admin_main.php', 'cmd_show_edit_delete_member', null, updateMainContent);
}

/**
 * Loads the create movie form.
 */
function movieCreateClick() {
  loadAdminBackButton();
  setAdminHeading("Create new movie");
  makeAjaxGetRequest('admin_main.php', 'cmd_show_create_movie', null, updateMainContent);

  // Wait for content to load, then display tooltips.
  setTimeout(loadToolTips, 500);
}

/**
 * Loads and populates the edit/delete movie form with pre-existing information.
 */
function movieEditDeleteClick() {
  loadAdminBackButton();
  setAdminHeading("Edit/Delete movie");
  makeAjaxGetRequest('admin_main.php', 'cmd_show_edit_delete_movie', null, updateMainContent);
}

/**
 * Loads the stock report containing information for all movies in the database.
 */
function movieStockReportClick() {
  loadAdminBackButton();
  setAdminHeading('Movie Stock Report');
  makeAjaxGetRequest('admin_main.php', 'cmd_show_movie_stock_report', null, updateMainContent);
}

/**
 * Gets all details (contained within movie_detail_view) for a particular movie_id, and prints them to the screen - ready for editing/deleting.
 */
function getMovieDetails() {
  let movie_id = document.getElementById('movie_select').value;
  if (movie_id != "") {
    params = '&movie_id=' + movie_id;
    makeAjaxGetRequest('admin_main.php', 'cmd_show_details_movie', params, updateMainContent);

    // Wait for content to load, then display tooltips.
    setTimeout(loadToolTips, 500);
  }
}

/**
 * Gets all details for a particular member_id, and prints them to the screen - ready for editing/deleting.
 */
function getMemberDetails() {
  let member_id = document.getElementById('member_select').value;
  if (member_id != "") {
    params = '&member_id=' + member_id;
    makeAjaxGetRequest('admin_main.php', 'cmd_show_details_member', params, updateMainContent);

    // Wait for content to load, then display tooltips.
    setTimeout(loadToolTips, 500);
  }
}

/**
 * Deletes a member from the database. The user is prompted to confirm this decision.
 */
function deleteMember() {
  let member_id = document.editMemberForm.member_id.value;

  params = "&member_id="+member_id;
  params += "&surname="+document.editMemberForm.surname.value;
  params += "&other_name="+document.editMemberForm.other_name.value;
  params += "&username="+document.editMemberForm.username.value;

  if (confirm("Are you sure to delete member #"+member_id+"?")) {
    makeAjaxPostRequest('admin_main.php', 'cmd_delete_member', params, updateMainContent);
  }
}

/**
 * Registers a new entry for a movie using the data provided in the createMovieForm.
 */
function createMovie() {
  event.preventDefault();

  var formData = new FormData(document.createMovieForm);
  formData.append('rental_period', document.createMovieForm.rental_period.value);
  var selects = document.getElementsByTagName('select');

  // If input is provided instead of using a drop-down <select> list, then overwrite it.
  for (var select of selects) {
    if (select.value != "") {
      formData.set(select.name, select.value);
    }
  }

  if (validate_movie('create', formData)) {
    makeAjaxPostRequest('admin_main.php', 'cmd_create_movie', formData, updateMainContent)
  }
}

/**
 * Deletes a movie from the database. The user is prompted to confirm this decision.
 */
function deleteMovie() {
  params = "&movie_id="+document.editMovieForm.movie_id.value;
  params += "&title="+document.editMovieForm.title.value;
  params += "&thumbpath="+document.editMovieForm.thumbpath.value;

  if (confirm("Are you sure to delete "+document.editMovieForm.title.value+"?")) {
    makeAjaxPostRequest('admin_main.php', 'cmd_delete_movie', params, updateMainContent);
  }
}

/**
 * Updates the details of a movie in the database using the information provided in the editMovieForm.
 */
function editMovie() {
  event.preventDefault();
  let formData = new FormData(document.editMovieForm);

  // Remove thumbpath from formdata as its not needed for editing a movie.
  formData.delete('thumbpath');

  if (validate_movie('edit', formData)) {
    makeAjaxPostRequest('admin_main.php', 'cmd_edit_movie', formData, updateMainContent);
  }
}

/**
 * Renames the text within the select heading on the page.
 * @param text The text the select heading will be replaced with.
 */
function setAdminHeading(text) {
  try {
    document.getElementById('adminHeading').innerHTML = text;
  } catch (e) {
    console.error("Error: cannot find heading with ID #selectHeading - text will not be displayed");
  }
}

/**
 * Loads a button that simply redirects the user back to the main page of the admin control panel.
 */
function loadAdminBackButton() {
    document.getElementById('admin_links').innerHTML =
      "<li><a href='admin.php'>Display Menu</a></li>";
}

/**
 * Determines whether the poster is suitable for the system, and if any errors occurred while uploading.
 * Feedback is printed to the user if something goes wrong.
 */
function validatePoster() {
  formData = new FormData(document.createMovieForm);

  makeAjaxPostRequest('admin_main.php','cmd_validate_poster', formData, function(data) {
    if (data != "") {
      document.getElementById('poster_error').innerHTML = data;
      document.getElementById('poster_valid').setAttribute('value', false);
    } else {
      // Clear error msg
      document.getElementById('poster_error').innerHTML = "";
      document.getElementById('poster_valid').setAttribute('value', true);
    }
  });
}

function editMember() {
  event.preventDefault();
  let formData = new FormData(document.editMemberForm);

  if (editMemberForm.magazine.checked) {
    formData.set('magazine', 1);
  } else {
    formData.set('magazine', 0);
  }

  if (validate_member('edit', formData)) {
    makeAjaxPostRequest('admin_main.php', 'cmd_edit_member', formData, updateMainContent);
  }
}

/**
 * Updates the 'mainContent' <div> with whatever HTML data is passed as a parameter.
 * Movies are intended to be displayed in the 'mainContent' <div>.
 * @param data The HTML data to be displayed.
 */
function updateMainContent(data) {
  document.getElementById('mainContent').innerHTML = data;
}