<?php
    ini_set('display_errors', 1);
    ini_set('error_reporting', E_ALL);

    require_once __DIR__ . '/../utils/JsonUtils.php';
    require_once __DIR__ . '/UserAPI.php';

    $payload = getRequestInfo();
     
    //connection to database
    $servername = "127.0.0.1";
    $username = "root";
    $password = "MyPassword";
    $dbname = "COP4331";

    // Create connection
    $mysql = new mysqli($servername, $username, $password, $dbname); 	

    if ($payload == null)
        returnWithError("Payload is empty");
    else
    {
        $user = User::Deserialize($payload);

        if ($mysql->connect_error != null)
            returnWithError($mysql->connect_error);
        else
        {
            $userAPI = new UserAPI($mysql);

            $result = $userAPI->GetUserByUsername($user->username);

            if ($result == false)
                returnWithError("User doesn't exist.");
            else
            {
                if (strcmp($result->password, $user->password) != 0)
                    returnWithError("Incorrect password.");
                else
                    sendResultInfoAsJson($result->getJSON());
            }
        }
    }

    function getRequestInfo()
    {
        return json_decode(file_get_contents('php://input'), true);
    }

    function sendResultInfoAsJson( $obj )
    {
        header('Content-type:application/json');
        echo $obj;
    }
    
    function returnWithError( $err )
    {
        $retValue = '{"error":"' . $err . '"}';
        sendResultInfoAsJson( $retValue );
    }
?>
