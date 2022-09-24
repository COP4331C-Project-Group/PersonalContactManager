async function doDeleteContact() {
  if (confirm("Are you sure you want to delete this contact?")) {
    const contact = JSON.parse(localStorage.getItem('individualContact'));
    const [status, responseJson] = await postData(
    window.urlBase + '/contacts/UpdateContact' + window.extension,
    {
      ID:contact.ID,
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

// When the user clicks on the button, open the editContactModal 
openEditContactBtn.onclick = function() {
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
  profileImage = document.getElementById("editProfilePicture");
  if (contact.contactImage == "") {
      profileImage.setAttribute('src', "images/default-profile-pic.jpg");
  } else {
    console.log("setting base 64");
    profileImage.setAttribute('src', "data:image/jpg;base64," + contact.contactImage);
  }
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

// TODO: remove duplication between here and main.js
var profileImage = document.getElementById("editProfilePicture");
profileImage.onclick = function() {
  let upload = document.getElementById("fileUpload");
  console.log(upload);
  upload.click();
}

// TODO: think of how to pass this to doCreateContact in a better way
let imgAsBase64String = "";

window.addEventListener('load', function() {
  document.querySelector('input[type="file"]').addEventListener('change', function() {
    if (this.files && this.files[0]) {
      var reader = new FileReader();

      reader.onload = function () {
        imgAsBase64String = reader.result.replace("data:", "")
        .replace(/^.+,/, "");
        console.log(imgAsBase64String);
      }
      reader.readAsDataURL(this.files[0]);

      profileImage.src = URL.createObjectURL(this.files[0]); // set src to blob url
    }
  });
});

// Get the button that opens the updateProfileModal
var confirmBtn = document.getElementById("confirmBtn");

// When the user clicks the button, open the updateProfileModal 
confirmBtn.onclick = function() {
  if (doUpdateContact()) {
    editContactModal.style.display = "none";
  }
}

async function doUpdateContact() {
  let firstName = document.getElementById("firstName").value;
  let lastName = document.getElementById("lastName").value;
  let phone = document.getElementById("phone").value;
  let email = document.getElementById("email").value;
  const contact = JSON.parse(localStorage.getItem('individualContact'));
  if (imgAsBase64String == "") {
    imgAsBase64String = contact.contactImage;
  }

  const msg = validateContactForm(firstName, lastName, phone, email);
  if (msg !== "") {
    document.getElementById("editError").innerHTML = msg;
    return false;
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
    displayContact();
  } else {
    document.getElementById("editError").innerHTML = responseJson.status_message;
    return false;
  }
  return true;
}

function displayContact() {
  const contact = JSON.parse(localStorage.getItem('individualContact'));
  document.title = "PCM - " + contact.firstName + " " + contact.lastName;
  contactTitle = document.getElementById("title")
  contactTitle.innerHTML = contact.firstName + " " + contact.lastName;
  contactPhone = document.getElementById("displayContactPhoneNumber");
  contactPhone.innerHTML = contact.phone;
  contactEmail = document.getElementById("displayContactEmail");
  contactEmail.innerHTML = contact.email;
  profileImage = document.getElementById("profilePicture");
  if (contact.contactImage == "") {
      profileImage.setAttribute('src', "images/default-profile-pic.jpg");
  } else {
    console.log("setting base 64");
    profileImage.setAttribute('src', "data:image/jpg;base64," + contact.contactImage);
  }
}
