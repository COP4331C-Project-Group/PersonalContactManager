async function doDeleteContact() {
  if (confirm("Are you sure you want to delete this contact?")) {
    const contact = JSON.parse(localStorage.getItem('individualContact'));
    const [status, responseJson] = await deleteData(
    window.urlBase + '/contacts/DeleteContact' + window.extension + '?',
    {
      ID:contact.ID
    });

    if (status == 200) {
      localStorage.setItem('individualContact', null);
    } else {
      document.getElementById("editError").innerHTML = responseJson.status_message;
    }

    document.location.href = "index.html";
  }
}

// Get the editContactModal
var editContactModal = document.getElementById("editContactModal");

// Get the button that opens the editContactModal
var openEditContactBtn = document.getElementById("openEditContactBtn");

// Get the <span> element that closes the editContactModal
var span = document.getElementsByClassName("close")[0];

async function getImage(contactID) {
  const [status, responseJson] = await getData(
    window.urlBase + '/contacts/GetContactImage' + window.extension + "?",
    {
      ID:contactID,
    });

  if (status != 200) {
    console.log("Failed to load image: " + status);
    return null;
  }

  return responseJson.data;
}

// When the user clicks on the button, open the editContactModal 
openEditContactBtn.onclick = async function() {
  editContactModal.style.display = "block";
  const contact = JSON.parse(localStorage.getItem('individualContact'));
  updateFirst = document.getElementById("firstName")
  updateFirst.value = contact.firstName;
  updateLast = document.getElementById("lastName")
  updateLast.value = contact.lastName;
  updatePhone = document.getElementById("phone");
  updatePhone.value = contact.phone;
  updateEmail = document.getElementById("email");
  updateEmail.value = contact.email;
  profileImage = document.getElementById("profilePicture");
  setContactImage(contact);
}

async function setContactImage(contact) {
  if (contact.hasImage) {
    imgAsBase64String = await getImage(contact.ID);
    if (imgAsBase64String !== null)
      profileImage.setAttribute( 'src', "data:image/jpg;base64," + imgAsBase64String);
  }
  else
    profileImage.setAttribute('src', "images/default-profile-pic.jpg");
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
confirmBtn.onclick = async function() {
  const success = await doUpdateContact();
  if (success) {
    editContactModal.style.display = "none";
    displayContact();
  }
}

async function doUpdateContact() {
  let firstName = document.getElementById("firstName").value;
  let lastName = document.getElementById("lastName").value;
  let phone = document.getElementById("phone").value;
  let email = document.getElementById("email").value;
  const contact = JSON.parse(localStorage.getItem('individualContact'));
  
  if (localStorage.getItem('uploadedImgAsBase64String') == null)
    setContactImage(contact);

  if (!validateContactForm(firstName, lastName, phone, email)) {
    return;
  }

  document.getElementById("editError").innerHTML = "";

  const [status, responseJson] = await putData(
    window.urlBase + '/contacts/UpdateContact' + window.extension,
    {
      firstName:firstName,
      lastName:lastName,
      email:email,
      phone:phone,
      userID:window.userID,
      contactImage:imgAsBase64String,
      ID:contact.ID,
    });

  if (status == 200) {
    localStorage.setItem("individualContact", JSON.stringify(responseJson.data));
  } else {
    document.getElementById("authUpdateResult").innerHTML = responseJson.status;
    document.getElementById("authUpdateAlert").style.display = "block";
    return false;
  }
  return true;
}

function formatContactPhoneAsString(phone) {
  console.log(phone);
  return "(" + phone.substring(0, 3) + ") " + phone.substring(3, 6) + "-" + phone.substring(6, 10);
}

async function displayContact() {
  const contact = JSON.parse(localStorage.getItem('individualContact'));
  document.title = "PCM - " + contact.firstName + " " + contact.lastName;
  contactTitle = document.getElementById("title")
  contactTitle.innerHTML = contact.firstName + " " + contact.lastName;
  contactPhone = document.getElementById("displayContactPhoneNumber");
  if (contact.phone !== "") {
    contactPhone.innerHTML = formatContactPhoneAsString(contact.phone);
  } else {
    contactPhone.innerHTML = "N/A"
  }
  contactEmail = document.getElementById("displayContactEmail");
  contactEmail.innerHTML = contact.email;
  if (contact.email !== "") {
    contactEmail.innerHTML = contact.email;
  } else {
    contactEmail.innerHTML = "N/A"
  }
  profileImage = document.getElementById("displayPicture");
  setContactImage(contact);
}
