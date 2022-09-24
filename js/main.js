var isSidebarOpen = false;

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
    console.log("successfully updated");
    updateProfileModal.style.display = "none";
  } else {
    console.log("failed to update");
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

  // TODO: switch to using hashes after getting everything working
  // var passwordHash = md5( oldPassword );
  const [status, responseJson] = await getData(
    window.urlBase + '/users/Login' + window.extension + "?",
    {
      username:window.username,
      password:oldPassword
    });
    
  // Check that all fields are populated
  // TODO extend condition so 
  if (firstName.length == 0 || lastName.length == 0 || username.length == 0 || status == 403 || newPassword === oldPassword || newPassword.length < minimumPasswordLength){
    document.getElementById("authUpdateUsernameResult").innerHTML = "Must provide a username!";
    document.getElementById("authUpdateFirstResult").innerHTML = "Must provide a first name!";
    document.getElementById("authUpdateLastResult").innerHTML = "Must provide a last name!";
    document.getElementById("usernameUpdateAlert").style.display = "block";
    document.getElementById("firstUpdateAlert").style.display = "block";
    document.getElementById("lastUpdateAlert").style.display = "block";

    if (firstName.length != 0) {
      document.getElementById("firstUpdateAlert").style.display = "none";
    }
  
    if (lastName.length != 0) {
      document.getElementById("lastUpdateAlert").style.display = "none";
    }

    // TODO: figure out how to make sure the username doesnt match other users
    if (username.length != 0) {
      document.getElementById("usernameUpdateAlert").style.display = "none";
    }
  
  // TODO: update this and other status checks to use a shared dictionary
  // for status codes to make this more readable
    if (oldPassword.length != 0) {
      if (status == 403){
        document.getElementById("passUpdateAlert").style.display = "block";
        document.getElementById("authUpdatePasswordResult").innerHTML = "Old password invalid for user " + window.username;
      }
      else{
        document.getElementById("passUpdateAlert").style.display = "none";
      }
    }

    if (newPassword.length != 0) {
      if (newPassword.length < minimumPasswordLength) {
        document.getElementById("retypepassUpdateAlert").style.display = "block";
        document.getElementById("authUpdateRetypePasswordResult").innerHTML = "Please choose a stronger password (min password length = 6)";
      }
      else{
        if (newPassword === oldPassword) {
          document.getElementById("retypepassUpdateAlert").style.display = "block";
          document.getElementById("authUpdateRetypePasswordResult").innerHTML = "You cannot enter old password";
        }
        else{
          document.getElementById("passUpdateAlert").style.display = "none";
        }
      }
    }
    return false;
  }
  else{
    return true;
  }
}


async function doUpdateUser() {
  firstName = document.getElementById("updateUserFirstName").value;
  lastName = document.getElementById("updateUserLastName").value;
  username = document.getElementById("updateUsername").value;
  oldPassword = document.getElementById("oldPassword").value;
  newPassword = document.getElementById("newPassword").value;

  let updateSpan = document.getElementById("updateResult");

  if (!validateUpdateUserInfo(firstName, lastName, username, oldPassword, newPassword)) {
    return;
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
    // reload page to reset contact name
    window.location.href = "index.html";
  } else {
    document.getElementById("authUpdateResult").innerHTML = responseJson.status_message;
    document.getElementById("authUpdateAlert").style.display = "block";
    return false;
  }

  return true;
}

var searchContactsButton = document.getElementById("searchContactsButton");
searchContactsButton.addEventListener("click", doSearch);

var contactString = document.getElementById("contactString");
contactString.addEventListener("keydown", function (e) {
  if (e.code === "Enter") {  //checks whether the pressed key is "Enter"
      doSearch();
  }
});

function createContactDiv(contact) {
  // TODO: update this to have nice styling
  return JSON.stringify(contact);
}

function loadContactPage(contactID) {
  const contacts = JSON.parse(localStorage.getItem('cachedContacts'));
  for (const contact of contacts) {
    if (contact.ID === contactID) {
      // console.log("contact: " + JSON.stringify(contact));
      // TODO: see if this gets weird when you have multiple contact pages open.
      localStorage.setItem("individualContact", JSON.stringify(contact));
      window.location.href = "contact.html";
      return;
    }
  }
  document.getElementById("searchError").innerHTML = "Failed to load contact page. :(";
}

async function doSearch() {
  let searchQuery = document.getElementById("contactString").value;
  if (searchQuery.length === 0) {
    document.getElementById("searchError").innerHTML = "Received empty search string. :(";
    return;
  }

  document.getElementById("searchError").innerHTML = "";

  const [status, responseJson] = await getData(
    window.urlBase + '/contacts/SearchContact' + window.extension + "?",
    {
      query:searchQuery,
      userID:window.userID,
      page:0,
      itemsPerPage:100,
    });

  localStorage.setItem("cachedContacts", JSON.stringify(responseJson.data));

  if (status == 200) {
    searchResultDiv = document.getElementById("searchResult");
    searchResultDiv.innerHTML = "Found " + responseJson.data.length + " contacts matching " + searchQuery;
    for ( var contact of responseJson.data ) {
      searchResultDiv.innerHTML += "<br/><a href=javascript:loadContactPage(" + contact.ID + ")>" + createContactDiv(contact) + "</a>";
    }
  } else {
    document.getElementById("searchError").innerHTML = responseJson.status_message;
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
}

async function doCreateContact() {
  let firstName = document.getElementById("firstName").value;
  let lastName = document.getElementById("lastName").value;
  let phone = document.getElementById("phone").value;
  let email = document.getElementById("email").value;

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
      userID:window.userID,
    });
  console.log(status);

  if (status == 201) {
    console.log("Successfully created contact");
    createContactModal.style.display = "none";
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
