function doLogout()
{
  window.userID = 0;
  window.firstName = "";
  window.lastName = "";
  document.cookie = "firstName= ; expires = Thu, 01 Jan 1970 00:00:00 GMT; SameSite=Lax";
  window.location.href = "auth.html";
}

