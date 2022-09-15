// TODO: abstract shared code (e.g. request sending); can pass a lambda

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

async function doLogin() {
  window.userID = 0;
  window.firstName = "";
  window.lastName = "";
  
  let username = document.getElementById("loginUsername").value;
  let password = document.getElementById("loginPassword").value;

  // Check that all fields are populated
  if (username.length == 0) {
    document.getElementById("authResult").innerHTML = "Must provide a username!";
    return;
  }

  if (password.length == 0) {
    document.getElementById("authResult").innerHTML = "Must provide a password!";
    return;
  }
  document.getElementById("authResult").innerHTML = "";

  let tmp = {username:username, password:password};
  // TODO: switch to using hashes after getting everything working
  // var hash = md5( password );
  // var tmp = {username:username, password:hash};
  let jsonPayload = JSON.stringify( tmp );

  var url = window.urlBase + '/users/Login' + window.extension + "?";
  for ( var key in tmp ) {
    url += key + "=" + tmp[key] + "&";
  }

  let response = await fetch(url);
  let responseJson = await response.json();
  if (response.status == 200) {
    window.userID = responseJson.data.userID;

    window.firstName = responseJson.data.firstName;
    window.lastName = responseJson.data.lastName;

    saveCookie();

    window.location.href = "index.html";
  } else {
    document.getElementById("authResult").innerHTML = responseJson.status_message;
  }
}

async function doRegister() {
  window.userID = 0;
  window.firstName = "";
  window.lastName = "";

  // TODO: update this later if needed/add more constraints
  const minimumPasswordLength = 6;
  
  let firstName = document.getElementById("registerFirstName").value;
  let lastName = document.getElementById("registerLastName").value;
  let username = document.getElementById("registerUsername").value;
  let password = document.getElementById("registerPassword").value;
  let confirmPassword = document.getElementById("registerConfirmPassword").value;

  // Check that all fields are populated
  if (firstName.length == 0) {
    document.getElementById("authResult").innerHTML = "Must provide a first name!";
    return;
  }

  if (lastName.length == 0) {
    document.getElementById("authResult").innerHTML = "Must provide a last name!";
    return;
  }

  if (username.length == 0) {
    document.getElementById("authResult").innerHTML = "Must provide a username!";
    return;
  }

  if (password.length == 0) {
    document.getElementById("authResult").innerHTML = "Must provide a password!";
    return;
  }

  if (confirmPassword.length == 0) {
    document.getElementById("authResult").innerHTML = "Must confirm password!";
    return;
  }

  // TODO: add better handling of strong password here
  if (password.length < minimumPasswordLength) {
    document.getElementById("authResult").innerHTML = "Please choose a stronger password (min password length = 6)";
    return;
  }

  if (password !== confirmPassword) {
    document.getElementById("authResult").innerHTML = "Passwords must match!";
    return;
  }

  document.getElementById("authResult").innerHTML = "";

  let tmp = {
    firstName:firstName,
    lastName:lastName,
    username:username,
    password:password
  };
  // TODO: switch to using hashes after getting everything working
  // var hash = md5( password );
  // var tmp = {firstName:firstName, lastName:lastName, username:username, password:hash};
  let jsonPayload = JSON.stringify( tmp );

  let url = window.urlBase + '/users/Register' + window.extension;

  var formData = new FormData()
  for ( var key in tmp ) {
    formData.append(key, tmp[key]);
  }
  const requestOptions = {
          method: 'POST',
          headers: {
              'Accept': 'application/json'
          },
          body: formData
      };

  let response = await fetch(url, requestOptions);
  let responseJson = await response.json();
  if (response.status == 201) {
    window.userID = responseJson.data.userID;

    if( window.userID < 1 )
    {
      document.getElementById("authResult").innerHTML = "User/Password combination incorrect";
      return;
    }

    window.firstName = responseJson.data.firstName;
    window.lastName = responseJson.data.lastName;

    saveCookie();

    window.location.href = "index.html";
  } else {
    document.getElementById("authResult").innerHTML = responseJson.status_message;
  }
}
