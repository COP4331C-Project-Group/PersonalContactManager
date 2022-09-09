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

function doLogin() {
  // TODO: remove this once API login is finished
  alert("Login is not implemented yet...");
  return;

  window.userId = 0;
  window.firstName = "";
  window.lastName = "";
  
  let username = document.getElementById("loginUsername").value;
  let password = document.getElementById("loginPassword").value;
  // TODO: switch to using hashes after getting everything working
  // var hash = md5( password );

  document.getElementById("authResult").innerHTML = "";

  let tmp = {username:username, password:password};
  //  var tmp = {username:username, password:hash};
  let jsonPayload = JSON.stringify( tmp );

  let url = window.urlBase + '/Login' + window.extension;

  let xhr = new XMLHttpRequest();
  xhr.open("POST", url, true);
  xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
  try
  {
    xhr.onreadystatechange = function() 
    {
      if (this.readyState == 4 && this.status == 200) 
      {
        let jsonObject = JSON.parse( xhr.responseText );
        window.userId = jsonObject.id;

        if( window.userId < 1 )
        {       
          document.getElementById("authResult").innerHTML = "User/Password combination incorrect";
          return;
        }

        window.firstName = jsonObject.firstName;
        window.lastName = jsonObject.lastName;

        saveCookie();

        window.location.href = "index.html";
      }
    };
    xhr.send(jsonPayload);
  }
  catch(err)
  {
    document.getElementById("authResult").innerHTML = err.message;
  }
}

function doRegister() {
  // TODO: remove this once API register is finished
  alert("Registering is not implemented yet...");
  return;

  window.userId = 0;
  window.firstName = "";
  window.lastName = "";

  // TODO: update this later if needed/add more constraints
  const minimumPasswordLength = 6;
  
  let fname = document.getElementById("registerFirstName").value;
  let lname = document.getElementById("registerLastName").value;
  let username = document.getElementById("registerUsername").value;
  let password = document.getElementById("registerPassword").value;
  let confirmPassword = document.getElementById("registerConfirmPassword").value;

  if (password !== confirmPassword) {
    document.getElementById("authResult").innerHTML = "Passwords must match!";
    return;
  }
  // TODO: add better handling of strong password here
  if (password.length < minimumPasswordLength) {
    document.getElementById("authResult").innerHTML = "Please choose a stronger password (min password length = 6)";
    return;
  }

  // TODO: switch to using hashes after getting everything working
  // var hash = md5( password );

  document.getElementById("authResult").innerHTML = "";

  let tmp = {username:username, password:password};
  //  var tmp = {username:username, password:hash};
  let jsonPayload = JSON.stringify( tmp );

  let url = window.urlBase + '/AddUser' + window.extension;

  let xhr = new XMLHttpRequest();
  xhr.open("POST", url, true);
  xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
  try
  {
    xhr.onreadystatechange = function() 
    {
      if (this.readyState == 4 && this.status == 200) 
      {
        let jsonObject = JSON.parse( xhr.responseText );
        window.userId = jsonObject.id;

        // TODO: add handling here for the cases where username is taken, etc.
        // Ask joey to make it clear what errors are expected.
        if( window.userId < 1 )
        {
          document.getElementById("authResult").innerHTML = "User/Password combination incorrect";
          return;
        }

        window.firstName = jsonObject.firstName;
        window.lastName = jsonObject.lastName;

        saveCookie();

        window.location.href = "index.html";
      }
    };
    xhr.send(jsonPayload);
  }
  catch(err)
  {
    document.getElementById("authResult").innerHTML = err.message;
  }
}
