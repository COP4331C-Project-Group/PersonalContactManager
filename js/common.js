window.urlBase = '/API';
window.extension = '.php';

window.userID = 0;
window.firstName = "";
window.lastName = "";

function saveCookie()
{
  let minutes = 20;
  let date = new Date();
  date.setTime(date.getTime()+(minutes*60*1000));
  document.cookie = "firstName=" + window.firstName + ",lastName=" + window.lastName + ",userId=" + window.userID + ";expires=" + date.toGMTString() + "; SameSite=Lax";
}

function readCookie()
{
  window.userID = -1;
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
      window.userID = parseInt( tokens[1].trim() );
    }
  }

  if( window.userID < 0 )
  {
    if (window.location.pathname.split("/").pop() !== "loginregister.html") {
      window.location.href = "loginregister.html";
    }
  }
  else
  {
    if (window.location.pathname.split("/").pop() !== "index.html") {
      window.location.href = "index.html";
    } else {
      document.getElementById("userName").innerHTML = "Logged in as " + window.firstName + " " + window.lastName;
    }
  }
}
