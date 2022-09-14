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
  //updateProfileModal.style.display = "block";
  window.location.href="updateProfile.html"
}

var searchContactsBtn = document.getElementById("searchContactsBtn");
searchContactsBtn.addEventListener("click", searchContacts);

var contactString = document.getElementById("contactString");
contactString.addEventListener("keydown", function (e) {
  if (e.code === "Enter") {  //checks whether the pressed key is "Enter"
      searchContacts(e.target.value);
  }
});

function searchContacts(text) {
  // TODO: implement this function
  alert("Searching contacts is not yet implemented...");
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
