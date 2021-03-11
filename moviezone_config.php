<?php
/**  DB connection details **/
define ('DB_CONNECTION_STRING', "mysql:host=localhost;dbname=mbowle16_moviezone_db");
define ('DB_USER', "mbowle16");
define ('DB_PASS', "18032000");
define ('MSG_ERR_CONNECTION', "Open connection to the database first");

/** Define constants to be used in application **/
define ('_MOVIE_PHOTO_FOLDER_', 'img/posters/');    // Directory where movie thumbnails/posters are stored.
define ('_HTML_TEMPLATE_FOLDER_', 'html_templates/');
define ('_MAX_NEW_RELEASES_', 9);                       // The max. amount of new releases to be selected (MAX 10).
define ('_MAX_POSTER_FILE_SIZE_', 1048576*2);         // 2mb. May be too large.

// Classes required for the MVC design pattern (including the database adapter)

// Used by guests/members
require_once('database_adapters/moviezone_dba.php');
require_once('models/moviezone_model.php');
require_once('views/moviezone_view.php');
require_once('controllers/moviezone_controller.php');

// Used by admins
require_once('database_adapters/admin_moviezone_dba.php');
require_once('models/admin_moviezone_model.php');
require_once('views/admin_moviezone_view.php');
require_once('controllers/admin_moviezone_controller.php');


/**************************************
 *         Request commands
 **************************************/

// Define request command messages for client-server communication using AJAX.
define ('CMD_REQUEST', 'request');

/** Filter by commands */

// Accessible to guests, members, administrators.
define ('CMD_FILTER_MOVIES', 'cmd_filter_movies');

/** Show movies commands **/

// Accessible to guests/members/administrators.
define ('CMD_SHOW_ALL_MOVIES', 'cmd_show_all_movies');
define ('CMD_SHOW_NEW_RELEASES', 'cmd_show_new_releases');

/** Display/show <select>/<option> elements commands **/

// Accessible to guests/members/administrators.
define ('CMD_SHOW_FILTER_ACTOR', 'cmd_show_filter_actor');
define ('CMD_SHOW_FILTER_DIRECTOR', 'cmd_show_filter_director');
define ('CMD_SHOW_FILTER_GENRE', 'cmd_show_filter_genre');
define ('CMD_SHOW_FILTER_CLASSIFICATION', 'cmd_show_filter_classification');


/**  Create commands **/

// Accessible to administrators.
define ('CMD_CREATE_MOVIE', 'cmd_create_movie');

// Accessible to guests/members/administrators.
define ('CMD_CREATE_MEMBER', 'cmd_create_member');

/**  Update commands **/

// Accessible to administrators.
define ('CMD_EDIT_MOVIE', 'cmd_edit_movie');
define ('CMD_EDIT_MEMBER', 'cmd_edit_member');

/**  Delete commands **/

// Accessible to administrators.
define ('CMD_DELETE_MOVIE', 'cmd_delete_movie');
define ('CMD_DELETE_MEMBER', 'cmd_delete_member');

/**  Login commands **/

// Accessible to members
define ('CMD_LOGIN_MEMBER', 'cmd_login_member');

// Accessible to administrators.
define ('CMD_LOGIN_ADMIN', 'cmd_login_admin');
define ('CMD_LOGOUT_ADMIN', 'cmd_logout_admin');

/** Checkout/add to cart commands  **/

// Accessible to members.
define ('CMD_ADD_TO_CART', 'cmd_add_to_cart');


/** Show form commands **/

// Accessible to administrators.
define ('CMD_SHOW_EDIT_DELETE_MEMBER', 'cmd_show_edit_delete_member');
define ('CMD_SHOW_EDIT_DELETE_MOVIE', 'cmd_show_edit_delete_movie');
define ('CMD_SHOW_CREATE_MOVIE', 'cmd_show_create_movie');


/** Misc. commands */

// Accessible to administrators.
define ('CMD_CHECK_IF_EXISTS', 'cmd_check_if_exists');
define ('CMD_SHOW_DETAILS_MEMBER', 'cmd_show_details_member');
define ('CMD_SHOW_DETAILS_MOVIE', 'cmd_show_details_movie');
define ('CMD_VALIDATE_POSTER', 'cmd_validate_poster');
define ('CMD_SHOW_MOVIE_STOCK_REPORT', 'cmd_show_movie_stock_report');

?>