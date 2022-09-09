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

function doLogout()
{
  window.userId = 0;
  window.firstName = "";
  window.lastName = "";
  document.cookie = "firstName= ; expires = Thu, 01 Jan 1970 00:00:00 GMT";
  window.location.href = "auth.html";
}

function saveCookie()
{
  let minutes = 20;
  let date = new Date();
  date.setTime(date.getTime()+(minutes*60*1000)); 
  document.cookie = "firstName=" + window.firstName + ",lastName=" + window.lastName + ",userId=" + window.userId + ";expires=" + date.toGMTString();
}

function readCookie()
{
  userId = -1;
  let data = document.cookie;
  let splits = data.split(",");
  for(var i = 0; i < splits.length; i++) 
  {
    let thisOne = splits[i].trim();
    let tokens = thisOne.split("=");
    if( tokens[0] == "firstName" )
    {
      window.firstName = tokens[1];
    }
    else if( tokens[0] == "lastName" )
    {
      window.lastName = tokens[1];
    }
    else if( tokens[0] == "userId" )
    {
      window.userId = parseInt( tokens[1].trim() );
    }
  }
  
  if( window.userId < 0 )
  {
    if (window.location.pathname.split("/").pop() !== "auth.html") {
      window.location.href = "auth.html";
    }
  }
  else
  {
    if (window.location.pathname.split("/").pop() !== "index.html") {
      window.location.href = "index.html";
    }

    document.getElementById("userName").innerHTML = "Logged in as " + window.firstName + " " + window.lastName;
  }
}
