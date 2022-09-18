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

// Get the button that opens the updateProfileModal
var confirmBtn = document.getElementById("confirmBtn");

// When the user clicks the button, open the updateProfileModal 
confirmBtn.onclick = function() {
  updateProfileModal.style.display = "none";
}

// When the user clicks anywhere outside of the updateProfileModal, close it
window.onclick = function(event) {
  if (event.target == updateProfileModal) {
    updateProfileModal.style.display = "none";
  }
}

function openUpdateProfileModal() {
  updateProfileModal.style.display = "block";
}

var searchContactsButton = document.getElementById("searchContactsButton");
searchContactsButton.addEventListener("click", doSearch);

var contactString = document.getElementById("contactString");
contactString.addEventListener("keydown", function (e) {
  if (e.code === "Enter") {  //checks whether the pressed key is "Enter"
      doSearch();
  }
});

async function doSearch() {
  let searchQuery = document.getElementById("contactString").value;
  if (searchQuery.length === 0) {
    document.getElementById("searchResult").innerHTML = "Received empty search string. :(";
    return;
  }

  document.getElementById("searchResult").innerHTML = "";

  // TODO: consider updating endpoint to take a single query string instead of
  // sending all fields populated with same value.
  const [status, responseJson] = await getData(
    window.urlBase + '/contacts/SearchContact' + window.extension + "?",
    {
      firstName:searchQuery,
      lastName:searchQuery,
      email:searchQuery,
      phone:searchQuery,
      userID:window.userID,
      limit:100,
    });

  if (status == 200) {
    document.getElementById("searchResult").innerHTML = "Found " + responseJson.data.length + " contacts matching " + searchQuery;
    // TODO: update display to show contacts using responseJson.data
    console.log(JSON.stringify(responseJson.data))
  } else {
    document.getElementById("searchResult").innerHTML = responseJson.status_message;
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

// Get the button that opens the createContactModal
var createBtn = document.getElementById("createBtn");

// When the user clicks the button, open the createContactModal 
createBtn.onclick = function() {
  createContactModal.style.display = "none";
}

function openCreateContactModal() {
  createContactModal.style.display = "block";
}
