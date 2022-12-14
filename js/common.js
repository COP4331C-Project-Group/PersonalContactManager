window.urlBase = '/API';
window.extension = '.php';

window.userID = -1;
window.firstName = "";
window.lastName = "";
window.username = "";

// TODO: update this later if needed/add more constraints
window.minimumPasswordLength = 6;
window.imageSizeLimit = 2000000;

function capitalizeFirstLetter(string) {
  return string.charAt(0).toUpperCase() + string.slice(1);
}

function saveCookie()
{
  let minutes = 20;
  let date = new Date();
  date.setTime(date.getTime()+(minutes*60*1000));
  document.cookie = "firstName=" + window.firstName + ",lastName=" + window.lastName + ",userId=" + window.userID + ",username=" + window.username + ";expires=" + date.toGMTString() + "; SameSite=Lax";
}

function readCookie()
{
  let data = document.cookie;
  // Sometimes prefix is added to cookie, can get rid of it by splitting on ;
  let data_no_prefix = data.split(";");
  data = data_no_prefix[(data_no_prefix.length === 2) ? 1 : 0];
  let parts = data.split(",");
  // parts has multiple elements if cookie is set, so we retrieve log in info
  if (parts.length > 1) {
    for(var i = 0; i < parts.length; i++)
    {
      let thisOne = parts[i].trim();
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
        window.userID = parseInt( tokens[1].trim() );
      }
      else if( tokens[0] == "username" )
      {
        window.username = tokens[1].trim();
      }
    }
  }

  page = window.location.pathname.split("/").pop();
  if( window.userID < 0 ) {
    if (page !== "auth.html" && page !== "landing.html") {
      window.location.href = "landing.html";
    }
  } else {
    if (page == "auth.html") {
      window.location.href = "index.html";
    } else if (page == "index.html") {
      document.getElementById("title").innerHTML = capitalizeFirstLetter(window.firstName) + "'s Contacts";
      document.getElementById("usernameSideNav").innerHTML = "&emsp;" + window.username;
    }
  }
}

function saveUserInfo(userJson) {
  window.userID = userJson.ID;
  window.firstName = userJson.firstName;
  window.lastName = userJson.lastName;
  window.username = userJson.username;
}

async function getData(url, jsonParams) {
  for ( var key in jsonParams ) {
    url += key + "=" + jsonParams[key] + "&";
  }

  let response = await fetch(url);
  if (!response.ok) {
    console.log(JSON.stringify(response))
    return [response.status, null];
  }
  let responseJson = await response.json();
  return [response.status, responseJson];
}

async function deleteData(url, jsonParams) {
  for ( var key in jsonParams ) {
    url += key + "=" + jsonParams[key] + "&";
  }
  
  // removes the last &
  url = url.slice(0, -1);

  const requestOptions = {
    method: 'DELETE'
  };

  let response = await fetch(url, requestOptions);
  if (!response.ok) {
    console.log(JSON.stringify(response));
    return [response.status, null];
  }

  let responseJson = await response.json();
  return [response.status, responseJson];
}

async function postData(url, jsonParams) {
  var formData = new FormData()
  for ( var key in jsonParams ) {
    formData.append(key, jsonParams[key]);
  }
  const requestOptions = {
          method: 'POST',
          headers: {
              'Accept': 'application/json'
          },
          body: formData
      };

  let response = await fetch(url, requestOptions);
  if (!response.ok) {
    console.log(JSON.stringify(response));
    return [response.status, null];
  }
  let responseJson = await response.json();
  return [response.status, responseJson];
}

async function putData(url, jsonParams) {
  const requestOptions = {
          method: 'PUT',
          headers: {
              'Content-Type': 'application/json'
          },
          body: JSON.stringify(jsonParams)
      };

  let response = await fetch(url, requestOptions);
  if (!response.ok) {
    console.log(JSON.stringify(response));
    return [response.status, null];
  }
  let responseJson = await response.json();
  return [response.status, responseJson];
}

// Taken from SO: https://stackoverflow.com/a/9204568
function isValidEmail(email) {
  var re = /\S+@\S+\.\S+/;
  return re.test(email);
};

// Taken from https://www.w3resource.com/javascript/form/phone-no-validation.php
// Expects phone numbers of the form
// XXX-XXX-XXXX
// XXX.XXX.XXXX
// XXX XXX XXXX
function isValidPhoneNumber(phone) {
  if (phone.match(/^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/)) {
    return true;
  } else {
    return false;
  }
  return;
}

function validateContactForm(firstName, lastName, phone, email) {
  let isValid = true;
  if (firstName.length === 0 || lastName.length === 0 || phone.length === 0 || email.length === 0 || (phone.length !== 0 && !isValidPhoneNumber(phone, "us")) || (email.length !== 0 && !isValidEmail(email))){
    document.getElementById("authContactPhoneResult").innerHTML = "Must provide a phone number!";
    document.getElementById("authContactEmailResult").innerHTML = "Must provide a email address!";
    document.getElementById("authContactFirstResult").innerHTML = "Must provide a first name!";
    document.getElementById("authContactLastResult").innerHTML = "Must provide a last name!";
    document.getElementById("phoneContactAlert").style.display = "block";
    document.getElementById("firstContactAlert").style.display = "block";
    document.getElementById("lastContactAlert").style.display = "block";
    document.getElementById("emailContactAlert").style.display = "block";

    if (firstName.length !== 0) {
      document.getElementById("firstContactAlert").style.display = "none";
    } else {
      isValid = false;
    }
  
    if (lastName.length !== 0) {
      document.getElementById("lastContactAlert").style.display = "none";
    } else {
      isValid = false;
    }
  
    // If phone number is nonempty, make sure it is valid
    if (phone.length !== 0 && !isValidPhoneNumber(phone, "us")) {
      document.getElementById("authContactPhoneResult").innerHTML = "Must provide a valid us-based phone number with format: " + "XXX-XXX-XXXX or XXX.XXX.XXXX or XXX XXX XXXX";
      isValid = false;
    } else {
      document.getElementById("phoneContactAlert").style.display = "none";
    }
  
    // If email is nonempty, make sure it is valid
    if (email.length !== 0 && !isValidEmail(email)) {
      document.getElementById("authContactEmailResult").innerHTML = "Must provide a valid email, in the format user@domain.extension.";
      isValid = false;
    } else {
      document.getElementById("emailContactAlert").style.display = "none";
    }
  }
  return isValid;
}
