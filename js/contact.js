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
  document.getElementById("contact-form").innerHTML = `
    <input type="text" id="firstName" class="na" value="` + contact.firstName + `" /><br />
    <input type="text" id="lastName" class="na" value="` + contact.lastName + `" /><br />
    <input type="tel" id="phone" class="na" value="` + contact.phone + `" /><br />
    <input type="text" id="email" class="na" value="` + contact.email + `" /><br />
  `;
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

  const msg = validateContactForm(firstName, lastName, phone, email);
  if (msg !== "") {
    document.getElementById("editError").innerHTML = msg;
    return false;
  }

  document.getElementById("editError").innerHTML = "";

  const [status, responseJson] = await postData(
    window.urlBase + '/contacts/UpdateContact' + window.extension,
    {
      firstName:firstName,
      lastName:lastName,
      email:email,
      phone:phone,
      userID:window.userID,
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
  contactSpan = document.getElementById("contactSpan");
  contactSpan.innerHTML = "<h1>" + contact.firstName + " " + contact.lastName + "</h1><h2>Phone number: " + contact.phone + "<br/>Email: " + contact.email + "</h2>";
}
