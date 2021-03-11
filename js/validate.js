var isAvailable = ['username', 'tagline', 'plot', 'director', 'studio', 'genre', 'classification',
  'star1', 'star2', 'star3', 'costar1', 'costar2', 'costar3'];

// Initalise each value in the isAvailable map
isAvailable.forEach(function (type) {
  isAvailable[type] = true;
});

/**
 * Loads tool tips for input elements (where formatting instructions for the element exists)
 */
function loadToolTips() {
  // Inputs are located within rows.
  let rows = document.getElementsByClassName("row");

  for (var row of rows) {
    // Only display tooltips for input elements.
    input = row.querySelector('input, textarea');
    if (input != null) {
      let name = input.name;
      let format = getFormattingInstructions(name);

      if (format != null) {
        // Create the div containing the tooltip.
        var tooltipDiv = document.createElement("div");

        // Set the text of the tooltip.
        var tooltipTxt = document.createTextNode(format);

        //Set the class name of the tooltip.
        tooltipDiv.className = "hint";

        // Add the text to the div and append it as a child to the row.
        tooltipDiv.appendChild(tooltipTxt);
        row.appendChild(tooltipDiv);
      }
    }
  }
}

function translateField(field) {
  // e.g. star1, costar1, director
  if (field.includes('star') || field == 'director') {
    field = 'name';
  }
  // e.g. rental/purchase price
  else if (field.includes('price')) {
    field = 'price';
  }
  // e.g. in-stock/rented
  else if (field.includes('num')) {
    field = 'num'
  }
  return field;
}

/**
 * Contains all regexes for validating.
 * If the regex for the parameter doesn't exist, 'undefined' is returned.
 * @param field The type of field which the user wishes to receieve the regular expression for.
 * @returns The regex associated with the parameter. May return undefined.
 */
function getRegex(field) {

  /** Translate field names so that a single regex can be applied to multiple inputs **/
  field = translateField(field);

  let regexes = {
    // Member account regexes
    surname:    /^([A-Z]')?[A-Z][a-z]+(-[A-Z][a-z]+)*$/,
    other_name:     /^[A-Z][a-z]+(-([A-Z][a-z]+)+)?$/,
    mobile:  /^0[45][0-9]{2}\s[0-9]{3}\s[0-9]{3}$/,
    landline:     /^\(0[236789]\)\s[0-9]{8}$/,
    email:     /^[A-z0-9.-_]+@[a-z]+(\.[a-z]{1,13})+$/,
    street:   /^[A-Z0-9][A-z0-9.]*\s([A-Z][a-z'.-]+\s)+[A-Z][a-z]+$/,
    suburb:    /^([A-Z][a-z]+\s?)+,\s[A-Z]{2,3}$/,
    postcode: /^[0-9]{4}$/,
    username:    /^[A-z0-9_]{6,10}$/,
    password:      /^(?=.*[a-z])(?=.*[A-Z])(?=.*[\d])(?=.*[`~!@#$%^&*\-_=+\[\{\]\};:'",<.>\/?]).*$/,

    // Movie details regexes
    title: /^[A-Z0-9][a-zA-Z0-9-.,()!?'"&#:;\s]{1,45}$/,
    year: /^(19\d\d|20[0-1]\d)$/,
    tagline: /^[A-Z][a-zA-Z0-9-.,()!?'"&#:;\s]{1,128}$/,
    plot: /^[A-Z][a-zA-Z0-9-.,()!?'"&#:;\s]{1,256}$/,
    name: /^[A-Z][a-z.]+(\s([A-Z]')?[A-Z][a-z.]+(-[A-Z][a-z]+)*)+$/,
    studio: /^[A-Z0-9][A-z0-9.'\s-]{2,128}$/,
    genre: /^[A-Z0-9][a-zA-Z0-9-'\s]{1,128}$/,
    classification: /^[A-Z]{0,2}$/,
    price: /^[0-9]{1,2}\.[0-9]{2}$/,
    num: /^[0-9]{1,3}$/
  };

  return regexes[field];
}

/**
 * Contains all formatting instructions for validating.
 * If formatting instructions for the parameter doesn't exist, 'undefined' is returned.
 * @param field The type of field which the user wishes to receieve formatting instructions for.
 * @returns The formatting instructions associated with the parameter. May return undefined.
 */
function getFormattingInstructions(field) {

  /** Translate field names so that a single regex can be applied to multiple inputs **/
  field = translateField(field);

  let formatting_instructions = {
    // Member account formatting instructions
    surname: "Surname must begin with a capital and may be hyphenated and use apostrophes.",
    other_name: "First name(s) must begin with a capital and may be hyphenated.",
    mobile: "Mobile number must be formatted as '0XDD DDD DDD' where X is 4 or 5 and D is any numeric digit.",
    landline: "Landline number must be formatted as '(0X) DDDDDDDD where X is 2/3/6/7/8/9 and D is any numberic digit.",
    email: "Email can contain as many subdomains as needed.",
    street: "Address must begin with an integer or capital.",
    suburb: "Suburb/state must begin with a capital and be formatted as 'suburb, abbreviated state' where the state is typed in ALL CAPS and separated from the suburb via a comma.",
    postcode: "Postcode must consist of 4 digits.",
    username: "Username must consist of alphanumeric characters between 6 and 10 characters in length.",
    password: "Password must consist of maximum 10 characters an include at least one (1) uppercase letter, lowercase letter, number, and special character. No white space is permitted.",

    // Movie details formatting instructions
    title: "Title must begin with a capital/decimal and be between 2 and 45 characters long.",
    year: "Year must be within the range of 1900-2019.",
    tagline: "Tagline must begin with a capital and be between 2 and 128 characters long.",
    plot: "Plot must begin with a capital and be between 2 and 256 characters long.",
    name: "Actors and directors must begin with a capital letter, surname may be hyphenated and use apostrophes.",
    studio: "Studio must begin with a capital or decimal, and be between 3 and 128 characters long.",
    genre: "Genre must begin with a capital or decimal, and be between 2 and 128 characters long.",
    classification: "Classification must consist of 1-2 capital letters.",
    price: "Purchase/rental prices must be formatted as 'DD.DD' where D represents a single digit.",
    num: "Stock information must be an integer ranging from 0 to 999."
  };

  return formatting_instructions[field];
}


/**
 * Validates whether the information entered in a form for editing/creating a member account is correct.
 * @param operation_type Must either be 'create' or 'edit'.
 * @param formData A formData object that contains all the information retrieved from a form.
 * @returns True if the data passed all validations, otherwise false is returned and an appropriate error is printed.
 */
function validate_member(operation_type, formData) {

  // Define supported operation types.
  let operation_types = ['create', 'edit'];

  // Check if operation type specified is supported.
  if (!operation_types.includes(operation_type)) {
    console.error("Invalid operation_type passed as parameter. Please use one of the following:");
    console.error(operation_types);
    return false;
  }

  // Check if the username provided is already registered.
  if (operation_type == 'create') {
    if (isAvailable['username'] == false) {
      alert("Username is already taken");
      return false;
    }
  }

  // Do both passwords match?
  if (operation_type == 'create') {
    let password = document.getElementById('password').value;
    let verify_password = document.getElementById('verifyPassword').value;

    if (password != verify_password) {
      alert("Passwords don't match.");
      return false;
    }
  }

  // Check each field in the form data against its regex (if defined).
  for (let [field, value] of formData.entries()) {
    field = field.toLowerCase();
    if (!validateAgainstRegex(field, value)) {
      return false;
    }
  }

  // All tests have been passed.
  return true;
}

/**
 * Validates whether the information entered in a form for editing/creating a movie is correct.
 * @param operation_type Must either be 'create' or 'edit'.
 * @param formData A formData object that contains all the information retrieved from a form.
 * @returns True if the data passed all validations, otherwise false is returned and an appropriate error is printed.
 */
function validate_movie(operation_type, formData) {
  let operation_types = ['create', 'edit'];   // Define supported operation types.

  operation_type = operation_type.toLowerCase();

  // Check if operation type specified is supported.
  if (!operation_types.includes(operation_type)) {
    console.error("Invalid operation_type passed as parameter. Please use one of the following:");
    console.error(operation_types);
    return false;
  }

  // Check that image has been successfully validated.
  if (operation_type == 'create') {
    if (document.getElementById('poster_valid').value != "true") {
      alert("Please upload another image.");
      return false;
    }
  }

  // Check that either an existing value for a field is selected, or a new value is added.
  // ...but not both.
  if (operation_type == 'create') {

    // i.e. Fields that offer the user to select or provide their own input.
    let input_fields = [
      'director', 'studio', 'genre', 'classification',
      'star1', 'star2', 'star3',
      'costar1', 'costar2', 'costar3'
    ];

    // i.e. Fields that are optional for the user to fill out when creating a movie.
    const optional_fields = [
      'star3',
      'costar1', 'costar2', 'costar3'
    ];

    // Check that a value for a common field (e.g. actor, director) isn't entered twice.
    for (var field of input_fields) {

      i = 0;  // Initalise counter

      // Increment by one where a common field contains a value.
      for (var field of document.getElementsByName(field)) {
        if (field.value != "") {
          i++;
        }
      }

      // Both fields shouldn't be empty.
      if (i == 0 && (!optional_fields.includes(field.name))) {
        alert("You must select one " + field.name);
        return false;
      }

      // Both fields shouldn't be filled out.
      else if (i == 2) {
        alert("You can't select more than one " + field.name);
        return false;
      }
    }
  }

  // Check that an actor appears in at most, one role.
  if (operation_type == 'create') {

    // Get all actor inputs (including selects)
    let stars = document.getElementsByClassName('actor');
    let actor_names = [];

    for(var actor of stars) {
      let name = actor.value;

      // Check that the input is filled, else skip the iteration.
      if (name != "") {

        // If the actor isn't already within actor_names; add it, else return false.
        if (!actor_names.includes(name)) {
          actor_names.push(name);
        } else {
          alert("An actor may not star in multiple roles.");
          return false;
        }
      }
    }
  }

  // Check that all data is unique and doesn't already exist in the database.
  if (operation_type == 'create') {
    for (var item of isAvailable)
      if (!isAvailable[item]) {
        alert(item+" already exists. Please enter a new value or select an existing one.");
        return false;
      }
  }

  for (let [field, value] of formData.entries()) {
    field = field.toLowerCase();
    elements = document.getElementsByName(field);

    // Field may have both a select/text input element.
    if (elements.length == 2) {
      let select = elements[0].value;
      value = elements[1].value;

      // If the select element is used (instead of inseting a custom value), then skip this iteration.
      if (select != "" || value == "") {
        continue;
      }
    }

    // Check field/value against its defined regex.
    if (!validateAgainstRegex(field, value)) {
      return false;
    }
  }

  // All validation checks have been passed.
  return true;

}

/**
 * Attempts to test a value against the defined regular expression for its field (if it exists).
 * @param field The field the value belongs to.
 * @param value The value to be tested.
 * @returns True if test was successful (or regex doesn't exist for field), other false if failed.
 */
function validateAgainstRegex(field, value) {
  if (getRegex(field) != undefined) {
    if (!getRegex(field).test(value) && value != "") {
      alert(getFormattingInstructions(field));
      return false;
    } else {
      // Value successfully validated against field's regex, or value is empty.
      return true;
    }
  } else {
    // Regex for field doesn't exist, return true.
    return true;
  }
}

/**
 * Checks whether a value for a field already exists in the database.
 * @param field e.g. username, landline, email, address.
 * @param val A value for the field.
 * @param handler The handler which will be sent a request (admin by default, any other value for guest/member).
 */
function checkIfExists(field, val, handler='admin') {
  // As most calls to this function will be for an admin form, the handler will be set to 'handler' by default.

  let request_handler;
  let param = '&type='+field.toLowerCase();
  param += '&value='+val;

  // Determine which handler the Ajax request will be sent to.
  if (handler == 'admin') {
    request_handler = 'admin_main.php';
  } else {
    request_handler = 'moviezone_main.php';
  }

  makeAjaxGetRequest(request_handler, 'cmd_check_if_exists', param, function(response) {
    // Response will either be true or false, and is printed by PHP.

    // A div that can hold text to alert the user that the value for the field already exists.
    // This element must have an ID of "[fieldname]_available".
    let availability_element = document.getElementById(field+"_available");

    // Value for field is already in use.
    if (response == true) {
      availability_element.innerHTML = field.charAt(0).toUpperCase()+field.slice(1)+" is already in use.";
      isAvailable[field] = false;
    }

    // Value is available.
    else {
      availability_element.innerHTML = "";    // Essentially hides it, without needing to make it "hidden".
      isAvailable[field] = true;
    }
  });
}

/**
 * Triggered upon the changing of preferred contact method.
 * Sets the "new preferred contact method" as required and displays the required element (*).
 * @param id The HTML ID of the radio button selected.
 */
function change_contactMethod(id) {

  // Get contact inputs - mobile, landline, email.
  let inputs = document.getElementsByClassName('contact_inputs');

  for (let input of inputs) {
    // The ID of the required element for the input (i.e. "*").
    let required_element = document.getElementById("required_"+input.id);

    // Toggle required attributes and required element (*).
    if (input.id != id) {
      // Make inputs not required, hide required star.
      input.required = false;
      required_element.style.visibility = 'hidden';
    } else {
      // Make selected input required, show required star.
      input.required = true;
      required_element.style.visibility = 'visible';
    }
  };
}

/**
 * Triggered upon ticking the wants magazine checkbox.
 * Toggles whether street address/suburb and state/postcode are required and toggles their required element (*).
 */
function change_wantsMagazine() {
  // Get magazine related inputs - street address, suburb/state, postcode.
  let inputs = document.getElementsByClassName('magazine_inputs');

  for (let input of inputs) {
    // The ID of the required element for the input (i.e. "*").
    let required_element = document.getElementById("required_"+input.id);

    // Toggle required attributes and required element (*).
    if (input.required) {
      input.required = false;
      required_element.style.visibility = 'hidden';
    } else {
      input.required = true;
      required_element.style.visibility = 'visible';
    }
  }
}