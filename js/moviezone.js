/**
 * New releases are shown when the MovieZone page is initially loaded.
 */
window.addEventListener('load', function(){
  movieShowNewReleasesClick();
});

/**
 * Updates the 'mainContent' <div> with whatever HTML data is passed as a parameter.
 * Movies are intended to be displayed in the 'mainContent' <div>.
 * @param data The HTML data to be displayed.
 */
function updateMainContent(data) {
	document.getElementById('mainContent').innerHTML = data;
}

/**
 * Updates the 'topNav' <div> with whatever HTML data is passed as a parameter.
 * 'topNav' is intended to house the <select>/<option> elements that are used to filter movies.
 * @param data The HTML data to be displayed.
 */
function updateTopNav(data) {
  var topNav = document.getElementById('topNav');
  topNav.innerHTML = data;
  topNav.style.display = "inherit";
}

/**
 * Updates the contents of whatever is within the #leftPanel <div>.
 * @param data The data which is used to replace
 */
function updateLeftPanel(data) {
  document.getElementById('leftPanel').innerHTML = data;
}

/**
 * Sends a command to the request handler to DISPLAY a filter for a discriminator.
 * Current valid discriminators: actor, director, genre, classification
 * @param filterType The type of <select> filter that will be constructed.
 */
function movieFilterClick(filterType) {
  const FILTER_TYPES = ['actor', 'director', 'genre', 'classification'];

  filterType = filterType.toLowerCase();  // Ensures correct formatting.

  setSelectHeading('Select ' + filterType);   // Change the "select" heading e.g. 'Select actor', 'Select genre'

  // Remove all movies currently displayed.
  var movies = document.getElementById('mainContent');
  while (movies.firstChild) {
    movies.removeChild(movies.firstChild);
  }

  if (FILTER_TYPES.includes(filterType)) {
    makeAjaxGetRequest('moviezone_main.php', 'cmd_show_filter_' + filterType, null, updateTopNav);
  } else {
    // If an invalid filter type is passed as an argument, then display all movies.
    console.error("Error: invalid filter type - please use one of the following instead:", ...FILTER_TYPES);
    console.log("Displaying all movies...");
    movieShowAllClick();
  }
}

/**
 * Makes a <select> element on the page disappear.
 * Useful when one is not required (such as show all movies/new releases).
 */
function makeSelectInvisible() {
// Check if the select element exists, and if so, disable its display.
  if (document.getElementById('select')) {
    document.getElementById('select').style.display = 'none';
  }
}

/**
 * Displays all movies in the #mainContent <div>.
 */
function movieShowAllClick() {
  makeSelectInvisible();
  setSelectHeading('All Movies');
  makeAjaxGetRequest('moviezone_main.php', 'cmd_show_all_movies', null, updateMainContent);
}

/**
 * Displays all new releases (the number of which is defined in moviezone_config.php) in the #mainContent <div>.
 */
function movieShowNewReleasesClick() {
  makeSelectInvisible();
  setSelectHeading('New Releases');
  makeAjaxGetRequest('moviezone_main.php', 'cmd_show_new_releases', null, updateMainContent);
}

/**
 * The function is triggered when a the value of a <select> element has changed.
 * Looks for the value within a <select> element and filters movies by it (via a sending a AJAX GET request).
 */
function selectFilterChanged() {
  // Define names of <select> elements - IDs must match, otherwise an error will be thrown.
  var actor = document.getElementById('actorSelect');
  var director = document.getElementById('directorSelect');
  var genre = document.getElementById('genreSelect');
  var classification = document.getElementById('classificationSelect');

  // Check to see if an element exists (only 1 should exist at a time)
  if (actor) {
    param = '&actor_id=' + actor.value;
  }
  else if (director) {
    param = '&director_id=' + director.value;
  }
  else if (genre) {
    param = '&genre_id=' + genre.value;
  }
  else if (classification) {
    param = '&classification=' + classification.value;
  }
  else {
    console.error("Error: could not get value of <select> element (does it exist?)");
    return;
  }

  // Update the #mainContent div with whatever's printed as a result of the Ajax request.
  makeAjaxGetRequest('moviezone_main.php', 'cmd_filter_movies', param, updateMainContent);
}

/**
 * Renames the text within the select heading on the page.
 * @param text The text the select heading will be replaced with.
 */
function setSelectHeading(text) {
  try {
    document.getElementById('selectHeading').innerHTML = text;
    } catch (e) {
    console.error("Error: cannot find heading with ID #selectHeading - text will not be displayed");
  }
}

/**
 * Adds a movie to the user's cart (if less than 5 movies) and updates the movie's button to "Already Selected".
 * @param movie_id The movie to be added to the user's cart.
 */
function addToCart(movie_id) {
  let selectedText = 'Already Selected';

  let btn = 'btn_'+movie_id;
  if (document.getElementById(btn).value != selectedText) {
    param = '&movie_id=' + movie_id;
    makeAjaxPostRequest('moviezone_main.php', 'cmd_add_to_cart', param, setCartText);
    document.getElementById(btn).value = selectedText;
  }
}

/**
 * Sets the cart status text - e.g. '4 movies selected'.
 * This function also redirects the user to the checkout page if they reach the max. number of movies in cart (5).
 * @param num_movies The amount of movies in the user's cart.
 */
function setCartText(num_movies) {
  if (num_movies==5) {
    // User has reached the max amount of movies, redirect them to checkout.
    window.location.replace('checkout.php');
  } else {
    // Update the cart text to display the number of movies selected.
    document.getElementById('movies_in_cart_box').innerHTML = num_movies;
  }
}

function createMember() {
  event.preventDefault();
  let formData = new FormData(document.createMemberForm);

  if (createMemberForm.magazine.checked) {
    formData.set('magazine', 1);
  } else {
    formData.set('magazine', 0);
  }

  if (validate_member('create', formData)) {
    makeAjaxPostRequest('moviezone_main.php', 'cmd_create_member', formData, updateMainContent)
  }
}
