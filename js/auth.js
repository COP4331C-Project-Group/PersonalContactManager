var confirmPasswordString = document.getElementById("registerConfirmPassword");
confirmPasswordString.addEventListener("keydown", function (e) {
  if (e.code === "Enter") {
    doRegister();
  }
});

var loginPasswordString = document.getElementById("loginPassword");
loginPasswordString.addEventListener("keydown", function (e) {
  if (e.code === "Enter") {
    doLogin();
  }
});

function changeLoginToRegister() {
  document.title = "PCM - Register";
  document.getElementById("registerDiv").style.zIndex = "1";
  document.getElementById("loginDiv").style.zIndex = "-1";
  document.getElementById("title").innerHTML = "Register";
}

function changeRegisterToLogin(){
  document.title = "PCM - Log In";
  document.getElementById("registerDiv").style.zIndex = "-1";
  document.getElementById("loginDiv").style.zIndex = "1";
  document.getElementById("title").innerHTML = "Log In";
}

function saveUserInfo(userJson) {
  window.userID = userJson.ID;

  window.firstName = userJson.firstName;
  window.lastName = userJson.lastName;

  saveCookie();
}

function validateLoginForm(username, password) {
  if (username.length == 0) {
    document.getElementById("authResult").innerHTML = "Must provide a username!";
    return false;
  }

  if (password.length == 0) {
    document.getElementById("authResult").innerHTML = "Must provide a password!";
    return false;
  }

  return true;
}

async function doLogin() {
  window.userID = 0;
  window.firstName = "";
  window.lastName = "";
  
  let username = document.getElementById("loginUsername").value;
  let password = document.getElementById("loginPassword").value;

  if (!validateLoginForm(username, password)) {
    return;
  }

  document.getElementById("authResult").innerHTML = "";

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
  } else {
    document.getElementById("authResult").innerHTML = status;
  }
}

function validateRegistrationForm(firstName, lastName, username, password, confirmPassword) {  
  // TODO: update this later if needed/add more constraints
  const minimumPasswordLength = 6;

  // Check that all fields are populated
  if (firstName.length == 0) {
    document.getElementById("authResult").innerHTML = "Must provide a first name!";
    return false;
  }

  if (lastName.length == 0) {
    document.getElementById("authResult").innerHTML = "Must provide a last name!";
    return false;
  }

  if (username.length == 0) {
    document.getElementById("authResult").innerHTML = "Must provide a username!";
    return false;
  }

  if (password.length == 0) {
    document.getElementById("authResult").innerHTML = "Must provide a password!";
    return false;
  }

  if (confirmPassword.length == 0) {
    document.getElementById("authResult").innerHTML = "Must confirm password!";
    return false;
  }

  // TODO: add better handling of strong password here
  if (password.length < minimumPasswordLength) {
    document.getElementById("authResult").innerHTML = "Please choose a stronger password (min password length = 6)";
    return false;
  }

  if (password !== confirmPassword) {
    document.getElementById("authResult").innerHTML = "Passwords must match!";
    return false;
  }

  return true;
}

async function doRegister() {
  window.userID = 0;
  window.firstName = "";
  window.lastName = "";
  
  let firstName = document.getElementById("registerFirstName").value;
  let lastName = document.getElementById("registerLastName").value;
  let username = document.getElementById("registerUsername").value;
  let password = document.getElementById("registerPassword").value;
  let confirmPassword = document.getElementById("registerConfirmPassword").value;

  if (!validateRegistrationForm(firstName, lastName, username, password, confirmPassword)) {
    return;
  }

  document.getElementById("authResult").innerHTML = "";

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
    document.getElementById("authResult").innerHTML = status;
  }
}
