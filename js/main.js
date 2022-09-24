var isSidebarOpen = false;

// Initialize number of contacts to 10
setNumberOfContactsToShow(10);

function toggleNav() {
  if (isSidebarOpen) {
    closeNav();
  } else {
    openNav();
  }
  isSidebarOpen = !isSidebarOpen;
}

function openNav() {
  document.getElementById("mySidenav").style.width = "250px";
  document.getElementById("main").style.marginLeft = "250px";
}

function closeNav() {
  document.getElementById("mySidenav").style.width = "0";
  document.getElementById("main").style.marginLeft= "0";
}

// Get the updateProfileModal
var updateProfileModal = document.getElementById("updateProfileModal");

// Get the <span> element that closes the updateProfileModal
var updateProfileSpan = document.getElementsByClassName("closeUpdateProfile")[0];

// When the user clicks on <span> (x), close the updateProfileModal
updateProfileSpan.onclick = function() {
  updateProfileModal.style.display = "none";
}

// Get the button that confirms profile update
var confirmBtn = document.getElementById("confirmBtn");

// When the user clicks the button, open the updateProfileModal 
confirmBtn.onclick = async function() {
  const success = await doUpdateUser();
  if (success) {
    updateProfileModal.style.display = "none";
  }
}

function openUpdateProfileModal() {
  updateProfileModal.style.display = "block";
  updateUserFirst = document.getElementById("updateUserFirstName")
  updateUserFirst.value = window.firstName;
  updateUserLast = document.getElementById("updateUserLastName")
  updateUserLast.value = window.lastName;
  updateUsername = document.getElementById("updateUsername");
  updateUsername.value = window.username;
}

async function validateUpdateUserInfo(firstName, lastName, username, oldPassword, newPassword) {
  // Check that all fields are populated
  if (firstName.length == 0) {
    return "Must provide a first name!";
  }

  if (lastName.length == 0) {
    return "Must provide a last name!";
  }

  if (username.length == 0) {
    return "Must provide a username!";
  }

  // Try to log in using the old password and old username
  document.getElementById("updateResult").innerHTML = "";

  // TODO: switch to using hashes after getting everything working
  // var passwordHash = md5( oldPassword );
  const [status, responseJson] = await getData(
    window.urlBase + '/users/Login' + window.extension + "?",
    {
      username:window.username,
      password:oldPassword
    });

  // TODO: update this and other status checks to use a shared dictionary
  // for status codes to make this more readable
  if (status == 403) {
    return "Old password invalid for user " + window.username;
  }

  if (newPassword.length == 0) {
    return "Must provide a password!";
  }

  // TODO: add better handling of strong password here
  if (newPassword.length < window.minimumPasswordLength) {
    return "Please choose a stronger password (min password length = 6)";
  }

  return "";
}

async function doUpdateUser() {
  firstName = document.getElementById("updateUserFirstName").value;
  lastName = document.getElementById("updateUserLastName").value;
  username = document.getElementById("updateUsername").value;
  oldPassword = document.getElementById("oldPassword").value;
  newPassword = document.getElementById("newPassword").value;

  let updateSpan = document.getElementById("updateResult");

  const error = await validateUpdateUserInfo(firstName, lastName, username, oldPassword, newPassword);
  if (error !== "") {
    updateSpan.innerHTML = error;
    return false;
  }
  updateSpan.innerHTML = "";

  const [status, responseJson] = await putData(
    window.urlBase + '/users/UpdateUser' + window.extension,
    {
      firstName:firstName,
      lastName:lastName,
      username:username,
      password:newPassword,
      ID:window.userID,
    });

  if (status == 200) {
    saveUserInfo(responseJson.data);
    // reload page to reset contact name
    window.location.href = "index.html";
  } else {
    updateSpan.innerHTML = responseJson.status_message;
    return false;
  }

  return true;
}

var contactString = document.getElementById("contactString");
contactString.addEventListener("keydown", function (e) {
  if (e.code === "Enter") {  //checks whether the pressed key is "Enter"
      doSearch();
  }
});

function capitalizeFirstLetter(string) {
  return string.charAt(0).toUpperCase() + string.slice(1);
}

function createContactDiv(contact) {
  return "<center><h3 style='border:1px solid black; width: 50%;'>" + capitalizeFirstLetter(contact.firstName) + " " + capitalizeFirstLetter(contact.lastName) + "</h6></center>";
}

function loadContactPage(contactID) {
  const contacts = JSON.parse(localStorage.getItem('cachedContacts'));
  for (const contact of contacts) {
    if (contact.ID === contactID) {
      // TODO: see if this gets weird when you have multiple contact pages open.
      localStorage.setItem("individualContact", JSON.stringify(contact));
      window.location.href = "contact.html";
      return;
    }
  }
  document.getElementById("searchError").innerHTML = "Failed to load contact page. :(";
}

function clearSearchResults() {
  searchResultDiv = document.getElementById("searchResult");
  searchResultDiv.innerHTML = "";
  document.getElementById("contactString").value = "";
}

async function doSearch() {
  let toggle = document.getElementById('toggle');
  let displayAll = toggle.checked;
  let searchQuery = document.getElementById("contactString").value;
  if (!displayAll && searchQuery.length === 0) {
    clearSearchResults();
    return;
  }

  let numberOfContacts = localStorage.getItem('numberOfContacts');

  if (window.userID == -1) {
    readCookie();
  }

  document.getElementById("searchError").innerHTML = "";

  const [status, responseJson] = await getData(
    window.urlBase + '/contacts/SearchContact' + window.extension + "?",
    {
      query:searchQuery,
      userID:window.userID,
      page:0,
      itemsPerPage:numberOfContacts,
    });

  console.log(responseJson);
  if (responseJson) {
    localStorage.setItem("cachedContacts", JSON.stringify(responseJson.data));
  }

  if (status == 200) {
    searchResultDiv = document.getElementById("searchResult");
    if (displayAll) {
      searchResultDiv.innerHTML = "Showing all of your contacts";
    } else {
      if (responseJson.data === false) {
        searchResultDiv.innerHTML = "Found no contacts matching " + searchQuery;
        return;
      }
      searchResultDiv.innerHTML = "Showing all contacts matching \"" + searchQuery + "\"";
    }
    searchResultDiv.innerHTML += " (displaying " + numberOfContacts + " at a time)"
    for ( var contact of responseJson.data ) {
      searchResultDiv.innerHTML += "<a href=javascript:loadContactPage(" + contact.ID + ")>" + createContactDiv(contact) + "</a>";
    }
  } else {
    document.getElementById("searchError").innerHTML = status;
  }
}

// Get the createContactModal
var createContactModal = document.getElementById("createContactModal");

// Get the <span> element that closes the createContactModal
var createContactSpan = document.getElementsByClassName("closeCreateContact")[0];

// When the user clicks on <span> (x), close the createContactModal
createContactSpan.onclick = function() {
  createContactModal.style.display = "none";
}

function openCreateContactModal() {
  createContactModal.style.display = "block";
  document.getElementById("firstName").value = "";
  document.getElementById("lastName").value = "";
  document.getElementById("phone").value = "";
  document.getElementById("email").value = "";
}

async function doCreateContact() {
  let firstName = document.getElementById("firstName").value;
  let lastName = document.getElementById("lastName").value;
  let phone = document.getElementById("phone").value;
  let email = document.getElementById("email").value;
  let imgAsBase64String = localStorage.getItem('imgAsBase64String');
  if (imgAsBase64String == null) {
    imgAsBase64String = "";
  }

  const msg = validateContactForm(firstName, lastName, phone, email);
  if (msg !== "") {
    document.getElementById("createResult").innerHTML = msg;
    return;
  }

  document.getElementById("createResult").innerHTML = "";

  const [status, responseJson] = await postData(
    window.urlBase + '/contacts/AddContact' + window.extension,
    {
      firstName:firstName,
      lastName:lastName,
      email:email,
      phone:phone,
      contactImage:imgAsBase64String,
      userID:window.userID,
    });

  if (status == 201) {
    createContactModal.style.display = "none";
  } else {
    document.getElementById("createResult").innerHTML = status;
  }
}

// When the user clicks anywhere outside of the updateProfileModal or createContactModal, close them
window.onclick = function(event) {
  if (event.target == updateProfileModal) {
    updateProfileModal.style.display = "none";
  }

  if (event.target == createContactModal) {
    createContactModal.style.display = "none";
  }
}

let btn = document.getElementById('toggle');
btn.onclick = function() {
  clearSearchResults();
  let searchBox = document.getElementById("contactString");
  let toggleLabel = document.getElementById("toggleLabel");
  if (searchBox.disabled) {
    searchBox.placeholder = "Search Contacts...";
    searchBox.disabled = false;
    searchBox.display='block';
    toggleLabel.innerHTML = "Show all";
  } else {
    searchBox.placeholder = "Displaying all contacts...";
    searchBox.disabled = true;
    searchBox.display = 'none';
    toggleLabel.innerHTML = "Hide all";
  }
  doSearch(searchBox.disabled);
}

function setNumberOfContactsToShow(n) {
  localStorage.setItem('numberOfContacts', n);
  let limitVals = [10, 25, 50];
  function resetStyle(val) {
    var element = document.getElementById('limit' + val);
    element.style.fontWeight = "normal";
  }
  limitVals.forEach(resetStyle);
  var element = document.getElementById('limit' + n);
  element.style.fontWeight = "bold";
  doSearch();
}

window.onload = (event) => {
  let searchBox = document.getElementById("contactString");
  if (document.getElementById('toggle').checked) {
    searchBox.placeholder = "Displaying all contacts...";
    searchBox.disabled = true;
    searchBox.display = 'none';
    toggleLabel.innerHTML = "Hide all";
  }
}
