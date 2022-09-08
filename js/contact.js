function confirmDeleteContact() {
  if (confirm("Are you sure you want to delete this contact?")) {
    // TODO: Delete contact here
    console.log("Deleting...")
    document.location.href = "index.html";
  }
}

// Get the editContactModal
var editContactModal = document.getElementById("editContactModal");

// Get the button that opens the editContactModal
var openEditContactBtn = document.getElementById("openEditContactBtn");

// Get the <span> element that closes the editContactModal
var span = document.getElementsByClassName("close")[0];

// When the user clicks on the button, open the editContactModal 
openEditContactBtn.onclick = function() {
  editContactModal.style.display = "block";
}

// When the user clicks on <span> (x), close the editContactModal
span.onclick = function() {
  editContactModal.style.display = "none";
}

// When the user clicks anywhere outside of the editContactModal, close it
window.onclick = function(event) {
  if (event.target == editContactModal) {
    editContactModal.style.display = "none";
  }
}

// Get the button that opens the updateProfileModal
var confirmBtn = document.getElementById("confirmBtn");

// When the user clicks the button, open the updateProfileModal 
confirmBtn.onclick = function() {
  // TODO: update this to actually update the contact
  editContactModal.style.display = "none";
}
