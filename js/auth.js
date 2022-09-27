var confirmPasswordString = document.getElementById("registerConfirmPassword");
confirmPasswordString.addEventListener("keydown", function (e) {
  if (e.code === "Enter") {
    doRegister();
  }
});

function addHideAlertOnInputListener(formLabelID, alertID) {
  if (formLabelID.value != "" && document.getElementById(alertID).style.display != "none")
    document.getElementById(alertID).style.display = "none";
}

document.getElementById("loginUsername").addEventListener("input", function() { 
  addHideAlertOnInputListener("loginUsername", "usernameLoginAlert");
  addHideAlertOnInputListener("loginUsername", "authLoginAlert"); 
});

document.getElementById("loginPassword").addEventListener("input", function() { 
  addHideAlertOnInputListener("loginPassword", "passLoginAlert");
  addHideAlertOnInputListener("loginUsername", "authLoginAlert"); 
});

var loginPasswordString = document.getElementById("loginPassword");
loginPasswordString.addEventListener("keydown", function (e) {
  if (e.code === "Enter") {
    let username = document.getElementById("loginUsername").value;
    let password = document.getElementById("loginPassword").value;
    doLogin(username, password);
  }
});

// Get the button that confirms profile update
var loginBtn = document.getElementById("loginButton");

// When the user clicks the button, open the updateProfileModal 
loginBtn.onclick = function() {
  let username = document.getElementById("loginUsername").value;
  let password = document.getElementById("loginPassword").value;
  doLogin(username, password);
}

function changeLoginToRegister() {
  document.title = "PCM - Register";
  document.getElementById("registerForm").style.left="0px"
  document.getElementById("registerForm").style.opacity="1";
  document.getElementById("loginForm").style.left = "1800px";
  document.getElementById("loginForm").style.opacity = "0";

  document.getElementById("title").innerHTML = "Register";
}

function changeRegisterToLogin(){
  document.title = "PCM - Log In";
  document.getElementById("registerForm").style.left="-1800px"
  document.getElementById("registerForm").style.opacity="0";
  document.getElementById("loginForm").style.left = "0px";
  document.getElementById("loginForm").style.opacity = "1";

  document.getElementById("")


  document.getElementById("title").innerHTML = "Log In";
}

function validateLoginForm(username, password) {
  let isValid = true;
  if (username.length == 0) {
    document.getElementById("authLoginUsernameResult").innerHTML = "Must provide a username!";
    document.getElementById("usernameLoginAlert").style.display = "block";
    isValid = false;
  } else {
    document.getElementById("usernameLoginAlert").style.display = "none";
  }
  if (password.length == 0) {
    document.getElementById("authLoginPasswordResult").innerHTML = "Must provide a password!";
    document.getElementById("passLoginAlert").style.display = "block";
    isValid = false;
  } else {
    document.getElementById("passLoginAlert").style.display = "none";
  }
  return isValid;
}

async function doLogin(username, password) {
  window.userID = -1;
  window.firstName = "";
  window.lastName = "";
  window.username = "";

  if (!validateLoginForm(username, password)) {
    return;
  }

  // TODO: switch to using hashes after getting everything working
  // var passwordHash = md5( password );

  const [status, responseJson] = await getData(
    window.urlBase + '/users/Login' + window.extension + "?",
    {
      username:username,
      password:password
    });

  if (status == 200) {
    saveUserInfo(responseJson.data);
    window.location.href = "index.html";
  } else if (status == 404 || status == 403) {
    document.getElementById("authLoginResult").innerHTML = "Bad username/password combo!";
    document.getElementById("authLoginAlert").style.display = "block";
  } else {
    document.getElementById("authLoginResult").innerHTML = status;
    document.getElementById("authLoginAlert").style.display = "block";
  }
}

document.getElementById("registerUsername").addEventListener("input", function() { 
  addHideAlertOnInputListener("registerUsername", "usernameRegisterAlert");
  addHideAlertOnInputListener("registerUsername", "authRegisterAlert");
});
document.getElementById("registerFirstName").addEventListener("input", function() { addHideAlertOnInputListener("registerFirstName", "firstRegisterAlert"); });
document.getElementById("registerLastName").addEventListener("input", function() { addHideAlertOnInputListener("registerLastName", "lastRegisterAlert"); });
document.getElementById("registerPassword").addEventListener("input", function() { addHideAlertOnInputListener("registerPassword ", "passRegisterAlert"); });
document.getElementById("registerConfirmPassword").addEventListener("input", function() { addHideAlertOnInputListener("registerConfirmPassword", "retypepassRegisterAlert"); });

function validateRegistrationForm(firstName, lastName, username, password, confirmPassword) {  
  // TODO: update this later if needed/add more constraints
  const minimumPasswordLength = 6;

  let isValid = true;

  if (username.length == 0) {
    document.getElementById("authRegisterUsernameResult").innerHTML = "Must provide a username!";
    document.getElementById("usernameRegisterAlert").style.display = "block";
    isValid = false;
  } else {
    document.getElementById("usernameRegisterAlert").style.display = "none";
  }

  if (firstName.length == 0) {
    document.getElementById("authRegisterFirstResult").innerHTML = "Must provide a first name!";
    document.getElementById("firstRegisterAlert").style.display = "block";
    isValid = false;
  } else {
    document.getElementById("firstRegisterAlert").style.display = "none";    
  }

  if (lastName.length == 0) {
    document.getElementById("authRegisterLastResult").innerHTML = "Must provide a last name!";
    document.getElementById("lastRegisterAlert").style.display = "block";
    isValid = false;
  } else {
    document.getElementById("lastRegisterAlert").style.display = "none";
  }

  if (password.length == 0) {
    document.getElementById("authRegisterPasswordResult").innerHTML = "Must provide a password!";
    document.getElementById("passRegisterAlert").style.display = "block";
    isValid = false;
  } else if (password.length < minimumPasswordLength) {
    document.getElementById("authRegisterPasswordResult").innerHTML = "Please choose a stronger password (min password length = 6)";
    document.getElementById("passRegisterAlert").style.display = "block";
    isValid = false;
  } else {
    document.getElementById("passRegisterAlert").style.display = "none";
  }

  if (confirmPassword.length == 0) {
    document.getElementById("authRegisterRetypePasswordResult").innerHTML = "Must retype password!";
    document.getElementById("retypepassRegisterAlert").style.display = "block";
    isValid = false;
  } else if (password !== confirmPassword) {
    document.getElementById("authRegisterRetypePasswordResult").innerHTML = "Must match password!";
    document.getElementById("retypepassRegisterAlert").style.display = "block";
    isValid = false;
  } else {
    document.getElementById("retypepassRegisterAlert").style.display = "none";
  }

  return isValid;
}

async function doRegister() {
  window.userID = -1;
  window.firstName = "";
  window.lastName = "";
  window.username = "";
  
  let firstName = document.getElementById("registerFirstName").value;
  let lastName = document.getElementById("registerLastName").value;
  let username = document.getElementById("registerUsername").value;
  let password = document.getElementById("registerPassword").value;
  let confirmPassword = document.getElementById("registerConfirmPassword").value;

  if (!validateRegistrationForm(firstName, lastName, username, password, confirmPassword)) {
    return;
  }

  // TODO: switch to using hashes after getting everything working
  // var passwordHash = md5( password );

  const [status, responseJson] = await postData(
    window.urlBase + '/users/Register' + window.extension,
    {
      firstName:firstName,
      lastName:lastName,
      username:username,
      password:password
    });

  if (status == 201) {
    saveUserInfo(responseJson.data);
    window.location.href = "index.html";
  } else {
    document.getElementById("authRegisterResult").innerHTML = status;
    document.getElementById("authRegisterAlert").style.display = "block";
  }
}
