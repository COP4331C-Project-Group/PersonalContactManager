var confirmPasswordString = document.getElementById("registerConfirmPassword");
confirmPasswordString.addEventListener("keydown", function (e) {
  if (e.code === "Enter") {
    doRegister();
  }
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

function validateLoginForm(username, password) {
  if ((username.length == 0) && (password.length == 0)) {
    document.getElementById("authLoginUsernameResult").innerHTML = "Must provide a username!";
    document.getElementById("authLoginPasswordResult").innerHTML = "Must provide a password!";
    document.getElementById("usernameLoginAlert").style.display = "block";
    document.getElementById("passLoginAlert").style.display = "block";
    return false;
  }
  if (username.length == 0) {
    document.getElementById("authLoginUsernameResult").innerHTML = "Must provide a username!";
    document.getElementById("usernameLoginLoginAlert").style.display = "block";
    document.getElementById("passLoginAlert").style.display = "none";
    return false;
  }
  if (password.length == 0) {
    document.getElementById("authLoginPasswordResult").innerHTML = "Must provide a username!";
    document.getElementById("usernameLoginAlert").style.display = "none";
    document.getElementById("passLoginAlert").style.display = "block";
    return false;
  }
  return true;
}

async function doLogin(username, password) {
  window.userID = 0;
  window.firstName = "";
  window.lastName = "";
  window.username = "";

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
  if (firstName.length == 0 || lastName.length == 0 || username.length == 0 || password.length == 0 || confirmPassword.length == 0 || password.length < minimumPasswordLength || password !== confirmPassword){
    document.getElementById("authRegisterUsernameResult").innerHTML = "Must provide a username!";
    document.getElementById("authRegisterPasswordResult").innerHTML = "Must provide a password!";
    document.getElementById("authRegisterRetypePasswordResult").innerHTML = "Must retype password!";
    document.getElementById("authRegisterFirstResult").innerHTML = "Must provide a first name!";
    document.getElementById("authRegisterLastResult").innerHTML = "Must provide a last name!";
    document.getElementById("usernameRegisterAlert").style.display = "block";
    document.getElementById("firstRegisterAlert").style.display = "block";
    document.getElementById("lastRegisterAlert").style.display = "block";
    document.getElementById("passRegisterAlert").style.display = "block";
    document.getElementById("retypepassRegisterAlert").style.display = "block";

    if (firstName.length != 0) {
      document.getElementById("firstRegisterAlert").style.display = "none";
    }
  
    if (lastName.length != 0) {
      document.getElementById("lastRegisterAlert").style.display = "none";
    }
  
    if (username.length != 0) {
      document.getElementById("usernameRegisterAlert").style.display = "none";
    }
  
    if (password.length != 0) {
      if (password.length < minimumPasswordLength) {
        document.getElementById("passRegisterAlert").style.display = "block";
        document.getElementById("authRegisterPasswordResult").innerHTML = "Please choose a stronger password (min password length = 6)";
      }
      else{
        document.getElementById("passRegisterAlert").style.display = "none";
      }
    }
  
    if (confirmPassword.length != 0) {
      if (password !== confirmPassword) {
        document.getElementById("authRegisterRetypePasswordResult").innerHTML = "Must match password!";
      }
      else{
        document.getElementById("retypepassRegisterAlert").style.display = "none";
      }
    }

    else if (confirmPassword.length == 0) {
      document.getElementById("authRegisterRetypePasswordResult").innerHTML = "Must match password!";
    }
    return false;
  }
  else{
    document.getElementById("retypepassRegisterAlert").style.display = "none";
    return true;
  }

  return true;
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
