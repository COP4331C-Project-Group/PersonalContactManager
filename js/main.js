var isSidebarOpen = false;
let currentPage = 0;

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
  // TODO: update this later if needed/add more constraints
  const minimumPasswordLength = 6;

  let isValid = true;

  if (username.length == 0) {
    document.getElementById("authUpdateUsernameResult").innerHTML = "Must provide a username!";
    document.getElementById("usernameUpdateAlert").style.display = "block";
    isValid = false;
  } else {
    document.getElementById("usernameUpdateAlert").style.display = "none";
  }

  if (firstName.length == 0) {
    document.getElementById("authUpdateFirstResult").innerHTML = "Must provide a first name!";
    document.getElementById("firstUpdateAlert").style.display = "block";
    isValid = false;
  } else {
    document.getElementById("firstUpdateAlert").style.display = "none";    
  }

  if (lastName.length == 0) {
    document.getElementById("authUpdateLastResult").innerHTML = "Must provide a last name!";
    document.getElementById("lastUpdateAlert").style.display = "block";
    isValid = false;
  } else {
    document.getElementById("lastUpdateAlert").style.display = "none";
  }

  if (oldPassword.length == 0) {
    document.getElementById("authUpdatePasswordResult").innerHTML = "Must provide a password!";
    document.getElementById("passUpdateAlert").style.display = "block";
    isValid = false;
  } else if (oldPassword.length < minimumPasswordLength) {
    document.getElementById("authUpdatePasswordResult").innerHTML = "Please choose a stronger password (min password length = 6)";
    document.getElementById("passUpdateAlert").style.display = "block";
    isValid = false;
  } else {
    document.getElementById("passUpdateAlert").style.display = "none";
  }

  if (newPassword.length == 0) {
    document.getElementById("authUpdateRetypePasswordResult").innerHTML = "Must retype password!";
    document.getElementById("retypepassUpdateAlert").style.display = "block";
    isValid = false;
  } else if (newPassword.length < minimumPasswordLength) {
    document.getElementById("retypepassUpdateAlert").style.display = "block";
    document.getElementById("authUpdateRetypePasswordResult").innerHTML = "Please choose a stronger password (min password length = 6)";
    isValid = false;
  } else {
    document.getElementById("retypepassUpdateAlert").style.display = "none";
  }

  // TODO: switch to using hashes after getting everything working
  // var passwordHash = md5( oldPassword );
  const [status, responseJson] = await getData(
    window.urlBase + '/users/Login' + window.extension + "?",
    {
      username:window.username,
      password:oldPassword
    });

  if (status == 403 || status == 404) {
    document.getElementById("passUpdateAlert").style.display = "block";
    document.getElementById("authUpdatePasswordResult").innerHTML = "Old password invalid for user " + window.username;
    isValid = false;
  } else {
    document.getElementById("passUpdateAlert").style.display = "none";
  }

  return isValid;
}

async function doUpdateUser() {
  firstName = document.getElementById("updateUserFirstName").value;
  lastName = document.getElementById("updateUserLastName").value;
  username = document.getElementById("updateUsername").value;
  oldPassword = document.getElementById("oldPassword").value;
  newPassword = document.getElementById("newPassword").value;

  let updateSpan = document.getElementById("updateResult");

  let success = await validateUpdateUserInfo(firstName, lastName, username, oldPassword, newPassword);
  if (success === false) {
    return false;
  }

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
    saveCookie();
    // reload page to reset contact name
    window.location.href = "index.html";
  } else {
    document.getElementById("authUpdateResult").innerHTML = responseJson.status_message;
    document.getElementById("authUpdateAlert").style.display = "block";
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

function createContactDiv(contact) {
  return "<center><h3 class='individualDivs'>" + capitalizeFirstLetter(contact.firstName) + " " + capitalizeFirstLetter(contact.lastName) + "</h6></center>";
}

function loadContactPage(contactID) {
  const contacts = JSON.parse(localStorage.getItem('cachedContacts'));
  for (const contact of contacts) {
    if (contact.ID === contactID) {
      localStorage.setItem("individualContact", JSON.stringify(contact));
      window.location.href = "contact.html";
      return;
    }
  }
  document.getElementById("searchError").innerHTML = "Failed to load contact page. :(";
}

function clearSearchResults() {
  document.getElementById("searchResult").innerHTML = "";
  document.getElementById("searchError").innerHTML = "";
  document.getElementById("contactString").value = "";
  currentPage = 0;
  localStorage.setItem("cachedContacts", null);
}

async function doSearch(page = 0) {
  let toggle = document.getElementById('toggle');
  let displayAll = toggle.checked;
  let searchQuery = document.getElementById("contactString").value;
  if (!displayAll && searchQuery.length === 0) {
    clearSearchResults();
    return;
  }

  searchResultDiv = document.getElementById("searchResult");
  if (page == 0) {
    searchResultDiv.innerHTML = "";
    localStorage.removeItem('cachedContacts');
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
      page:page,
      itemsPerPage:numberOfContacts,
    });

  let cached = localStorage.getItem("cachedContacts");
  if (responseJson.data) {
    cached = (cached === null) ? responseJson.data : JSON.parse(cached).concat(responseJson.data);
    localStorage.setItem("cachedContacts", JSON.stringify(cached));
  } else {
    document.getElementById("searchError").innerHTML = "No contacts found";
    return;
  }

  let caption = document.getElementById("searchError");

  if (status == 200) {
    searchResultDiv = document.getElementById("searchResult");
    if (displayAll) {
      caption.innerHTML = "Showing all of your contacts";
    } else {
      if (responseJson.data === false) {
        caption.innerHTML = "Found no contacts matching " + searchQuery;
        return;
      }
      caption.innerHTML = "Showing contacts matching \"" + searchQuery + "\"";
    }
    caption.innerHTML += " (loading " + numberOfContacts + " at a time)"
    for ( var contact of responseJson.data ) {
      searchResultDiv.innerHTML += "<div class=\"hoverable\"><a href=javascript:loadContactPage(" + contact.ID + ")>" + createContactDiv(contact) + "</a></div>";
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

  if (!validateContactForm(firstName, lastName, phone, email)) {
    return;
  }

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
    localStorage.removeItem('imgAsBase64String');
    createContactModal.style.display = "none";
    doSearch();
  } else {
    document.getElementById("authContactResult").innerHTML = responseJson.status_message;
    document.getElementById("authContactAlert").style.display = "block";
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
  let searchBox = document.getElementById("contactString");
  let toggleLabel = document.getElementById("toggleLabel");
  let clearButton = document.getElementById("clearSearchButton");
  if (searchBox.disabled) {
    searchBox.placeholder = "Search Contacts...";
    searchBox.disabled = false;
    searchBox.display='block';
    clearButton.disabled = false;
    toggleLabel.innerHTML = "Show all";
  } else {
    clearSearchResults();
    searchBox.placeholder = "Displaying all contacts...";
    searchBox.disabled = true;
    searchBox.display = 'none';
    clearButton.disabled = true;
    toggleLabel.innerHTML = "Hide all";
  }
  doSearch();
}

function setNumberOfContactsToShow(n) {
  localStorage.setItem('numberOfContacts', n);
  var element = document.getElementById('limit' + n);
  if (element !== null && element.style.fontWeight == 'bold') {
    return;
  }  
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
  let clearButton = document.getElementById("clearSearchButton");
  if (document.getElementById('toggle').checked) {
    searchBox.placeholder = "Displaying all contacts...";
    searchBox.disabled = true;
    clearButton.disabled = true;
    searchBox.display = 'none';
    toggleLabel.innerHTML = "Hide all";
  }
}

let cont = document.querySelector(".container");

cont.addEventListener("scroll", () => {
  if (cont.scrollTop >= (cont.scrollHeight - cont.clientHeight) * 0.9) {
    currentPage += 1;
    doSearch(currentPage);
  }
});
