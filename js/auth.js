function doLogin()
{
    // TODO: remove this once API login is finished
    alert("Login is not implemented yet...");
    return;

    window.userId = 0;
    window.firstName = "";
    window.lastName = "";
    
    let login = document.getElementById("loginUsername").value;
    let password = document.getElementById("loginPassword").value;
    // TODO: switch to using hashes after getting everything working
    // var hash = md5( password );

    document.getElementById("loginResult").innerHTML = "";

    let tmp = {login:login, password:password};
    //  var tmp = {login:login, password:hash};
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
    alert("Registering is not implemented yet...");
}
